/*
 * Attaches the image uploader to the input field
 */
jQuery(document).ready(function($){
 
    //Instantiates the variable that holds the media library frame.
    var meta_image_frame;
 
    //Runs when the image button is clicked.
    $('#testimonial_client_image-button').click(function(e){
        console.log('hello');
 
        //Prevents the default action from occuring.
        e.preventDefault();
 
        //re-open meta image frame 
        if ( meta_image_frame ) {
            meta_image_frame.open();
            return;
        }
 
        //Set up the media library frame        
        meta_image_frame = wp.media.frames.meta_image_frame =  wp.media({
            multiple: false,
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }

        });

 
        //Runs when an image is selected.
        meta_image_frame.on('select', function(){
            e.preventDefault();
 
            //Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
 
            //Sends the attachment URL to custom field
            $('#testimonial_client_image').val(media_attachment.url);
        });
        meta_image_frame.open();
        
    });
});