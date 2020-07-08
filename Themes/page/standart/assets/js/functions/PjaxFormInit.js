var PjaxFormInit = {


init: function(element) {

//Pjax Form Post
$(document).on('submit', element, function(e) {
       $method = $(this).attr('method');
       $container = $(this).data('container');
       $replace = $(this).data('replace');

       if($method){
        var formMethod = $method;
       }else{
        var formMethod = 'GET';
       }

       if($container){
        var formContainer = $container;
       }else{
        var formContainer = settings.site.container;
       }

       if($replace == false){
        var formReplace = false;
       }else{
        var formReplace = true;
       }

       $.pjax.submit(event, formContainer, {
        'push': formReplace,
        'replace': formReplace,
        'timeout': 5000,
        'scrollTo': 1,
        'type': formMethod,
        'maxCacheLength': 0
        });

       e.preventDefault();
       e.stopImmediatePropagation();

});

    }


};