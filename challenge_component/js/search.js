/**
 * Search page js.
 */
(function ($, Drupal, drupalSettings) {
  if (document.querySelector('.left-filters .facets-widget-checkbox') !== null) {
    $(".left-filters .facets-widget-checkbox").click(function() {
      var isClass = $(this).hasClass("visible");
      $('.left-filters .facets-widget-checkbox').removeClass('visible');
      if(isClass){
      $(this).removeClass("visible");
      }else{
      $(this).addClass("visible");
      }
    });
  } 

  $(document).ready(function(){
    $('.page-view-main-search .view-header .display_result_count').append("<div class='mobile-filter'>Filter</div>");
    $('.page-view-main-search .view-header').click(function() {
      $(this).siblings('.all-filter-wrapper').addClass('filter-active');
    });
    $('.page-view-main-search .views-exposed-form').append("<div class='form-close'></div>")
    $('.page-view-main-search .views-exposed-form .form-close').click(function() {
      $('.all-filter-wrapper').removeClass('filter-active');
    });
  });

})(jQuery, Drupal, drupalSettings);