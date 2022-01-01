(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.challenge_component = {
    attach: function(context, settings) {
      $('.submit-photo-button').click(function() {
        $("#popup-photo").removeAttr("style");
        $("#popup-video").removeAttr("style");
      });
      $('.add-video-challenge').click(function() {
        $('.field--name-field-select-image').hide();
        $('#field_select_video-media-library-wrapper').show();
        $('#edit-body-0-format--3').hide();
      });
      $('.add-photo-challenge').click(function() {
        $('#field_select_video-media-library-wrapper').hide();
        $('.field--name-field-select-image').show();
      });
      $(document).on("click", ".add-photo", function(e) {
        $('.field--name-field-select-video').hide();  
        $('.field--name-field-select-image').show();
      });
      $(document).on("click", ".add-video", function(e) {
        $('.field--name-field-select-image').hide();
        $('.field--name-field-select-video').show();  
      });
      var timerData = jQuery( "#jquery-countdown-timer" ).text();
      if (timerData == 0) {
        var linkText = 'submittions have closed!';
        jQuery(".submit-photo-button").hide();
        jQuery('.submit-photo-subtitle2').html(linkText);
      }

      var timerData = jQuery( "#jquery-countdown-timer-voting" ).text();
      if (timerData == 0) {
        var linkText = 'Voting have been closed!';
        jQuery(".submit-vote-button-voting").hide();
        jQuery('.submit-photo-subtitle2-voting').html(linkText);
      }
      //Auto select not publish for submittions page.
      $("#edit-field-result-status, #edit-field-rating, #edit-field-label, #edit-field-regional-label ").change(function() {
            selectedValue = $('#edit-field-publish').val('0');
      });

      $('.media-load-more').unbind().click(function() {
        var nid = $(this).attr('nid');
        var existingCount = $(this).attr('total-media-items');
        var currentItems = $(this).attr('display-items');
        
        $.ajax({
          type: "POST",
          url: "/get-gallery-items",
          data: { nid: nid, initialCount: settings.no_of_items, existingCount: existingCount, currentItems:currentItems },
          dataType: "json",
          success: function(data) {
              if (parseInt(existingCount) == parseInt(currentItems)) {
                $(this).hide();
              }
              
              if ($.isEmptyObject(data)) {
                $('<span class="no-result">Sorry ! No result found, please try later.</span>').insertAfter($('.media-load-more'));
              } else {
                
                var output = '';
                var recordCount = 0;
                $.each(data, function(key, value) {
                  recordCount++;
                  output += "<div class='card'>"; 
                  var type = value.media_type.split("/");  
                  if (type[0] == 'image') {
                    output += "<img src ='"+value.media_url+"' width='320' height='240'>";
                  }
                  if (type[0] == 'video') {
                    output += '<video width="320" height="240" controls><source src="'+value.media_url+'" type="'+value.media_type+'"></video>';
                  }
                  output += '</div>';
                });
                
                $('.media-load-more').attr('display-items', recordCount);
                $('.gallery-data-wrapper').empty();
                $('.gallery-data-wrapper').html(output);
                $('.result-summary').html("Displaying "+ recordCount +" of "+ existingCount);
                if (parseInt(recordCount) == parseInt(existingCount)) {
                  $('.media-load-more').hide();
                }
              }
          }
      });
      });
    }
  }
}(jQuery, Drupal, drupalSettings));  