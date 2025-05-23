'use strict';

/**
 * All Types Meta Box Class JS
 *
 * JS used for the custom metaboxes and other form items.
 *
 */

//var jQuery =jQuery.noConflict();
function wpw_auto_poster_update_repeater_fields(){
    
      
    /**
     * Datepicker Field.
     *
     * @since 1.0.0
     */
    jQuery('.wpw-auto-poster-meta-date').each( function() {
      
      var jQuerythis  = jQuery(this),
          format = jQuerythis.attr('rel');
  
      jQuerythis.datepicker( { showButtonPanel: true, dateFormat: format } );
      
    });
    
    jQuery('.wpw-auto-poster-meta-datetime').each( function() {
      
      var jQuerythis  = jQuery(this),
          format = jQuerythis.attr('rel');
      jQuerythis.datetimepicker({ampm: true,dateFormat : format});//
      
    });
  
    /**
     * Timepicker Field.
     *
     * @since 1.0.0
     */
    jQuery('.wpw-auto-poster-meta-time').each( function() {
      
      var jQuerythis   = jQuery(this),
          format   = jQuerythis.attr('rel'),
          aampm    = jQuerythis.attr('data-ampm');
      if ('true' == aampm)
        aampm = true;
      else
        aampm = false;

      jQuerythis.timepicker( { showSecond: true, timeFormat: format, ampm: aampm } );
      
    });    
    
    /**
     * Select Color Field.
     *
     * @since 1.0.0
     */
    jQuery(document).on('click', '.wpw-auto-poster-meta-color-select', function() {
      var jQuerythis = jQuery(this);
      var id = jQuerythis.attr('rel');
      jQuery(this).siblings('.wpw-auto-poster-meta-color-picker').farbtastic("#" + id).toggle();
      return false;
    });
  
    /**
     * Add Files.
     *
     * @since 1.0.0
     */
    jQuery(document).on('click', '.wpw-auto-poster-meta-add-file', function() {
      var jQueryfirst = jQuery(this).parent().find('.file-input:first');
      jQueryfirst.clone().insertAfter(jQueryfirst).show();
      return false;
    });
    
    jQuery(document).on('click', '.wpw-auto-poster-meta-add-fileadvanced', function() {
      var jQueryfirst = jQuery(this).parent().find('.file-input-advanced:first');
      jQueryfirst.clone().insertAfter(jQueryfirst).show();
      return false;
    });
  
    /**
     * Delete File.
     *
     * @since 1.0.0
     */
    jQuery( document ).on( "click", ".wpw-auto-poster-meta-upload .wpw-auto-poster-meta-delete-file",function() {
      
      var jQuerythis   = jQuery(this),
          jQueryparent = jQuerythis.parent(),
          data     = jQuerythis.attr('rel');
          
      jQuery.post( ajaxurl, { action: 'at_delete_file', data: data }, function(response) {
        response == '0' ? ( alert( 'File has been successfully deleted.' ), jQueryparent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
      });
      
      return false;
    
    });
  
    /**
     * Reorder Images.
     *
     * @since 1.0.0
     */
    jQuery('.wpw-auto-poster-meta-images').each( function() {
      
      var jQuerythis = jQuery(this), order, data;
      
      jQuerythis.sortable( {
        placeholder: 'ui-state-highlight',
        update: function (){
          order = jQuerythis.sortable('serialize');
          data   = order + '|' + jQuerythis.siblings('.wpw-auto-poster-meta-images-data').val();
  
          jQuery.post(ajaxurl, {action: 'at_reorder_images', data: data}, function(response){
            response == '0' ? alert( 'Order saved!' ) : alert( "You don't have permission to reorder images." );
          });
        }
      });
      
    });
    
    /**
     * Thickbox Upload
     *
     * @since 1.0.0
     */
    jQuery( document ).on( "click", ".wpw-auto-poster-meta-upload-button", function() {
      
      var data       = jQuery(this).attr('rel').split('|'),
          post_id   = data[0],
          field_id   = data[1],
          backup     = window.send_to_editor; // backup the original 'send_to_editor' function which adds images to the editor
          
      // change the function to make it adds images to our section of uploaded images
      window.send_to_editor = function(html) {
        
        jQuery('#wpw-auto-poster-meta-images-' + field_id).append( jQuery(html) );
  
        tb_remove();
        
        window.send_to_editor = backup;
      
      };
  
      // note that we pass the field_id and post_id here
      tb_show('', 'media-upload.php?post_id=' + post_id + '&field_id=' + field_id + '&type=image&TB_iframe=true');
  
      return false;
    });
  
    /**
     * repeater sortable
     * @since 2.1
     */
    jQuery('.repeater-sortable').sortable();
  
  /**
     * enable chosen
     */
    wpwAutoPosterFancySelect();
  
  }
var Ed_array = Array;
jQuery(document).ready(function(jQuery) {

   /**
     * DateTimepicker Field.
     *
     * @since 1.0.0
     */
   
    jQuery('.wpw-auto-poster-meta-datetime').each( function() {
      
      var jQuerythis  = jQuery(this),
          format = jQuerythis.attr('rel');
      
      jQuerythis.datetimepicker({ampm: true,dateFormat : format});//,timeFormat:'hh:mm:ss',showSecond:true
      
    });
  /**
   *  conditinal fields
   *  @since 2.9.9
   */
  jQuery( document ).on( "click", ".conditinal_control", function() {

    if(jQuery(this).is(':checked')){
      jQuery(this).next().show('fast');    
    }else{
      jQuery(this).next().hide('fast');    
    }
  });

  /**
   * enable chosen
   * @since 2.9.8
   */
  wpwAutoPosterFancySelect();

  /**
   * repeater sortable
   * @since 2.1
   */
  jQuery('.repeater-sortable').sortable(); 
  
  /**
   * repater Field
   * @since 1.0.0
   */
  //edit
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-re-toggle",function() {

    if( jQuery(this).prev().is(':visible') ) {
      jQuery(this).prev().hide();
    } else {
      jQuery(this).prev().show();
    }
  });
  
  
  /**
   * Datepicker Field.
   *
   * @since 1.0.0
   */
  jQuery('.wpw-auto-poster-meta-date').each( function() {
    
    var jQuerythis  = jQuery(this),
        format = jQuerythis.attr('rel');

    jQuerythis.datepicker( { showButtonPanel: true, dateFormat: format } );
    
  });

  /**
   * Timepicker Field.
   *
   * @since 1.0.0
   */
  jQuery('.wpw-auto-poster-meta-time').each( function() {
    
    var jQuerythis   = jQuery(this),
          format   = jQuerythis.attr('rel'),
          aampm    = jQuerythis.attr('data-ampm');
      if ('true' == aampm)
        aampm = true;
      else
        aampm = false;

      jQuerythis.timepicker( { showSecond: true, timeFormat: format, ampm: aampm } );
    
  });

  /**
   * Colorpicker Field.
   *
   * @since 1.0.0
   * better handler for color picker with repeater fields support
   * which now works both when button is clicked and when field gains focus.
   */
  if (jQuery.farbtastic){//since WordPress 3.5
    jQuery( document ).on( "focus", ".wpw-auto-poster-meta-color",function() {
      wpw_auto_poster_load_colorPicker(jQuery(this).next());
    });

    jQuery( document ).on( "focusout", ".wpw-auto-poster-meta-color",function() {
      wpw_auto_poster_hide_colorPicker(jQuery(this).next());
    });

    /**
     * Select Color Field.
     *
     * @since 1.0.0
     */
    jQuery( document ).on( "click", ".wpw-auto-poster-meta-color-select",function() {
      if (jQuery(this).next('div').css('display') == 'none')
        wpw_auto_poster_load_colorPicker(jQuery(this));
      else
        wpw_auto_poster_hide_colorPicker(jQuery(this));
    });

    function wpw_auto_poster_load_colorPicker(ele){
      colorPicker = jQuery(ele).next('div');
      input = jQuery(ele).prev('input');

      jQuery.farbtastic(jQuery(colorPicker), function(a) { jQuery(input).val(a).css('background', a); });

      colorPicker.show();
    }

    function wpw_auto_poster_hide_colorPicker(ele){
      colorPicker = jQuery(ele).next('div');
      jQuery(colorPicker).hide();
    }
    //issue #15
    jQuery('.wpw-auto-poster-meta-color').each(function(){
      var colo = jQuery(this).val();
      if (colo.length == 7)
        jQuery(this).css('background',colo);
    });
  }else{
    
    if ( jQuery('.wpd-mb-meta-color-iris').length  ) {
      
    jQuery('.wpd-mb-meta-color-iris').wpColorPicker();
    }
    
  }

  jQuery('._wpweb_validate-video').on('blur', function() {

        if(!jQuery(this).hasClass('_wpweb_fb_post_reel')){
            
            var imageUrl = jQuery(this).val();
            var validVideoExtensions = ['mp4', 'avi', 'mov', 'webm', 'mkv'];

            // Extract the extension from the URL
            var extension = imageUrl.split('.').pop().toLowerCase();
            if (jQuery(this).val() != '') {
            // Check if the URL ends with a valid image extension
            if (validVideoExtensions.includes(extension)) {
                // Enable save button and remove any error messages
                jQuery('#publish').removeAttr('disabled');
                jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
                jQuery(this).css('border', '');
            } else {
                // Disable the Publish/Save button and show an error message
                jQuery('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
                jQuery('#publish').attr('disabled', 'disabled');
                if (!jQuery('.image-error').length) {
                    jQuery(this).css('border', '2px solid red');
                }
            }
            }
        }
  });

  // Re-enable the button if the input field is empty
  jQuery(document).on('input', '._wpweb_validate-video', function() {
      if (jQuery(this).val() === '') {
        jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
        jQuery(this).css('border', '');
      }
  });
  
  /**
   * Add Files.
   *
   * @since 1.0.0
   */
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-add-file", function() {
    var jQueryfirst = jQuery(this).parent().find('.file-input:first');
    jQueryfirst.clone().insertAfter(jQueryfirst).show();
    return false;
  });
  /*
  *
  * Advanced Add Files
  */
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-add-fileadvanced",function() {
     var jQueryfirst = jQuery(this).parent().find('.file-input-advanced:last');
     jQueryfirst.clone().insertAfter(jQueryfirst).show();
     jQuery(this).parent().find('.file-input-advanced:last .wpw-auto-poster-upload-file-link').val('');
     return false;
   });
   
  /*
   *
   * Advanced Add Files
   */
  jQuery( document ).on( "click", ".wpw-auto-poster-delete-fileadvanced",function() {
    var row = jQuery(this).parent().parent().parent( 'tr' );
    var count = row.find('.file-input-advanced').length;
      if(count > 1) {
       jQuery(this).parent('.file-input-advanced').remove();
      } else {
        alert( WpwAutoPosterMeta.one_file_min );
      }
     return false;
   });
   
   // WP 3.5+ uploader
  
    jQuery( document ).on( "click", ".wpw-auto-poster-upload-fileadvanced",function(e) {

    e.preventDefault();
    
    if(typeof wp == "undefined" || WpwAutoPosterMeta.new_media_ui != '1' ){// check for media uploader
        
      //Old Media uploader
        
      window.formfield = '';
      e.preventDefault();
      
      window.formfield = jQuery(this).closest('.file-input-advanced');
      
      tb_show('', 'media-upload.php?post_id='+ jQuery('#post_ID').val() + '&type=image&amp;TB_iframe=true');
          //store old send to editor function
          window.restore_send_to_editor = window.send_to_editor;
          //overwrite send to editor function
          window.send_to_editor = function(html) {
            attachmenturl = jQuery('a', '<div>' + html + '</div>').attr('href');
            window.formfield.find('.wpw-auto-poster-upload-file-link').val(attachmenturl);
          
            wpw_auto_poster_load_images_muploader();
            tb_remove();
            //restore old send to editor function
            window.send_to_editor = window.restore_send_to_editor;
          }
        return false;
          
    } else {
      
      var file_frame;
      window.formfield = '';
      
      //new media uploader
      var button = jQuery(this);
  
      window.formfield = jQuery(this).closest('.file-input-advanced');
    
      // If the media frame already exists, reopen it.
      if ( file_frame ) {
        file_frame.open();
        return;
      }
  
      // Create the media frame.
      file_frame = wp.media.frames.file_frame = wp.media({
        frame: 'post',
        state: 'insert',
        title: button.data( 'uploader_title' ),
        button: {
          text: button.data( 'uploader_button_text' ),
        },
        multiple: true  // Set to true to allow multiple files to be selected
      });
  
      file_frame.on( 'menu:render:default', function(view) {
            // Store our views in an object.
            var views = {};
  
            // Unset default menu items
            view.unset('library-separator');
            view.unset('gallery');
            view.unset('featured-image');
            view.unset('embed');
  
            // Initialize the views in our view object.
            view.set(views);
        });
  
      // When an image is selected, run a callback.
      file_frame.on( 'insert', function() {
  
        // Get selected size from media uploader
        var selected_size = $('.attachment-display-settings .size').val();
        
        var selection = file_frame.state().get('selection');
        selection.each( function( attachment, index ) {
        attachment = attachment.toJSON();
          
        // Selected attachment url from media uploader
        var attachment_url = attachment.sizes[selected_size].url;
          
          if(index == 0){
            // place first attachment in field
            window.formfield.find('.wpw-auto-poster-upload-file-link').val(attachment_url);
            
          } else{
            
            window.formfield.find('.wpw-auto-poster-upload-file-link').val(attachment_url);
            
          }
        });
      });
  
      // Finally, open the modal
      file_frame.open();
    }
    
  });

  /**
   * Delete File.
   *
   * @since 1.0.0
   */
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-upload .wpw-auto-poster-meta-delete-file",function() {
    
    var jQuerythis   = jQuery(this),
        jQueryparent = jQuerythis.parent(),
        data = jQuerythis.attr('rel');
    
    var ind = jQuery(this).index()
    jQuery.post( ajaxurl, { action: 'atm_delete_file', data: data, tag_id: jQuery('#post_ID').val() }, function(response) {
      response == '0' ? ( alert( 'File has been successfully deleted.' ), jQueryparent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
    });
    
    return false;
  
  });

    
  /**
   * Thickbox Upload
   *
   * @since 1.0.0
   */
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-upload-button", function() {
    
    var data       = jQuery(this).attr('rel').split('|'),
        post_id   = data[0],
        field_id   = data[1],
        backup     = window.send_to_editor; // backup the original 'send_to_editor' function which adds images to the editor
        
    // change the function to make it adds images to our section of uploaded images
    window.send_to_editor = function(html) {
      
      jQuery('#wpw-auto-poster-meta-images-' + field_id).append( jQuery(html) );

      tb_remove();
      
      window.send_to_editor = backup;
    
    };

    // note that we pass the field_id and post_id here
    tb_show('', 'media-upload.php?post_id=' + post_id + '&field_id=' + field_id + '&type=image&TB_iframe=true');

    return false;
  });

    
  /**
   * Helper Function
   *
   * Get Query string value by name.
   *
   * @since 1.0.0
   */
  function get_query_var( name ) {

    var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
      
  }
  
  //new image upload field
  function wpw_auto_poster_load_images_muploader(){
    jQuery(".mupload_img_holder").each(function(i,v){
      if (jQuery(this).next().next().val() != ''){
        if (!jQuery(this).children().size() > 0){
          jQuery(this).append('<img src="' + jQuery(this).next().next().val() + '" style="height: 150px;width: 150px;" />');
          jQuery(this).next().next().next().val("Delete Image");
          jQuery(this).next().next().next().removeClass('wpw-auto-poster-meta-upload_image_button').addClass('wpw-auto-poster-meta-delete_image_button');
        }
      }
    });
  }
  
  wpw_auto_poster_load_images_muploader();
  //delete img button
  
  jQuery( document ).on( "click", ".wpw-auto-poster-meta-delete_image_button",function(e) {
    var btnText = jQuery(this).val();

    jQuery(this).prev().val('');
    jQuery(this).prev().prev().val('');
    jQuery(this).prev().prev().prev().html('');
    
    // Set the URL in the input field
    jQuery('._wpweb_validate-image').val();

    // Validate the selected image URL
    validateImageField('', jQuery('._wpweb_validate-image'));

    if( btnText.includes("video") || btnText.includes("Video") ) {
      jQuery(this).val("Upload Video");
    } else {
      jQuery(this).val("Upload Image");
    }
    
    jQuery(this).removeClass('wpw-auto-poster-meta-delete_image_button').addClass('wpw-auto-poster-meta-upload_image_button');
  });
 
  //editor resize fix
  jQuery(window).resize(function() {
    jQuery.each(Ed_array, function() {
      var ee = this;
      jQuery(ee.getScrollerElement()).width(100); // set this low enough
      width = jQuery(ee.getScrollerElement()).parent().width();
      jQuery(ee.getScrollerElement()).width(width); // set it to
      ee.refresh();
    });
  });
  
  
  // WP 3.5+ uploader
  
  var formfield1;
    var formfield2;
    
    jQuery( document ).on( "click", ".wpw-auto-poster-meta-upload_image_button",function(e) {

    e.preventDefault();
    formfield1 = jQuery(this).prev();
    formfield2 = jQuery(this).prev().prev();
    var button = jQuery(this);
      
    if(typeof wp == "undefined" || WpwAutoPosterMeta.new_media_ui != '1' ){// check for media uploader//
       
        tb_show('', 'media-upload.php?post_id='+ jQuery('#post_ID').val() + '&type=image&amp;TB_iframe=true');
          //store old send to editor function
          window.restore_send_to_editor = window.send_to_editor;
          //overwrite send to editor function
          window.send_to_editor = function(html) {
            
            imgurl = jQuery('img',html).attr('src');
            
            if(jQuery('img',html).attr('class')) {
              
              img_calsses = jQuery('img',html).attr('class').split(" ");
              att_id = '';
              jQuery.each(img_calsses,function(i,val){
                if (val.indexOf("wp-image") != -1){
                  att_id = val.replace('wp-image-', "");
                }
              });
      
              jQuery(formfield2).val(att_id);
            }
            
            jQuery(formfield1).val(imgurl);
            wpw_auto_poster_load_images_muploader();
            tb_remove();
            //restore old send to editor function
            window.send_to_editor = window.restore_send_to_editor;
          }
          return false;
          
    } else {
      
      var file_frame;
      
      // If the media frame already exists, reopen it.
      if ( file_frame ) {
        file_frame.open();
        return;
      }
   
      if( button.attr('rel') == '_wpweb_yt_post_image' ||  button.attr('rel') == '_wpweb_insta_post_reel' || button.attr('rel') == '_wpweb_fb_post_reel'){

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          frame: 'post',
          state: 'insert',
          title: button.data( 'uploader_title' ),
          button: {
            text: button.data( 'uploader_button_text' ),
          },
          multiple: false,  // Set to true to allow multiple files to be selected
          library: {
            type: [ 'video']
          },
        });
      } else{

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          frame: 'post',
          state: 'insert',
          title: button.data( 'uploader_title' ),
          button: {
            text: button.data( 'uploader_button_text' ),
          },
          multiple: false,  // Set to true to allow multiple files to be selected
          library: {
            type: [ 'image']
          },
        });
      }
      
      file_frame.on( 'menu:render:default', function(view) {
            // Store our views in an object.
            var views = {};
  
            // Unset default menu items
            view.unset('library-separator');
            view.unset('gallery');
            view.unset('featured-image');
            view.unset('embed');
  
            // Initialize the views in our view object.
            view.set(views);
        });
  
      // When an image is selected, run a callback.
      file_frame.on( 'insert', function() {
        
        // Get selected size from media uploader
        var selected_size = jQuery('.attachment-display-settings .size').val();
        

        var selection = file_frame.state().get('selection');
        selection.each( function( attachment, index ) {
          attachment = attachment.toJSON();

          // Selected attachment url from media uploader
          if( typeof selected_size !== 'undefined' ){
            var attachment_url = attachment.sizes[selected_size].url;
           } else{
             var attachment_url = attachment.url;
           }
           console.log(attachment.filesizeInBytes);
        
          
          if( ( attachment.type == 'image' && button.attr('rel') != '_wpweb_yt_post_image' ) || ( attachment.type == 'video' && button.attr('rel') == '_wpweb_yt_post_image' ) || ( attachment.type == 'video' && button.attr('rel') == '_wpweb_insta_post_reel' ) || ( attachment.type == 'video' && button.attr('rel') == '_wpweb_fb_post_reel' )  ) {
              if(index == 0){
                // place first attachment in field
                jQuery(formfield2).val(attachment.id);
                    jQuery(formfield1).val(attachment_url);
                    wpw_auto_poster_load_images_muploader();
                    // Set the URL in the input field
                    jQuery('._wpweb_validate-video').val(attachment_url);

                    // Validate the selected image URL
                    validateVideoField(attachment_url, jQuery('._wpweb_validate-video'));

                    if(button.attr('rel') == '_wpweb_fb_post_reel' ){

                        var maxFileSize = 20 * 1024 * 1024; // 5MB in bytes
                        if (attachment.filesizeInBytes > maxFileSize) {
                            
                            jQuery('._wpweb_fb_post_reel').css('border', '2px solid red');
                            jQuery('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
                            // Disable the save button
                            jQuery('#publish').attr('disabled', true); // Disable for post/page editor
                            // Add an error message in the last position of the parent <td> if not already present
                            if (!jQuery('._wpweb_fb_post_reel').closest('td').find('.img-error').length) {
                                jQuery('._wpweb_fb_post_reel').closest('td').append('<p class="img-error" style="color: red;">Please upload the video size less than 20 MB.</p>');
                            }
                        } else {
                            // Remove red border if URL is valid
                            jQuery('._wpweb_fb_post_reel').css('border', '');
                            // Enable the save button
                            jQuery('#publish').attr('disabled', false); // Enable for post/page editor
                            jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
                            // Remove error message if it exists
                            jQuery('._wpweb_fb_post_reel').closest('td').find('.img-error').remove();
                        }
                    }
                    
              } else{
                
                jQuery(formfield2).val(attachment.id);
                    jQuery(formfield1).val(attachment_url);
                    wpw_auto_poster_load_images_muploader();

                    // Set the URL in the input field
                    jQuery('._wpweb_validate-image').val(attachment_url);

                    // Validate the selected image URL
                    validateImageField(attachment_url, jQuery('._wpweb_validate-image'));
              }
            }
        });
        jQuery('#publish').attr('disabled', false); // Enable for post/page editor
        jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
      });
      
      // Finally, open the modal
      file_frame.open();
    }
    
  });
  
  
  //added for tabs in metabox
  // tab between them
  jQuery('.metabox-tabs li a').each(function(i) {
    var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');
    
    if ( 'active' != jQuery(this).attr('class') )
      jQuery('div.' + thisTab).hide();

    jQuery('div.' + thisTab).addClass('tab-content');

    jQuery(this).on( "click", function() { 
      // hide all child content
      jQuery(this).parent().parent().parent().children('div').hide();
 
      // remove all active tabs
      jQuery(this).parent().parent('ul').find('li.active').removeClass('active');
 
      // show selected content
      jQuery(this).parent().parent().parent().find('div.'+thisTab).show();
      jQuery(this).parent().parent().parent().find('li.'+thisTab).addClass('active');
    });
  });

  jQuery('.metabox-tabs').show();
  
  
});

