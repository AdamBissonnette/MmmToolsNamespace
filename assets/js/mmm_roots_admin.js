var activeUpload = null;
var storeSendToEditor = null;
var newSendToEditor = null;

function SaveOptions(form)
{
	var formdata = jQuery(form).serializeArray()
	jQuery.post ('admin-ajax.php',
		 { 'action':'do_ajax', 'fn':'settings', 'count':10, settings:formdata },
		  function(data){FinalizeOptions(data)},
		   "json");
}

function FinalizeOptions(data)
{
	if (data == 0)
	{
		ShowModal('Settings Saved!', '<p>The settings have been saved!</p>');
	}
	else
	{
		ShowModal('Oops!', '<p>Something unexpected happened... settings might have been saved... open this page in a second window and see?</p>');
	}
}

function SetupUploadControls()
{
	storeSendToEditor = window.send_to_editor;

	newSendToEditor = function(html) {
	 imgurl = jQuery('img',html).attr('src');
	 jQuery(activeUpload).val(imgurl);
	 tb_remove();
	 window.send_to_editor = storeSendToEditor;
	}

	jQuery('.image_uploader a').click(function(event) {
		window.send_to_editor = newSendToEditor;
		activeUpload = jQuery(event.target).prev();

		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});
}

function SetupSelects()
{
	jQuery(".mmm-select").select2();
	jQuery(".mmm-select-multi").select2Sortable(
		{bindOrder: 'sortableStop'}
    );
}


function SetupSaveEvents()
{
	UpdateTextFieldFromSelect();

	jQuery(document.forms).bind("submit", function() {
		BindTextUpdateOnSubmit();
	});
}

function BindTextUpdateOnSubmit()
{
	var selects = jQuery('.mmm-select-multi');

	jQuery.each(selects, function() {
		var curSelect = jQuery(this);

		var curTextID = curSelect.prop("id").replace('mmm-select-','');;
    	var curText = jQuery('#' + curTextID);

    	var selectValue = curSelect.val();

   		var joinedSelectValue = selectValue.join(",");
    	
		curText.val(joinedSelectValue);
	});
}

function UpdateTextFieldFromSelect()
{
	//@TODO This function and control should be updated to remove the extra text input now that the order is bound on sortableStop
	var selects = jQuery('.mmm-select-multi');

	selects.on("change", function(e) {

		var curSelect = jQuery(e.currentTarget);

		var curTextID = curSelect.prop("id").replace('mmm-select-','');;
    	var curText = jQuery('#' + curTextID);

    	var selectValue = curSelect.val();

		var curUpdateRegionID = curTextID + "-update";
		var curUpdateRegion = jQuery('#' + curUpdateRegionID);
		curUpdateRegion.html("&nbsp");

		var joinedSelectValue = "";

    	if (selectValue != null)
    	{
    		//Update Edit Region
			var length = selectValue.length;
			var elems = "";

			for (var i = 0; i < length; i++) {
				var label = curSelect.find('option[value="' + selectValue[i] + '"]');
				var temp = "";
				temp = '<a target="blank" href="post.php?post=' + label.val() + '&amp;action=edit">' + label.html() + '</a> ';
				var elems = elems + temp;
			}

			curUpdateRegion.html(elems);

    		joinedSelectValue = selectValue.join(",");
    	}
    	
		curText.val(joinedSelectValue);
	});
}


function convert_hex_to_rgb(hex)
{
    h = (hex.charAt(0)=="#") ? hex.substring(1,7):hex;

    r = parseInt((h).substring(0,2),16);
    g = parseInt((h).substring(2,4),16);
    b = parseInt((h).substring(4,6),16);

    return r + ", " + g + ", " + b;
}

function convert_rgb_to_hex(rgb_array)
{
    r = toHex(rgb_array[0]);
    g = toHex(rgb_array[1]);
    b = toHex(rgb_array[2]);

    return r + g + b;
}

function toHex(n) {
    n = parseInt(n,10);
    if (isNaN(n)) return "00";
    n = Math.max(0,Math.min(n,255));
    return "0123456789ABCDEF".charAt((n-n%16)/16)
      + "0123456789ABCDEF".charAt(n%16);
}

