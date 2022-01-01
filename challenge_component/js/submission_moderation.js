(function ($, Drupal, drupalSettings) {
  'use strict';
  Drupal.behaviors.submission_moderation = {
    attach: function (context, settings) {
      $('.submission-moderation-result-status').on('change', function(e) {
        if (e.handled !== true) {
          e.handled = true;
          var element = $(this).find('option:selected'); 
          var nid = element.attr("nid");
          var fieldValue = element.val();
          var field = "field_result_status";
          $.ajax({
            type: "POST",
            url: "/update-submissoion-moderation",
            data: { nid: nid, field: field, value: fieldValue},
            dataTyajaxpe: "json",
            success: function(data) {
              $('#record-'+nid).hide();
            }
          });
        }
      }); 

      $('.submission-moderation-rating').on('change', function(e) {
        if (e.handled !== true) {
          e.handled = true;
          var element = $(this).find('option:selected'); 
          var nid = element.attr("nid");
          var fieldValue = element.val();
          var field = "field_rating";
          $.ajax({
            type: "POST",
            url: "/update-submissoion-moderation",
            data: { nid: nid, field: field, value: fieldValue},
            dataTyajaxpe: "json",
            success: function(data) {
              
            }
          });
        }
      });

      $('.submission-moderation-publish').on('change', function(e) {
        if (e.handled !== true) {
          e.handled = true;
          var element = $(this).find('option:selected'); 
          var nid = element.attr("nid");
          var fieldValue = element.val();
          var field = "field_publish";
          $.ajax({
            type: "POST",
            url: "/update-submissoion-moderation",
            data: { nid: nid, field: field, value: fieldValue},
            dataTyajaxpe: "json",
            success: function(data) {
              
            }
          });
        }
      });

      $('.submission-moderation-label').on('change', function(e) {
        if (e.handled !== true) {
          e.handled = true;
          var element = $(this).find('option:selected'); 
          var nid = element.attr("nid");
          var fieldValue = element.val();
          var field = "field_label";
          $.ajax({
            type: "POST",
            url: "/update-submissoion-moderation",
            data: { nid: nid, field: field, value: fieldValue},
            dataTyajaxpe: "json",
            success: function(data) {
              
            }
          });
        }
      });

      $('.submission-moderation-regional-label').on('change', function(e) {
        if (e.handled !== true) {
          e.handled = true;
          var element = $(this).find('option:selected'); 
          var nid = element.attr("nid");
          var fieldValue = element.val();
          var field = "field_regional_label";
          $.ajax({
            type: "POST",
            url: "/update-submissoion-moderation",
            data: { nid: nid, field: field, value: fieldValue},
            dataTyajaxpe: "json",
            success: function(data) {
              
            }
          });
        }
      });
    }
  } 
}(jQuery, Drupal, drupalSettings));  
