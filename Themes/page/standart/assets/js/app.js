window.onload = function() {

if('ga' in window) {
  ga(function() {
        var trackers = ga.getAll()[0];
        if(trackers){
            trackers.set('page', window.location.pathname);
            trackers.send('pageview');
        }
  });
}

var storage = $.localStorage;
$.cookie.defaults = {expires: 365, path: settings.site.path};
$(':text').attr('autocomplete','total'+Math.random(0,100000000));

/*
* Not use pjax selector
 */
$('[href^="#"]').attr("data-pjax", "false");
$('[target^="_blank"]').attr("data-pjax", "false");

$('[href^="#"]').click(function(event) {
      $('[href^="#"]').attr("data-pjax", "false");
});
/*
* External Link target blank
 */
  $("a").filter(function () {
      return this.hostname && this.hostname !== location.hostname;
  }).each(function () {
      $(this).attr({
          target: "_blank"
      });
  });

$.cookie('previousPage', window.location.pathname, {path:"/"});
settings['previousPage'] = $.cookie('previousPage');

PjaxFormInit.init('[data-form="normal"]');
FormInit.init('form[data-form="true"]');
ModalActionInit.init('[data-modal="true"]');
FormVisilibleInit.init('[data-main-collapse]');

/**
 * LazyLoadingImage
 */
$('img[data-img="lazy"]').lazyload();

/*
Sweet Alert 2
 */
swal.setDefaults({
  buttonsStyling: false,
  animation: false,
  confirmButtonClass: 'btn btn-danger',
  cancelButtonClass: 'btn btn-secondary',
  cancelButtonText: 'Cancel',
});
/*
Toastr
 */
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "0",
  "hideDuration": "0",
  "timeOut": "2000",
  "extendedTimeOut": "1000",
};

/**
 * Active list style to url
 */
$('ul[role="tablist"] > li > a[href!="'+window.location.href+'"]').parents('li').removeClass('active');
$('ul[role="tablist"] > li > a[href="'+window.location.href+'"]').parents('li').addClass('active');


$(document).find('[type="text/nanoscript"]').each(function() {
      eval($(this).html());
});

};