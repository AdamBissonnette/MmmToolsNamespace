<?php
namespace MmmToolsNamespace;


	function createFormField($label, $name, $value, $type, $options=array())
	{
		$output = '';
		$field = '';
		$useField = true;
		$isHtml = false;

		switch ($type)
		{
			case 'text':
				$field = createInput($label, $value, $type, $options);
			break;
			case 'textarea':
				$field = createTextArea($label, $value, $options);
			break;
			case 'select':
				$field = createSelect($label, $value, $options);
			break;
			case 'editor':
				$useField = false;
			break;
			case 'html':
				$useField = false;
				$isHtml = true;
			break;
			case 'checkbox':
				$field = createCheckbox($label, $value, $options);
			break;
			case 'image':
				$field = createImageUpload($label, $name, $value, $options);
			break;
			case 'color':
				$field = createColorPicker($label, $value, $options);
			break;
		}

		$formFieldTemplate = '<div class="control-group %s-wrap">%s<div class="controls">%s</div></div>';

		$formFieldLabel = "";

		if ($isHtml)
		{
			extract( merge_options(
				array("data" => ""), $options)
			);

			$field = $data;
		}
		else if (!$useField)
		{
			ob_start(); //Since there isn't a nice way to get this content we have to use the output buffer
			wp_editor( $value, $label, $settings = array() );
			$field = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			if ($name != "")
			{
				$formFieldLabel = sprintf('<label class="control-label" for="%s">%s</label>', $label, $name);
			}
		}

		$output = sprintf($formFieldTemplate, $label, $formFieldLabel, $field);

		return $output;
	}

	function merge_options($pairs, $atts) {
	    $atts = (array)$atts;
	    $out = array();
	    foreach($pairs as $name => $default) {
	            if ( array_key_exists($name, $atts) )
	                    $out[$name] = $atts[$name];
	            else
	                    $out[$name] = $default;
	    }
	    return $out;
	}

	function createInput($label, $value, $type="text", $options = null)
	{
		extract( merge_options(
			array("class" => "",
				"placeholder" => "",
				"note" => "",
				"updateRegion" => false,
				"disabled" => false,
				"default_value" => ""),
			$options)
		);

		$name = $label;
		$disabledParam = "";

		if ($disabled)
		{
			$disabledParam = ' disabled="disabled"';
			$value = $default_value;
			$name = "";
		}

		$output = sprintf('<input type="%s" id="%s" class="%s" name="%s" value="%s" placeholder="%s"%s />', $type,
			 $label, //id
			 $class,
			 $name, //name
			 stripslashes($value), //value
			 $placeholder,
			 $disabledParam
		);
		
		if ($updateRegion == true) {
			$output .= sprintf('<div class="mmm-update-region"><label class="control-label"><i class="fa fa-level-up fa-rotate-90"></i> Field Value</label><div class="controls"><div id="%s-update" class="mmm-update-content">%s</div></div></div>', $label, $value);
		}

		if (isset($note)) {
			$output .= sprintf('<p class="help-block">%s</p>', $note);
		}
		
		return $output;
	}

	function createColorPicker($label, $value, $options = array())
	{
		extract( merge_options(
			array("note" => "", "title" => "", "link" => "", "default_value" => ""), $options)
		);

		$template = '<div for="%1$s" class="color-picker-container">
		    <span class="customize-control-title">%2$s</span>

		    <input type="text" title="Hex Color" class="hex_color" id="%1$s_color" />
		    
		    <input type="range" class="alpha_range" title="Alpha / Transparency (1-10)" id="%1$s_alpha" min="0" max="10" value="10" />
		    <output id="%1$s_output" for="%1$s_alpha" class="alpha_output">10</output>

		    <input type="text" class="mmm_color_picker" id="%1$s" name="%1$s" value="%3$s" %4$s readonly="readonly" />
		</div>';

		if ($value == "")
		{
			$value = $default_value;
		}

		$output = sprintf($template, $label, $title, $value, $link);

		return $output;
	}

	function createTextArea($label, $value, $options = null)
	{
		extract( merge_options(
			array("class" => "", "placeholder" => "", "rows" => 3, "note" => ""), $options)
		);

		$output = sprintf('<textarea id="%s" class="%s" rows="%s" name="%s" placeholder="%s">%s</textarea>', 
			 $label, //id
			 $class,
	 		 $rows,
			 $label, //name
			 $placeholder,
			 stripslashes($value) //value
		);
		
		if ($note) {
			$output .= sprintf('<p class="help-block">%s</p>', $note);
		}
		
		return $output;
	}

	function createCheckbox($label, $value, $options = null)
	{
		extract( merge_options(
			array("class" => "", "placeholder" => "", "note" => ""), $options)
		);

		$checked = "";
		if ($value != "") //if there is a value then it's checked
		{
			$checked = ' checked="checked"';
		}

		$output = sprintf('<input type="checkbox" id="%s" class="%s" name="%s"%s />', 
			 $label, //id
			 $class,
			 $label, //name
			 $checked //value
		);
		
		if ($note) {
			$output .= sprintf('<p class="help-block">%s</p>', $note);
		}
		
		return $output;
	}

	function createSelect($label, $selectedKey, $options)
	{
		extract( merge_options(
			array("class" => "", "placeholder" => "", "note" => "", "data" => array(), "isMultiple" => false, "addBlank" => false, "updateRegion" => false), $options)
		);

		$output = "No Data Available";
		$linkTemplate = '<a target="blank" href="post.php?post=%s&action=edit">%s</a> ';

		if (count($data) > 0)
		{
			$selectedKeys = array();
			$links = "";
		
			if ($selectedKey != "")
			{
				$selectedKeys = explode(",", $selectedKey);
			}
			
			//If it's a multi select then flag it as such and explode the key into keys
			if ($isMultiple)
			{
				$output = sprintf('<input style="display: none" type="text" id="%1$s" name="%1$s" value="%2$s" />', $label, $selectedKey);
				$output .= sprintf('<select id="mmm-select-%s" class="%s mmm-select-multi" multiple>', $label, $class, $label);
			}
			else
			{
				$output = sprintf('<select id="%s" class="%s mmm-select" name="%s">', $label, $class, $label);
				
			}

			if ($addBlank)
			{
				$output .= createSelectOption("", "", $placeholder);
			}

			foreach ($selectedKeys as $key) {
					$output .= createSelectOption($key, $data[$key], true);
					$links .= sprintf($linkTemplate, $key, $data[$key]);
					unset($data[$key]);
			}

			foreach ($data as $key => $text)
			{
				$output .= createSelectOption($key, $text);
			}
			
			$output .= '</select>';

			if ($updateRegion == true) {
				$output .= sprintf('<div class="mmm-update-region"><label class="control-label"><i class="fa fa-level-up fa-rotate-90"></i> Direct Links</label><div class="controls"><div id="%s-update" class="mmm-update-content">%s</div></div></div>', $label, $links);
			}

			if ($note != "") {
				$output .= sprintf('<p class="help-block">%s</p>', $note);
			}		
		}

		return $output;
	}

	function createSelectOption($key, $text, $selected = false)
	{
		$optionTemplate = '<option value="%s"%s>%s</option>\n';
		$output = "";

		if ($selected)
		{
			$output .= sprintf($optionTemplate, $key, ' selected', $text);
		}
		else
		{
			$output .= sprintf($optionTemplate, $key, '', $text);
		}

		return $output;
	}

	function createImageUpload($label, $name, $value, $options)
	{
		extract( merge_options(
			array("class" => "", "note" => "", "isMultiple" => false, "updateOnChange" => ""), $options)
		);

		$template = '
					<div class="image_uploader %2$s">
						<input id="%1$s" type="text" name="%1$s" value="%3$s" />
						<a title="Set Image" id="%1$s_upload" class="thickbox mmm-upload %1$s_upload">Upload %4$s</a>
					</div>';

		$output = sprintf($template, $label, $class, $value, $name);

		if ($note != "") {
			$output .= sprintf('<p class="help-block">%s</p>', $note);
		}

		return $output;
	}

	function getCategorySelectArray()
	{
		$categories = get_categories(array('hide_empty' => 0));
		
		$catArray = array();
		foreach ($categories as $category)
		{
			$catArray[$category->term_id] = $category->cat_name;
		}
		
		return $catArray;
	}

	function getPagesSelectArray()
	{
		return getTaxonomySelectArray('page');
	}

	function getPostsSelectArray()
	{
		return getTaxonomySelectArray('post');
	}

	function getTaxonomySelectArray($taxonomy, $posts_per_page = -1)
	{
		$args = array('post_type' => $taxonomy, 'posts_per_page' => $posts_per_page);
		$posts = get_posts($args);
		
		$postArray = array();
		foreach ($posts as $post)
		{
			$postArray[$post->ID] = $post->post_title;
		}

		return $postArray;
	}

	function getFontAwesomeSelectArray()
	{

		return array('adjust' => 'adjust [&#xf042;]', 'adn' => 'adn [&#xf170;]', 'align-center' => 'align-center [&#xf037;]', 'align-justify' => 'align-justify [&#xf039;]', 'align-left' => 'align-left [&#xf036;]', 'align-right' => 'align-right [&#xf038;]', 'ambulance' => 'ambulance [&#xf0f9;]', 'anchor' => 'anchor [&#xf13d;]', 'android' => 'android [&#xf17b;]', 'angellist' => 'angellist [&#xf209;]', 'angle-double-down' => 'angle-double-down [&#xf103;]', 'angle-double-left' => 'angle-double-left [&#xf100;]', 'angle-double-right' => 'angle-double-right [&#xf101;]', 'angle-double-up' => 'angle-double-up [&#xf102;]', 'angle-down' => 'angle-down [&#xf107;]', 'angle-left' => 'angle-left [&#xf104;]', 'angle-right' => 'angle-right [&#xf105;]', 'angle-up' => 'angle-up [&#xf106;]', 'apple' => 'apple [&#xf179;]', 'archive' => 'archive [&#xf187;]', 'area-chart' => 'area-chart [&#xf1fe;]', 'arrow-circle-down' => 'arrow-circle-down [&#xf0ab;]', 'arrow-circle-left' => 'arrow-circle-left [&#xf0a8;]', 'arrow-circle-o-down' => 'arrow-circle-o-down [&#xf01a;]', 'arrow-circle-o-left' => 'arrow-circle-o-left [&#xf190;]', 'arrow-circle-o-right' => 'arrow-circle-o-right [&#xf18e;]', 'arrow-circle-o-up' => 'arrow-circle-o-up [&#xf01b;]', 'arrow-circle-right' => 'arrow-circle-right [&#xf0a9;]', 'arrow-circle-up' => 'arrow-circle-up [&#xf0aa;]', 'arrow-down' => 'arrow-down [&#xf063;]', 'arrow-left' => 'arrow-left [&#xf060;]', 'arrow-right' => 'arrow-right [&#xf061;]', 'arrow-up' => 'arrow-up [&#xf062;]', 'arrows' => 'arrows [&#xf047;]', 'arrows-alt' => 'arrows-alt [&#xf0b2;]', 'arrows-h' => 'arrows-h [&#xf07e;]', 'arrows-v' => 'arrows-v [&#xf07d;]', 'asterisk' => 'asterisk [&#xf069;]', 'at' => 'at [&#xf1fa;]', 'automobile' => 'automobile (alias) [&#xf1b9;]', 'backward' => 'backward [&#xf04a;]', 'ban' => 'ban [&#xf05e;]', 'bank' => 'bank (alias) [&#xf19c;]', 'bar-chart' => 'bar-chart [&#xf080;]', 'bar-chart-o' => 'bar-chart-o (alias) [&#xf080;]', 'barcode' => 'barcode [&#xf02a;]', 'bars' => 'bars [&#xf0c9;]', 'beer' => 'beer [&#xf0fc;]', 'behance' => 'behance [&#xf1b4;]', 'behance-square' => 'behance-square [&#xf1b5;]', 'bell' => 'bell [&#xf0f3;]', 'bell-o' => 'bell-o [&#xf0a2;]', 'bell-slash' => 'bell-slash [&#xf1f6;]', 'bell-slash-o' => 'bell-slash-o [&#xf1f7;]', 'bicycle' => 'bicycle [&#xf206;]', 'binoculars' => 'binoculars [&#xf1e5;]', 'birthday-cake' => 'birthday-cake [&#xf1fd;]', 'bitbucket' => 'bitbucket [&#xf171;]', 'bitbucket-square' => 'bitbucket-square [&#xf172;]', 'bitcoin' => 'bitcoin (alias) [&#xf15a;]', 'bold' => 'bold [&#xf032;]', 'bolt' => 'bolt [&#xf0e7;]', 'bomb' => 'bomb [&#xf1e2;]', 'book' => 'book [&#xf02d;]', 'bookmark' => 'bookmark [&#xf02e;]', 'bookmark-o' => 'bookmark-o [&#xf097;]', 'briefcase' => 'briefcase [&#xf0b1;]', 'btc' => 'btc [&#xf15a;]', 'bug' => 'bug [&#xf188;]', 'building' => 'building [&#xf1ad;]', 'building-o' => 'building-o [&#xf0f7;]', 'bullhorn' => 'bullhorn [&#xf0a1;]', 'bullseye' => 'bullseye [&#xf140;]', 'bus' => 'bus [&#xf207;]', 'cab' => 'cab (alias) [&#xf1ba;]', 'calculator' => 'calculator [&#xf1ec;]', 'calendar' => 'calendar [&#xf073;]', 'calendar-o' => 'calendar-o [&#xf133;]', 'camera' => 'camera [&#xf030;]', 'camera-retro' => 'camera-retro [&#xf083;]', 'car' => 'car [&#xf1b9;]', 'caret-down' => 'caret-down [&#xf0d7;]', 'caret-left' => 'caret-left [&#xf0d9;]', 'caret-right' => 'caret-right [&#xf0da;]', 'caret-square-o-down' => 'caret-square-o-down [&#xf150;]', 'caret-square-o-left' => 'caret-square-o-left [&#xf191;]', 'caret-square-o-right' => 'caret-square-o-right [&#xf152;]', 'caret-square-o-up' => 'caret-square-o-up [&#xf151;]', 'caret-up' => 'caret-up [&#xf0d8;]', 'cc' => 'cc [&#xf20a;]', 'cc-amex' => 'cc-amex [&#xf1f3;]', 'cc-discover' => 'cc-discover [&#xf1f2;]', 'cc-mastercard' => 'cc-mastercard [&#xf1f1;]', 'cc-paypal' => 'cc-paypal [&#xf1f4;]', 'cc-stripe' => 'cc-stripe [&#xf1f5;]', 'cc-visa' => 'cc-visa [&#xf1f0;]', 'certificate' => 'certificate [&#xf0a3;]', 'chain' => 'chain (alias) [&#xf0c1;]', 'chain-broken' => 'chain-broken [&#xf127;]', 'check' => 'check [&#xf00c;]', 'check-circle' => 'check-circle [&#xf058;]', 'check-circle-o' => 'check-circle-o [&#xf05d;]', 'check-square' => 'check-square [&#xf14a;]', 'check-square-o' => 'check-square-o [&#xf046;]', 'chevron-circle-down' => 'chevron-circle-down [&#xf13a;]', 'chevron-circle-left' => 'chevron-circle-left [&#xf137;]', 'chevron-circle-right' => 'chevron-circle-right [&#xf138;]', 'chevron-circle-up' => 'chevron-circle-up [&#xf139;]', 'chevron-down' => 'chevron-down [&#xf078;]', 'chevron-left' => 'chevron-left [&#xf053;]', 'chevron-right' => 'chevron-right [&#xf054;]', 'chevron-up' => 'chevron-up [&#xf077;]', 'child' => 'child [&#xf1ae;]', 'circle' => 'circle [&#xf111;]', 'circle-o' => 'circle-o [&#xf10c;]', 'circle-o-notch' => 'circle-o-notch [&#xf1ce;]', 'circle-thin' => 'circle-thin [&#xf1db;]', 'clipboard' => 'clipboard [&#xf0ea;]', 'clock-o' => 'clock-o [&#xf017;]', 'close' => 'close (alias) [&#xf00d;]', 'cloud' => 'cloud [&#xf0c2;]', 'cloud-download' => 'cloud-download [&#xf0ed;]', 'cloud-upload' => 'cloud-upload [&#xf0ee;]', 'cny' => 'cny (alias) [&#xf157;]', 'code' => 'code [&#xf121;]', 'code-fork' => 'code-fork [&#xf126;]', 'codepen' => 'codepen [&#xf1cb;]', 'coffee' => 'coffee [&#xf0f4;]', 'cog' => 'cog [&#xf013;]', 'cogs' => 'cogs [&#xf085;]', 'columns' => 'columns [&#xf0db;]', 'comment' => 'comment [&#xf075;]', 'comment-o' => 'comment-o [&#xf0e5;]', 'comments' => 'comments [&#xf086;]', 'comments-o' => 'comments-o [&#xf0e6;]', 'compass' => 'compass [&#xf14e;]', 'compress' => 'compress [&#xf066;]', 'copy' => 'copy (alias) [&#xf0c5;]', 'copyright' => 'copyright [&#xf1f9;]', 'credit-card' => 'credit-card [&#xf09d;]', 'crop' => 'crop [&#xf125;]', 'crosshairs' => 'crosshairs [&#xf05b;]', 'css3' => 'css3 [&#xf13c;]', 'cube' => 'cube [&#xf1b2;]', 'cubes' => 'cubes [&#xf1b3;]', 'cut' => 'cut (alias) [&#xf0c4;]', 'cutlery' => 'cutlery [&#xf0f5;]', 'dashboard' => 'dashboard (alias) [&#xf0e4;]', 'database' => 'database [&#xf1c0;]', 'dedent' => 'dedent (alias) [&#xf03b;]', 'delicious' => 'delicious [&#xf1a5;]', 'desktop' => 'desktop [&#xf108;]', 'deviantart' => 'deviantart [&#xf1bd;]', 'digg' => 'digg [&#xf1a6;]', 'dollar' => 'dollar (alias) [&#xf155;]', 'dot-circle-o' => 'dot-circle-o [&#xf192;]', 'download' => 'download [&#xf019;]', 'dribbble' => 'dribbble [&#xf17d;]', 'dropbox' => 'dropbox [&#xf16b;]', 'drupal' => 'drupal [&#xf1a9;]', 'edit' => 'edit (alias) [&#xf044;]', 'eject' => 'eject [&#xf052;]', 'ellipsis-h' => 'ellipsis-h [&#xf141;]', 'ellipsis-v' => 'ellipsis-v [&#xf142;]', 'empire' => 'empire [&#xf1d1;]', 'envelope' => 'envelope [&#xf0e0;]', 'envelope-o' => 'envelope-o [&#xf003;]', 'envelope-square' => 'envelope-square [&#xf199;]', 'eraser' => 'eraser [&#xf12d;]', 'eur' => 'eur [&#xf153;]', 'euro' => 'euro (alias) [&#xf153;]', 'exchange' => 'exchange [&#xf0ec;]', 'exclamation' => 'exclamation [&#xf12a;]', 'exclamation-circle' => 'exclamation-circle [&#xf06a;]', 'exclamation-triangle' => 'exclamation-triangle [&#xf071;]', 'expand' => 'expand [&#xf065;]', 'external-link' => 'external-link [&#xf08e;]', 'external-link-square' => 'external-link-square [&#xf14c;]', 'eye' => 'eye [&#xf06e;]', 'eye-slash' => 'eye-slash [&#xf070;]', 'eyedropper' => 'eyedropper [&#xf1fb;]', 'facebook' => 'facebook [&#xf09a;]', 'facebook-square' => 'facebook-square [&#xf082;]', 'fast-backward' => 'fast-backward [&#xf049;]', 'fast-forward' => 'fast-forward [&#xf050;]', 'fax' => 'fax [&#xf1ac;]', 'female' => 'female [&#xf182;]', 'fighter-jet' => 'fighter-jet [&#xf0fb;]', 'file' => 'file [&#xf15b;]', 'file-archive-o' => 'file-archive-o [&#xf1c6;]', 'file-audio-o' => 'file-audio-o [&#xf1c7;]', 'file-code-o' => 'file-code-o [&#xf1c9;]', 'file-excel-o' => 'file-excel-o [&#xf1c3;]', 'file-image-o' => 'file-image-o [&#xf1c5;]', 'file-movie-o' => 'file-movie-o (alias) [&#xf1c8;]', 'file-o' => 'file-o [&#xf016;]', 'file-pdf-o' => 'file-pdf-o [&#xf1c1;]', 'file-photo-o' => 'file-photo-o (alias) [&#xf1c5;]', 'file-picture-o' => 'file-picture-o (alias) [&#xf1c5;]', 'file-powerpoint-o' => 'file-powerpoint-o [&#xf1c4;]', 'file-sound-o' => 'file-sound-o (alias) [&#xf1c7;]', 'file-text' => 'file-text [&#xf15c;]', 'file-text-o' => 'file-text-o [&#xf0f6;]', 'file-video-o' => 'file-video-o [&#xf1c8;]', 'file-word-o' => 'file-word-o [&#xf1c2;]', 'file-zip-o' => 'file-zip-o (alias) [&#xf1c6;]', 'files-o' => 'files-o [&#xf0c5;]', 'film' => 'film [&#xf008;]', 'filter' => 'filter [&#xf0b0;]', 'fire' => 'fire [&#xf06d;]', 'fire-extinguisher' => 'fire-extinguisher [&#xf134;]', 'flag' => 'flag [&#xf024;]', 'flag-checkered' => 'flag-checkered [&#xf11e;]', 'flag-o' => 'flag-o [&#xf11d;]', 'flash' => 'flash (alias) [&#xf0e7;]', 'flask' => 'flask [&#xf0c3;]', 'flickr' => 'flickr [&#xf16e;]', 'floppy-o' => 'floppy-o [&#xf0c7;]', 'folder' => 'folder [&#xf07b;]', 'folder-o' => 'folder-o [&#xf114;]', 'folder-open' => 'folder-open [&#xf07c;]', 'folder-open-o' => 'folder-open-o [&#xf115;]', 'font' => 'font [&#xf031;]', 'forward' => 'forward [&#xf04e;]', 'foursquare' => 'foursquare [&#xf180;]', 'frown-o' => 'frown-o [&#xf119;]', 'futbol-o' => 'futbol-o [&#xf1e3;]', 'gamepad' => 'gamepad [&#xf11b;]', 'gavel' => 'gavel [&#xf0e3;]', 'gbp' => 'gbp [&#xf154;]', 'ge' => 'ge (alias) [&#xf1d1;]', 'gear' => 'gear (alias) [&#xf013;]', 'gears' => 'gears (alias) [&#xf085;]', 'gift' => 'gift [&#xf06b;]', 'git' => 'git [&#xf1d3;]', 'git-square' => 'git-square [&#xf1d2;]', 'github' => 'github [&#xf09b;]', 'github-alt' => 'github-alt [&#xf113;]', 'github-square' => 'github-square [&#xf092;]', 'gittip' => 'gittip [&#xf184;]', 'glass' => 'glass [&#xf000;]', 'globe' => 'globe [&#xf0ac;]', 'google' => 'google [&#xf1a0;]', 'google-plus' => 'google-plus [&#xf0d5;]', 'google-plus-square' => 'google-plus-square [&#xf0d4;]', 'google-wallet' => 'google-wallet [&#xf1ee;]', 'graduation-cap' => 'graduation-cap [&#xf19d;]', 'group' => 'group (alias) [&#xf0c0;]', 'h-square' => 'h-square [&#xf0fd;]', 'hacker-news' => 'hacker-news [&#xf1d4;]', 'hand-o-down' => 'hand-o-down [&#xf0a7;]', 'hand-o-left' => 'hand-o-left [&#xf0a5;]', 'hand-o-right' => 'hand-o-right [&#xf0a4;]', 'hand-o-up' => 'hand-o-up [&#xf0a6;]', 'hdd-o' => 'hdd-o [&#xf0a0;]', 'header' => 'header [&#xf1dc;]', 'headphones' => 'headphones [&#xf025;]', 'heart' => 'heart [&#xf004;]', 'heart-o' => 'heart-o [&#xf08a;]', 'history' => 'history [&#xf1da;]', 'home' => 'home [&#xf015;]', 'hospital-o' => 'hospital-o [&#xf0f8;]', 'html5' => 'html5 [&#xf13b;]', 'ils' => 'ils [&#xf20b;]', 'image' => 'image (alias) [&#xf03e;]', 'inbox' => 'inbox [&#xf01c;]', 'indent' => 'indent [&#xf03c;]', 'info' => 'info [&#xf129;]', 'info-circle' => 'info-circle [&#xf05a;]', 'inr' => 'inr [&#xf156;]', 'instagram' => 'instagram [&#xf16d;]', 'institution' => 'institution (alias) [&#xf19c;]', 'ioxhost' => 'ioxhost [&#xf208;]', 'italic' => 'italic [&#xf033;]', 'joomla' => 'joomla [&#xf1aa;]', 'jpy' => 'jpy [&#xf157;]', 'jsfiddle' => 'jsfiddle [&#xf1cc;]', 'key' => 'key [&#xf084;]', 'keyboard-o' => 'keyboard-o [&#xf11c;]', 'krw' => 'krw [&#xf159;]', 'language' => 'language [&#xf1ab;]', 'laptop' => 'laptop [&#xf109;]', 'lastfm' => 'lastfm [&#xf202;]', 'lastfm-square' => 'lastfm-square [&#xf203;]', 'leaf' => 'leaf [&#xf06c;]', 'legal' => 'legal (alias) [&#xf0e3;]', 'lemon-o' => 'lemon-o [&#xf094;]', 'level-down' => 'level-down [&#xf149;]', 'level-up' => 'level-up [&#xf148;]', 'life-bouy' => 'life-bouy (alias) [&#xf1cd;]', 'life-buoy' => 'life-buoy (alias) [&#xf1cd;]', 'life-ring' => 'life-ring [&#xf1cd;]', 'life-saver' => 'life-saver (alias) [&#xf1cd;]', 'lightbulb-o' => 'lightbulb-o [&#xf0eb;]', 'line-chart' => 'line-chart [&#xf201;]', 'link' => 'link [&#xf0c1;]', 'linkedin' => 'linkedin [&#xf0e1;]', 'linkedin-square' => 'linkedin-square [&#xf08c;]', 'linux' => 'linux [&#xf17c;]', 'list' => 'list [&#xf03a;]', 'list-alt' => 'list-alt [&#xf022;]', 'list-ol' => 'list-ol [&#xf0cb;]', 'list-ul' => 'list-ul [&#xf0ca;]', 'location-arrow' => 'location-arrow [&#xf124;]', 'lock' => 'lock [&#xf023;]', 'long-arrow-down' => 'long-arrow-down [&#xf175;]', 'long-arrow-left' => 'long-arrow-left [&#xf177;]', 'long-arrow-right' => 'long-arrow-right [&#xf178;]', 'long-arrow-up' => 'long-arrow-up [&#xf176;]', 'magic' => 'magic [&#xf0d0;]', 'magnet' => 'magnet [&#xf076;]', 'mail-forward' => 'mail-forward (alias) [&#xf064;]', 'mail-reply' => 'mail-reply (alias) [&#xf112;]', 'mail-reply-all' => 'mail-reply-all (alias) [&#xf122;]', 'male' => 'male [&#xf183;]', 'map-marker' => 'map-marker [&#xf041;]', 'maxcdn' => 'maxcdn [&#xf136;]', 'meanpath' => 'meanpath [&#xf20c;]', 'medkit' => 'medkit [&#xf0fa;]', 'meh-o' => 'meh-o [&#xf11a;]', 'microphone' => 'microphone [&#xf130;]', 'microphone-slash' => 'microphone-slash [&#xf131;]', 'minus' => 'minus [&#xf068;]', 'minus-circle' => 'minus-circle [&#xf056;]', 'minus-square' => 'minus-square [&#xf146;]', 'minus-square-o' => 'minus-square-o [&#xf147;]', 'mobile' => 'mobile [&#xf10b;]', 'mobile-phone' => 'mobile-phone (alias) [&#xf10b;]', 'money' => 'money [&#xf0d6;]', 'moon-o' => 'moon-o [&#xf186;]', 'mortar-board' => 'mortar-board (alias) [&#xf19d;]', 'music' => 'music [&#xf001;]', 'navicon' => 'navicon (alias) [&#xf0c9;]', 'newspaper-o' => 'newspaper-o [&#xf1ea;]', 'openid' => 'openid [&#xf19b;]', 'outdent' => 'outdent [&#xf03b;]', 'pagelines' => 'pagelines [&#xf18c;]', 'paint-brush' => 'paint-brush [&#xf1fc;]', 'paper-plane' => 'paper-plane [&#xf1d8;]', 'paper-plane-o' => 'paper-plane-o [&#xf1d9;]', 'paperclip' => 'paperclip [&#xf0c6;]', 'paragraph' => 'paragraph [&#xf1dd;]', 'paste' => 'paste (alias) [&#xf0ea;]', 'pause' => 'pause [&#xf04c;]', 'paw' => 'paw [&#xf1b0;]', 'paypal' => 'paypal [&#xf1ed;]', 'pencil' => 'pencil [&#xf040;]', 'pencil-square' => 'pencil-square [&#xf14b;]', 'pencil-square-o' => 'pencil-square-o [&#xf044;]', 'phone' => 'phone [&#xf095;]', 'phone-square' => 'phone-square [&#xf098;]', 'photo' => 'photo (alias) [&#xf03e;]', 'picture-o' => 'picture-o [&#xf03e;]', 'pie-chart' => 'pie-chart [&#xf200;]', 'pied-piper' => 'pied-piper [&#xf1a7;]', 'pied-piper-alt' => 'pied-piper-alt [&#xf1a8;]', 'pinterest' => 'pinterest [&#xf0d2;]', 'pinterest-square' => 'pinterest-square [&#xf0d3;]', 'plane' => 'plane [&#xf072;]', 'play' => 'play [&#xf04b;]', 'play-circle' => 'play-circle [&#xf144;]', 'play-circle-o' => 'play-circle-o [&#xf01d;]', 'plug' => 'plug [&#xf1e6;]', 'plus' => 'plus [&#xf067;]', 'plus-circle' => 'plus-circle [&#xf055;]', 'plus-square' => 'plus-square [&#xf0fe;]', 'plus-square-o' => 'plus-square-o [&#xf196;]', 'power-off' => 'power-off [&#xf011;]', 'print' => 'print [&#xf02f;]', 'puzzle-piece' => 'puzzle-piece [&#xf12e;]', 'qq' => 'qq [&#xf1d6;]', 'qrcode' => 'qrcode [&#xf029;]', 'question' => 'question [&#xf128;]', 'question-circle' => 'question-circle [&#xf059;]', 'quote-left' => 'quote-left [&#xf10d;]', 'quote-right' => 'quote-right [&#xf10e;]', 'ra' => 'ra (alias) [&#xf1d0;]', 'random' => 'random [&#xf074;]', 'rebel' => 'rebel [&#xf1d0;]', 'recycle' => 'recycle [&#xf1b8;]', 'reddit' => 'reddit [&#xf1a1;]', 'reddit-square' => 'reddit-square [&#xf1a2;]', 'refresh' => 'refresh [&#xf021;]', 'remove' => 'remove (alias) [&#xf00d;]', 'renren' => 'renren [&#xf18b;]', 'reorder' => 'reorder (alias) [&#xf0c9;]', 'repeat' => 'repeat [&#xf01e;]', 'reply' => 'reply [&#xf112;]', 'reply-all' => 'reply-all [&#xf122;]', 'retweet' => 'retweet [&#xf079;]', 'rmb' => 'rmb (alias) [&#xf157;]', 'road' => 'road [&#xf018;]', 'rocket' => 'rocket [&#xf135;]', 'rotate-left' => 'rotate-left (alias) [&#xf0e2;]', 'rotate-right' => 'rotate-right (alias) [&#xf01e;]', 'rouble' => 'rouble (alias) [&#xf158;]', 'rss' => 'rss [&#xf09e;]', 'rss-square' => 'rss-square [&#xf143;]', 'rub' => 'rub [&#xf158;]', 'ruble' => 'ruble (alias) [&#xf158;]', 'rupee' => 'rupee (alias) [&#xf156;]', 'save' => 'save (alias) [&#xf0c7;]', 'scissors' => 'scissors [&#xf0c4;]', 'search' => 'search [&#xf002;]', 'search-minus' => 'search-minus [&#xf010;]', 'search-plus' => 'search-plus [&#xf00e;]', 'send' => 'send (alias) [&#xf1d8;]', 'send-o' => 'send-o (alias) [&#xf1d9;]', 'share' => 'share [&#xf064;]', 'share-alt' => 'share-alt [&#xf1e0;]', 'share-alt-square' => 'share-alt-square [&#xf1e1;]', 'share-square' => 'share-square [&#xf14d;]', 'share-square-o' => 'share-square-o [&#xf045;]', 'shekel' => 'shekel (alias) [&#xf20b;]', 'sheqel' => 'sheqel (alias) [&#xf20b;]', 'shield' => 'shield [&#xf132;]', 'shopping-cart' => 'shopping-cart [&#xf07a;]', 'sign-in' => 'sign-in [&#xf090;]', 'sign-out' => 'sign-out [&#xf08b;]', 'signal' => 'signal [&#xf012;]', 'sitemap' => 'sitemap [&#xf0e8;]', 'skype' => 'skype [&#xf17e;]', 'slack' => 'slack [&#xf198;]', 'sliders' => 'sliders [&#xf1de;]', 'slideshare' => 'slideshare [&#xf1e7;]', 'smile-o' => 'smile-o [&#xf118;]', 'soccer-ball-o' => 'soccer-ball-o (alias) [&#xf1e3;]', 'sort' => 'sort [&#xf0dc;]', 'sort-alpha-asc' => 'sort-alpha-asc [&#xf15d;]', 'sort-alpha-desc' => 'sort-alpha-desc [&#xf15e;]', 'sort-amount-asc' => 'sort-amount-asc [&#xf160;]', 'sort-amount-desc' => 'sort-amount-desc [&#xf161;]', 'sort-asc' => 'sort-asc [&#xf0de;]', 'sort-desc' => 'sort-desc [&#xf0dd;]', 'sort-down' => 'sort-down (alias) [&#xf0dd;]', 'sort-numeric-asc' => 'sort-numeric-asc [&#xf162;]', 'sort-numeric-desc' => 'sort-numeric-desc [&#xf163;]', 'sort-up' => 'sort-up (alias) [&#xf0de;]', 'soundcloud' => 'soundcloud [&#xf1be;]', 'space-shuttle' => 'space-shuttle [&#xf197;]', 'spinner' => 'spinner [&#xf110;]', 'spoon' => 'spoon [&#xf1b1;]', 'spotify' => 'spotify [&#xf1bc;]', 'square' => 'square [&#xf0c8;]', 'square-o' => 'square-o [&#xf096;]', 'stack-exchange' => 'stack-exchange [&#xf18d;]', 'stack-overflow' => 'stack-overflow [&#xf16c;]', 'star' => 'star [&#xf005;]', 'star-half' => 'star-half [&#xf089;]', 'star-half-empty' => 'star-half-empty (alias) [&#xf123;]', 'star-half-full' => 'star-half-full (alias) [&#xf123;]', 'star-half-o' => 'star-half-o [&#xf123;]', 'star-o' => 'star-o [&#xf006;]', 'steam' => 'steam [&#xf1b6;]', 'steam-square' => 'steam-square [&#xf1b7;]', 'step-backward' => 'step-backward [&#xf048;]', 'step-forward' => 'step-forward [&#xf051;]', 'stethoscope' => 'stethoscope [&#xf0f1;]', 'stop' => 'stop [&#xf04d;]', 'strikethrough' => 'strikethrough [&#xf0cc;]', 'stumbleupon' => 'stumbleupon [&#xf1a4;]', 'stumbleupon-circle' => 'stumbleupon-circle [&#xf1a3;]', 'subscript' => 'subscript [&#xf12c;]', 'suitcase' => 'suitcase [&#xf0f2;]', 'sun-o' => 'sun-o [&#xf185;]', 'superscript' => 'superscript [&#xf12b;]', 'support' => 'support (alias) [&#xf1cd;]', 'table' => 'table [&#xf0ce;]', 'tablet' => 'tablet [&#xf10a;]', 'tachometer' => 'tachometer [&#xf0e4;]', 'tag' => 'tag [&#xf02b;]', 'tags' => 'tags [&#xf02c;]', 'tasks' => 'tasks [&#xf0ae;]', 'taxi' => 'taxi [&#xf1ba;]', 'tencent-weibo' => 'tencent-weibo [&#xf1d5;]', 'terminal' => 'terminal [&#xf120;]', 'text-height' => 'text-height [&#xf034;]', 'text-width' => 'text-width [&#xf035;]', 'th' => 'th [&#xf00a;]', 'th-large' => 'th-large [&#xf009;]', 'th-list' => 'th-list [&#xf00b;]', 'thumb-tack' => 'thumb-tack [&#xf08d;]', 'thumbs-down' => 'thumbs-down [&#xf165;]', 'thumbs-o-down' => 'thumbs-o-down [&#xf088;]', 'thumbs-o-up' => 'thumbs-o-up [&#xf087;]', 'thumbs-up' => 'thumbs-up [&#xf164;]', 'ticket' => 'ticket [&#xf145;]', 'times' => 'times [&#xf00d;]', 'times-circle' => 'times-circle [&#xf057;]', 'times-circle-o' => 'times-circle-o [&#xf05c;]', 'tint' => 'tint [&#xf043;]', 'toggle-down' => 'toggle-down (alias) [&#xf150;]', 'toggle-left' => 'toggle-left (alias) [&#xf191;]', 'toggle-off' => 'toggle-off [&#xf204;]', 'toggle-on' => 'toggle-on [&#xf205;]', 'toggle-right' => 'toggle-right (alias) [&#xf152;]', 'toggle-up' => 'toggle-up (alias) [&#xf151;]', 'trash' => 'trash [&#xf1f8;]', 'trash-o' => 'trash-o [&#xf014;]', 'tree' => 'tree [&#xf1bb;]', 'trello' => 'trello [&#xf181;]', 'trophy' => 'trophy [&#xf091;]', 'truck' => 'truck [&#xf0d1;]', 'try' => 'try [&#xf195;]', 'tty' => 'tty [&#xf1e4;]', 'tumblr' => 'tumblr [&#xf173;]', 'tumblr-square' => 'tumblr-square [&#xf174;]', 'turkish-lira' => 'turkish-lira (alias) [&#xf195;]', 'twitch' => 'twitch [&#xf1e8;]', 'twitter' => 'twitter [&#xf099;]', 'twitter-square' => 'twitter-square [&#xf081;]', 'umbrella' => 'umbrella [&#xf0e9;]', 'underline' => 'underline [&#xf0cd;]', 'undo' => 'undo [&#xf0e2;]', 'university' => 'university [&#xf19c;]', 'unlink' => 'unlink (alias) [&#xf127;]', 'unlock' => 'unlock [&#xf09c;]', 'unlock-alt' => 'unlock-alt [&#xf13e;]', 'unsorted' => 'unsorted (alias) [&#xf0dc;]', 'upload' => 'upload [&#xf093;]', 'usd' => 'usd [&#xf155;]', 'user' => 'user [&#xf007;]', 'user-md' => 'user-md [&#xf0f0;]', 'users' => 'users [&#xf0c0;]', 'video-camera' => 'video-camera [&#xf03d;]', 'vimeo-square' => 'vimeo-square [&#xf194;]', 'vine' => 'vine [&#xf1ca;]', 'vk' => 'vk [&#xf189;]', 'volume-down' => 'volume-down [&#xf027;]', 'volume-off' => 'volume-off [&#xf026;]', 'volume-up' => 'volume-up [&#xf028;]', 'warning' => 'warning (alias) [&#xf071;]', 'wechat' => 'wechat (alias) [&#xf1d7;]', 'weibo' => 'weibo [&#xf18a;]', 'weixin' => 'weixin [&#xf1d7;]', 'wheelchair' => 'wheelchair [&#xf193;]', 'wifi' => 'wifi [&#xf1eb;]', 'windows' => 'windows [&#xf17a;]', 'won' => 'won (alias) [&#xf159;]', 'wordpress' => 'wordpress [&#xf19a;]', 'wrench' => 'wrench [&#xf0ad;]', 'xing' => 'xing [&#xf168;]', 'xing-square' => 'xing-square [&#xf169;]', 'yahoo' => 'yahoo [&#xf19e;]', 'yelp' => 'yelp [&#xf1e9;]', 'yen' => 'yen (alias) [&#xf157;]', 'youtube' => 'youtube [&#xf167;]', 'youtube-play' => 'youtube-play [&#xf16a;]', 'youtube-square' => 'youtube-square [&#xf166;]');
	}
<<<<<<< HEAD
=======

	function getGoogleWebFontSelectArray()
	{
		return array();
	}
}
>>>>>>> e75631f78e519ed25ca78ca98d4733e68f1ffcac


?>