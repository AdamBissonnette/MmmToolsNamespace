(function($) {
 
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
 
}(jQuery));