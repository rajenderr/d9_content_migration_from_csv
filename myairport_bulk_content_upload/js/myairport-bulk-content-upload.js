(function ($, Drupal, drupalSettings) {
  'use strict';
  Drupal.behaviors.myairport_bulk_content_upload = {
    attach: function(context, settings) {
      var fileValue = $('#edit-nodes-upload-upload').val();
      if(fileValue == undefined){
        $("#edit-submit").prop("disabled", false);
      }
      var file = $('input[data-drupal-selector="edit-nodes-upload-fids"]').val();
      if (file) {
        $("#edit-submit").removeAttr("disabled");
        $("#edit-submit").removeClass("is-disabled");
      }
      if($( ".js-form-managed-file" ).find( ":input" ).hasClass( "js-hide" )) {
        $("#edit-submit").prop("disabled", true);
      }
      
      if($( ".js-form-managed-file" ).find( ":input" ).hasClass( "error" )) {
        $("<div class = 'error-description'>Please upload only csv file</div>" ).insertAfter( ".description" );
        $('.description').next().next().remove();
      }
    },
  };
}(jQuery, Drupal, drupalSettings));