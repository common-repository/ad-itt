jQuery(window).load(function() {
  if (AdIttAjax.enable_top_ad_link != "" && AdIttAjax.top_ad_link != "" && AdIttAjax.top_ad_img != "") {
      var close_link = "Close";
      if (AdIttAjax.top_ad_close_img != "") {
        close_link = "<img title='Close' alt='Close' src='" + AdIttAjax.top_ad_close_img + "' />";
      }
      var ad_class = AdIttAjax.top_ad_position;
      jQuery(AdIttAjax.css_content_wrapper_selector).prepend("<div class='"+ad_class+"' id='top_aditt'><a target='_blank' href='" + AdIttAjax.top_ad_link + "'><img src='" + AdIttAjax.top_ad_img + "' /></a> <a id='close_top_aditt_img' href=''>" + close_link + "</a></div>");
  }
  if (AdIttAjax.enable_bottom_ad_link != "" && AdIttAjax.bottom_ad_link != "" && AdIttAjax.bottom_ad_img != "") {
    jQuery(AdIttAjax.css_content_wrapper_selector).append("<a target='_blank' id='bottom_aditt' href='" + AdIttAjax.bottom_ad_link + "'><img src='" + AdIttAjax.bottom_ad_img + "' /></a>");
  }
  if (AdIttAjax.enable_left_ad_link != "" && AdIttAjax.left_ad_link != "" && AdIttAjax.left_ad_img != "") {
    var ad_class = AdIttAjax.left_ad_position;
    jQuery(AdIttAjax.css_content_wrapper_selector).append("<a target='_blank' id='left_aditt' class='" + ad_class + "' href='" + AdIttAjax.left_ad_link + "'><img src='" + AdIttAjax.left_ad_img + "' /></a>");
  }
  if (AdIttAjax.enable_right_ad_link != "" && AdIttAjax.right_ad_link != "" && AdIttAjax.right_ad_img != "") {
    var ad_class = AdIttAjax.right_ad_position;
    jQuery(AdIttAjax.css_content_wrapper_selector).append("<a target='_blank' id='right_aditt' class='" + ad_class + "' href='" + AdIttAjax.right_ad_link + "'><img src='" + AdIttAjax.right_ad_img + "' /></a>");
  }
  jQuery("#top_aditt").width(jQuery(AdIttAjax.css_content_wrapper_selector).css("width"));
  var position = jQuery(AdIttAjax.css_content_wrapper_selector).offset();
  var left = 0;
  if (jQuery("#left_aditt").length > 0) {
    left = parseInt(position.left - 221);
    jQuery("#left_aditt").css("left", left);
  }

  var right = 0;
  if (jQuery("#right_aditt").length > 0) {
    right = parseInt(position.left + parseInt(jQuery(AdIttAjax.css_content_wrapper_selector).css("width")));
    jQuery("#right_aditt").css("left", right);
  }
  if (AdIttAjax.top_ad_retract_time != "" ) {
    jQuery("#top_aditt").delay(AdIttAjax.top_ad_retract_time).slideUp();
  }
  jQuery("#close_top_aditt_img").live("click", function() {
    jQuery("#top_aditt").hide();
    return false;
  });
  jQuery(window).resize(resize_window);
  resize_window();
  function resize_window() {
    var position = jQuery(AdIttAjax.css_content_wrapper_selector).offset();
    var left = 0;
    if (jQuery("#left_aditt").length > 0) {
      left = parseInt(position.left - 221);
      jQuery("#left_aditt").css("left", left);
    }
    var right = 0;
    if (jQuery("#right_aditt").length > 0) {
      right = parseInt(position.left + parseInt(jQuery(AdIttAjax.css_content_wrapper_selector).css("width")));
      jQuery("#right_aditt").css("left", right);
    }
  }
});