CRM.$(function ($) {
  // Add an "open in new window" tag to the DonorSearch action summary link.
  $('.crm-action-ds-profile a').attr("target", "_blank");
  // Also a tooltip to meet accessibility guidelines for opening in new window.
  $('.crm-action-ds-profile a').after('<span class="ol">Opens in New Window</span>');
  $('.crm-action-ds-profile .ol').css({"display":"none"});
  $('.crm-action-ds-profile a').hover(function() {
    $('.crm-action-ds-profile .ol').css({"display":"block","float":"right","background":"pink"});
  },
  function() {
    $('.crm-action-ds-profile .ol').css({"display":"none"});
  });
});