/**
 * Chosen enable function
 * @since 2.9.8
 */
function wpwAutoPosterFancySelect(){
  jQuery(".wpw-auto-poster-metabox-wrapper select").each(function (){
      if( jQuery(this).attr('id') == '_wpweb_yt_user_id' ){
        jQuery(this).css('width','500px').chosen({search_contains:true,width: '500px'});
      }else{
        jQuery(this).css('width','300px').chosen({search_contains:true,width: '300px'});
      }
  });
}

// Function to validate image URLs
function isValidImageURL(url) {
  var imgextenstion = WpwAutoPosterMetaBox.imgwithpipe;
  return(url.match('/\.('+ imgextenstion + ')$/') != null);
}

// Function to validate video URLs
function isValidVideoURL(url) {
  var videoextenstion = WpwAutoPosterMetaBox.videowithpipe;
  return (url.match('/\.(' + videoextenstion + ')$/i') != null);
}

// Function to validate image URL and add/remove border accordingly
function validateImageField(imageUrl, $inputField) {
  if (!isValidImageURL(imageUrl) && imageUrl != '') {
      // Add red border if URL is not valid
      $inputField.css('border', '2px solid red');
      jQuery('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
      // Disable the save button
      jQuery('#publish').attr('disabled', true); // Disable for post/page editor
      // Add an error message in the last position of the parent <td> if not already present
      if (!$inputField.closest('td').find('.img-error').length) {
        $inputField.closest('td').append('<p class="img-error" style="color: red;">Please upload the valid image format.</p>');
      }
  } else {
      // Remove red border if URL is valid
      $inputField.css('border', '');
      // Enable the save button
      jQuery('#publish').attr('disabled', false); // Enable for post/page editor
      jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
      // Remove error message if it exists
      $inputField.closest('td').find('.img-error').remove();
  }
}

// Function to validate image URL and add/remove border accordingly
function validateVideoField(mediaUrl, $inputField) {
  if (!isValidVideoURL(mediaUrl)) {
      // Add red border if URL is not valid
      $inputField.css('border', '2px solid red');
      jQuery('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
      // Disable the save button
      jQuery('#publish').attr('disabled', true); // Disable for post/page editor
      // Add an error message in the last position of the parent <td> if not already present
      if (!$inputField.closest('td').find('.video-error').length) {
        $inputField.closest('td').append('<p class="video-error" style="color: red;">Please upload the valid video format.</p>');
      }
  } else {
      // Remove red border if URL is valid
      $inputField.css('border', '');
      // Enable the save button
      jQuery('#publish').attr('disabled', false); // Enable for post/page editor
      jQuery('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
      // Remove error message if it exists
      $inputField.closest('td').find('.video-error').remove();
  }
}