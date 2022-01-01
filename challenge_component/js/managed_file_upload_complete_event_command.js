(function ($, Drupal) {

  "use strict";

  /**
   * Add new custom command.
   */
  Drupal.AjaxCommands.prototype.triggerManagedFileUploadComplete = function (ajax, response, status) {
    var files = response.files;
    var make = files.Make;
    var model = files.Model;
    var aperture = files.MaxApertureValue;
    var ISOSpeed = files.ISOSpeedRatings;
    var FocalLength = files.FocalLength;
    var GPSLatitude = files.GPSLatitude;
    var GPSLongitude = files.GPSLongitude;
    var ExposureTime = files.ExposureTime;
    if (model) {
      $('#edit-field-body-0-target-id').val(model);
    }
    if (ISOSpeed) {
      $('#edit-field-iso-media-0-value').val(ISOSpeed);
      $('#edit-field-iso-0-value').val(ISOSpeed);
    }
    if (aperture) {
      $('#edit-field-aperture-0-value').val(aperture);  
    }
    
    if (FocalLength) {
      $('#edit-field-focal-length-media-0-value').val(FocalLength);
      $('#edit-field-focal-length-0-value').val(FocalLength);
    }
    if (ExposureTime) {
      $('#edit-field-exposure-media-0-value').val(ExposureTime);
    }
    var Latitude = files.GPSLatitude.split("'").pop().split("\'")[0].split('"')[0];
    var Longitude = files.GPSLongitude.split("'").pop().split("\'")[0].split('"')[0];
    
    
    $.get({ url: 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+Latitude+','+Longitude+'&sensor=false&key=AIzaSyDj6bLDLDuaqOFGju6QujSUJa47Hzzwvgc', success(data) {
      $('.geolocation-geocoder-address').val(data.results[0].formatted_address);
    }});
  };

}(jQuery, Drupal));