function update_color_picker_control(parent_id)
{
    alpha_id = parent_id + "_alpha";
    color_picker_id = parent_id + "_color";
    control_id = parent_id;

    if (jQuery(alpha_id).val() == 10)
    {
        jQuery(control_id).val(jQuery(color_picker_id).val());
    }
    else
    {
        rgb = convert_hex_to_rgb(jQuery(color_picker_id).val());
        jQuery(control_id).val("rgba(" + rgb + ", " + jQuery(alpha_id).val() / 10 + ")");
    }

    jQuery(control_id).change();
}

jQuery(document).ready(function($) {
	$('#btnOptionsSave').click(function(e) {
		e.preventDefault();

		UpdateTextFieldFromSelect();

		if (ValidateForm("form#theme_settings"))
		{
			SaveOptions(jQuery("form#theme_settings"));
		}
	});

	SetupUploadControls();
	SetupSelects();
	SetupSaveEvents();

	/* $('.hex_color').change(function(data, handler) {
		$('#' + data.target.id + '-update').html(data.target.value);
	}); */

	var options = {
			change: function(event, ui) {console.log(event.target);}
		};

	$('.hex_color').wpColorPicker(options);

	mmmcolorpicker = {
	    init: function() {
	        $.each($('.mmm_color_picker'), function (i, elem) {
	            var parent_id = '#' + elem.id;
	            var color_input = parent_id + "_color";
	            var range_input = parent_id + "_alpha";
	            var range_output = parent_id + "_output";


	            var curVal = $(elem).val().replace("rgba(", "");
	            curVal = curVal.replace(")", "");
	            curVal = curVal.replace("0.", "");
	            curVal = curVal.split(",");

	            if (curVal.length > 1)
	            {
	                hexVal = convert_rgb_to_hex(curVal);
	                $(color_input).val(hexVal).change();
	                
	                alphaVal = curVal[3];

	                $(range_output).html(alphaVal);
	                $(range_input).val(alphaVal);
	            }
	            else
	            {
	                $(color_input).val(curVal).change();
	                //In this case we've found a hex value so there is no alpha
	            }
	        });

	        var options = {
				change: function(event, ui) {
					parent_id = '#' + $(event.target).parent().parent().parent().attr('for');
					update_color_picker_control(parent_id);
				}
			};

			$('.hex_color').wpColorPicker(options);

	        $('.alpha_range').change(function(e) {
	            output_control = $('#' + $(e.target).parent().attr('for') + "_output");
	            output_control.html(e.target.value);

	            update_color_picker_control('#' + $(e.target).parent().attr('for'));
	        });
	    } // end init
	}; // end mmm.colorpicker

	mmmcolorpicker.init();

	// Object for creating WordPress 3.5 media upload menu 
	// for selecting theme images.
	wp.media.MmmMediaManager = {
	     
	    init: function() {
	        // Create the media frame.
	        this.frame = wp.media.frames.MmmMediaManager = wp.media({
	            title: 'Choose Image',
	            library: {
	                type: 'image'
	            },
	            button: {
	                text: 'Insert',
	            }
	        });
	 
	        // When an image is selected, run a callback.
	        this.frame.on( 'select', function() {
	            // Grab the selected attachment.
	            var attachment = wp.media.MmmMediaManager.frame.state().get('selection').first(),
	                controllerName = wp.media.MmmMediaManager.$el.data('controller');
	             
	            controller = wp.customize.control.instance(controllerName);
	            controller.thumbnailSrc(attachment.attributes.url);
	            controller.setting.set(attachment.attributes.url);
	        });

	        $('.open-media-library').click( function( event ) {
	            wp.media.MmmMediaManager.$el = $(this);
	            var controllerName = $(this).data('controller');
	            event.preventDefault();
	 
	            wp.media.MmmMediaManager.frame.open();
	        });
	         
	    } // end init
	}; // end MmmMediaManager
	 
	wp.media.MmmMediaManager.init();
});