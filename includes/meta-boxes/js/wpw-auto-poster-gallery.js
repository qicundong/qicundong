'use strict';

jQuery(function($) {

// This is used to upload more than one custom images when selecting image posting for facebook at post level

  var file_frame;

  $(document).on('click', '#wpw_auto_poster_meta a.gallery-add', function(e) {

    e.preventDefault();

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: true,
      library: {
        type: 'image' // Only allow images
      }
    });

    file_frame.on('select', function() {
      var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list li:last')),
          selection = file_frame.state().get('selection');
      var index;

      selection.map(function(attachment, i) {
        attachment = attachment.toJSON(),
        index      = listIndex + (i + 1);
        var src_url = "";
        if( attachment.sizes.thumbnail ){
          src_url = attachment.sizes.thumbnail.url;
        } else{
          src_url = attachment.url;
        }
        
        $('#gallery-metabox-list').append('<li><input type="hidden" name="wpw_auto_poster_gallery[]" value="' + attachment.id + '"><span class="image-container"><img class="image-preview" src="' + src_url + '"></span><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>');
      });

      // Set the URL in the input field
      $('._wpweb_validate-image').val(src_url);

      // Validate the selected image URL
      validateImageField(src_url, $('._wpweb_validate-image'));

    });

    makeSortable();
    
    file_frame.open();

  });

  $(document).on('blur', '._wpweb_validate-image', function() {
      var imageUrl = $(this).val();
      var validImageExtensions = WpwAutoPosterGallery.imgarray;
      
      // Extract the extension from the URL
      var extension = imageUrl.split('.').pop().toLowerCase();
      if ($(this).val() != '') {
        // Check if the URL ends with a valid image extension
        if (validImageExtensions.includes(extension)) { 
            // Enable save button and remove any error messages
            $('#publish').removeAttr('disabled');
            $('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
            $(this).css('border', '');
            // Remove error message if it exists
            $(this).closest('td').find('.img-error').remove();
        } else {
            // Disable the Publish/Save button and show an error message
            $('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
            $('#publish').attr('disabled', 'disabled');
            if (!$('.image-error').length) {
              $(this).css('border', '2px solid red');
            }
            // Add an error message in the last position of the parent <td> if not already present
            if (!$(this).closest('td').find('.img-error').length) {
              $(this).closest('td').append('<p class="img-error" style="color: red;">Please upload the valid image format.</p>');
            }
        }
      }
  });

  // Re-enable the button if the input field is empty
  $(document).on('input', '._wpweb_validate-image', function() {
      if ($(this).val() === '') {
          $('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
          $(this).css('border', '');
          // Remove error message if it exists
          $(this).closest('td').find('.img-error').remove();
      }
  });


  $('._wpweb_validate-video').on('blur', function() {
    if(jQuery(this).hasClass('_wpweb_fb_post_reel')){
      return false;
    }
    var imageUrl = $(this).val();

    // Get the ID of the current element
    var elementId = $(this).attr('id');
    
    // Check if the ID contains "insta_post_reel"
    if (elementId && elementId.includes("insta_post_reel")) {
      var validVideoExtensions = ['mp4', 'mov'];
    } else {
      var validVideoExtensions = WpwAutoPosterGallery.videoarray;
    }

    // Check if the ID contains "fb_post_reel"
    if (elementId && elementId.includes("fb_post_reel")) {
      var validVideoExtensions = ['mp4', 'mov'];
    } else {
      var validVideoExtensions = WpwAutoPosterGallery.videoarray;
    }

    // Extract the extension from the URL
    var extension = imageUrl.split('.').pop().toLowerCase();
    if ($(this).val() != '') {
      // Check if the URL ends with a valid image extension
      alert(validVideoExtensions);
      if (validVideoExtensions.includes(extension)) {
          // Enable save button and remove any error messages
          $('#publish').removeAttr('disabled');
          $('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
          $(this).css('border', '');
          // Remove error message if it exists
          $(this).closest('td').find('.video-error').remove();
      } else {
          // Disable the Publish/Save button and show an error message
          $('.editor-post-publish-button, .editor-post-save-draft').attr('disabled', 'disabled');
          $('#publish').attr('disabled', 'disabled');
          // Add an error message in the last position of the parent <td> if not already present
          if (!$(this).closest('td').find('.video-error').length) {
            $(this).closest('td').append('<p class="video-error" style="color: red;">Please upload the valid video format.</p>');
          }
          $(this).css('border', '2px solid red');
      }
    }
});

// Re-enable the button if the input field is empty
$(document).on('input', '._wpweb_validate-video', function() {
    if ($(this).val() === '') {
        $('.editor-post-publish-button, .editor-post-save-draft').removeAttr('disabled');
        $(this).css('border', '');
        // Remove error message if it exists
        $(this).closest('td').find('.video-error').remove();
    }
});

  $(document).on('click', '#wpw_auto_poster_meta a.change-image', function(e) {

    e.preventDefault();

    var that = $(this);

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: false,
      library: {
        type: 'image' // Only allow images
      }
    });

    file_frame.on( 'select', function() {
      attachment = file_frame.state().get('selection').first().toJSON();
      that.parent().find('input:hidden').attr('value', attachment.id);
      that.parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
      // Set the URL in the input field
      $('._wpweb_validate-image').val(src_url);

      // Validate the selected image URL
      validateImageField(src_url, $('._wpweb_validate-image'));
    });

    file_frame.open();

  });


  function makeSortable() {
    $('#gallery-metabox-list').sortable({
      opacity: 0.6,
      stop: function() {
      }
    });

    $('#gallery-metabox-list-insta').sortable({
        opacity: 0.6,
        stop: function() {
        }
      });
  }

  $(document).on('click', '#wpw_auto_poster_meta a.remove-image', function(e) {
    e.preventDefault();

    $(this).parents('li').animate({ opacity: 0 }, 200, function() {
      $(this).remove();
    });
  });

  makeSortable();


  	/* Insta Gallery */
      $(document).on('click', '#wpw_auto_poster_meta a.gallery-add-insta', function(e) {

        e.preventDefault();
    
        if (file_frame) file_frame.close();
    
        file_frame = wp.media.frames.file_frame = wp.media({
          title: $(this).data('uploader-title'),
          button: {
            text: $(this).data('uploader-button-text'),
          },
          multiple: true,
          library: {
            type: 'image' // Only allow images
          }
        });
        
        file_frame.on('select', function() {
          var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list-insta li:last')),
              selection = file_frame.state().get('selection');
          var index;
        
          selection.map(function(attachment, i) {
            attachment = attachment.toJSON(),
            index      = listIndex + (i + 1);
            var src_url = "";
            
            if( attachment.sizes.thumbnail ){
                src_url = attachment.sizes.thumbnail.url;
            } else{
            src_url = attachment.url;
            }   
            
            $('#gallery-metabox-list-insta').append('<li><input type="hidden" name="wpw_auto_poster_gallery_insta[]" value="' + attachment.id + '"><span class="image-container-insta"><img class="image-preview" src="' + src_url + '"></span><a class="change-image button button-small" href="#" data-uploader-title="Change image" data-uploader-button-text="Change image">Change image</a><br><small><a class="remove-image" href="#">Remove image</a></small></li>');
          });
        });
 
        makeSortable();
        
        file_frame.open();
    
      });
  	/* Insta Gallery */

});

// Function to validate image URLs
function isValidImageURL(url) {
  var imgextenstion = WpwAutoPosterGallery.imgwithpipe;
  return(url.match('/\.(' + imgextenstion + ')$/') != null);
}

// Function to validate video URLs
// function isValidVideoURL(url) {
//   var videoextenstion = WpwAutoPosterGallery.videowithpipe;
//   alert(videoextenstion)
//   return (url.match('/\.(' + videoextenstion + ')$/i') != null);
// }

// Function to validate video URLs
function isValidVideoURL(url) {
  // Ensure videoextenstion contains valid pipe-separated extensions like "mp4|webm|ogg"
  var videoExtensions = WpwAutoPosterMetaBox.videowithpipe;

  // Properly escape dots in the regular expression and ensure it matches the end of the URL
  var regex = new RegExp('\\.(' + videoExtensions + ')$', 'i');

  // Test the URL against the regular expression
  return regex.test(url);
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