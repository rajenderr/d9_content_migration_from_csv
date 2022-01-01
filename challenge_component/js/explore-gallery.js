(function ($, Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.explore_gallery_component = {
    attach: function(context, settings) {
      

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
                  /*output += "<div class='card'>"; 
                  var type = value.media_type.split("/");  
                  if (type[0] == 'image') {
                    output += "<img src ='"+value.media_url+"' width='320' height='240'>";
                  }
                  if (type[0] == 'video') {
                    output += '<video width="320" height="240" controls><source src="'+value.media_url+'" type="'+value.media_type+'"></video>';
                  }
                  output += '</div>';*/
                    
                    
                    
                output += '<div class="card"><div class="col-submission_item"><div class="sub_image_wrap"><div class="modal-show" data-bs-toggle="modal" data-bs-target="#popup-content-'+value.mid+'">'; 
                var type = value.media_type.split("/");  
                if (type[0] == 'image') {
                    output += "<img src ='"+value.media_url+"' width='320' height='240'>";
                }
                if (type[0] == 'video') {
                    output += '<video width="320" height="240" controls><source src="'+value.media_url+'" type="'+value.media_type+'"></video>';
                }
                if (type[0] == 'remote_video') {
                output += '<iframe width="320" height="240" src="'+value.media_url+'"></iframe>';
                }
                output += '</div>';
var media_popup_id = "popup-content-" + value.mid;
 output += `<div class="modal fade gallery_overlay_model" id="popup-content-` + value.mid +`" tabindex="-1" aria-labelledby="galleryModalPopup" aria-hidden="true"><div class='modal-dialog modal-fullscreen'>`;
  output +=        `<div class="modal-content">
            <div class="modal-header">
              <a class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
            </div>
              <div class="modal-body">
            <div class="challenge_overlay_voted_body">
              <div class="row">
           
                <div class="gallery_gallery_slider">
                  <div class="gallery_image">`;
                    
					 if (type[0] == 'image') {
						output += "<img src ='"+value.media_url+"' width='320' height='240'>";
						}
						if (type[0] == 'video') {
						output += '<video width="320" height="240" controls><source src="'+value.media_url+'" type="'+value.media_type+'"></video>';
						}
						if (type[0] == 'remote_video') {
						output += '<iframe width="320" height="240" src="'+value.media_url+'"></iframe>';
						}
                 output += `</div>
                                             
               
                </div>
                <div class="Photo_details_wrap">
                  <div class="photo_title_wrap">
                    <div class="title_location_w">  
                      <div class="photo_title"><h2>' + value.pro_name + '</h2></div>`;
                      if  (!empty(value.media_location)) {
					   output += '<div class="photo_location"><i class="bi bi-geo-alt-fill"></i>' + value.media_location + '</div>';
					   }
                    output += `</div>
                                                      <div class="share_like_wrapper">
                    <div class="a2a_kit a2a_kit_size_32 a2a_default_style share_link_wrap">
        <a class="a2a_dd addtoany_share"><i class="bi bi-share-fill"></i>Share</a>
</div>
                                                      
                                                </div>
                  </div>
                  <div class="photo_technical_detail_wrap">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="photographer_w">
                          <div class="user_img"><figure><img src="`+ value.pro_image +`"></figure></div>
                                                                                <div class="user_name_wrap">`;
 if  (!empty(value.pro_name)) {
                      output += '<div class="user_tag">AMBASSADOR</div>';
}
                      output += `<div class="user_name">`+ value.pro_name +` </div>
                  </div>
                          
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="photo_tech_w">
                          <div class="photo_tech_row">`;
                             if  (!empty(value.media_body)) {
                            output += `<div class="photo_tech_detail"><h4 class="tech_title">BODY</h4>
                              <p class="tech_d">` + value.media_body + `</p>
                            </div>`;
                            }

                             if (!empty(value.media_aperture)) {
                            output += `<div class="photo_tech_detail">
                              <h4 class="tech_title">APERTURE</h4>
                              <p class="tech_d">` + value.media_aperture + `</p>
                            </div>`;
                            }

                            if (!empty(value.media_exposure)) {
                            output += `<div class="photo_tech_detail">
                              <h4 class="tech_title">EXPOSURE</h4>
                              <p class="tech_d">` + value.media_exposure + `</p>
                            </div>`;
                          
                            }

                            if (!empty(value.media_lens)) {
                            output += `<div class="photo_tech_detail">
                              <h4 class="tech_title">LENS</h4>
                              <p class="tech_d">` + value.media_lens + `</p>
                            </div>`;
                            }

                            if (!empty(value.media_focal_length)) {
                            output += `<div class="photo_tech_detail">
                              <h4 class="tech_title">FOCAL LENGTH</h4>
                              <p class="tech_d">` + value.media_focal_length + `</p>
                            </div>`;
                            }

                            if (!empty(value.media_iso)) {
                            output += `<div class="photo_tech_detail">
                              <h4 class="tech_title">ISO</h4>
                              <p class="tech_d">` + value.media_iso + `</p>
                            </div>`;
                            }
                          output += `</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
                     
    </div>
    <div class="hover_content">
        <div class="expand_btn" data-bs-toggle="modal" data-bs-target="#popup-content-` + value.mid +`">Expand</div>
        <div class="hover_description ">
            <div class="submission_title">` + value.pro_name + value.media_name + `</div>`;
             if  (!empty(value.media_body)) {
            output += '<div class="camera_body"><h2>BODY</h2><p>' + value.media_body + '</p></div>';
            }
            if  (!empty(value.media_lens)) {
            output += '<div class="camera_lens"><h2>LENS</h2><p>' + value.media_lens + '</p></div>';
            }
        output += `</div>
        <div class="user_details">`;
             if (!empty(value.pro_name)) {
                output += `<div class="user_picture">`+ value.pro_name;
                     if  (!empty(value.pro_image)) {
                        output += '<img src="/themes/custom/sony_alphauniverse/images/no_thumbnail.jpg" class="unavailable-thumbnail">';
                    }
                output += `</div>
                <div class="user_name_wrap">
                    <div class="user_tag">AMBASSADOR</div>
                    <div class="user_name">` + value.pro_name + `</div>
                </div>`;
            }
       output += `</div>
    </div>
</div>           
    </div>`;

   
                  
                  
                  
                  
                  
                  
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
      
      function empty(str) {
        if (typeof str == 'undefined' || !str || str.length === 0 || str === "" || !/[^\s]/.test(str) || /^\s*$/.test(str) || str.replace(/\s/g,"") === "")
            return true;
        else
            return false;
      }
    }
  }
}(jQuery, Drupal, drupalSettings));  