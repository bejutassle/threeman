var PjaxInit = {


    init: function(container, scrollto, cache, timeout) {

    if($.support.pjax){

      $.pjax.defaults.scrollTo = scrollto;
      $.pjax.defaults.timeout = timeout;
      $.pjax.defaults.maxCacheLength = cache;
      NProgress.configure({
        parent: 'body',
        showSpinner: false
      });

/*
* Select is defined pjax element
 */
      $(document).pjax('a[data-pjax!="false"]', {
        container: container,
        fragment: container
      });

/**
 * Ajax Send and Stop change Cursor status
 */

        $(document).ajaxStart(function() {
            $('[data-xhr-load!="false"]').css({'cursor' : 'wait'});
            NProgress.inc();
        }).ajaxStop(function() {
            $('[data-xhr-load!="false"]').css({'cursor' : ''});
            NProgress.done();
        });

/**
 * Page Send
 */
      $(document).on('pjax:send', function(e) {
            $('[data-xhr-load!="false"]').css({'cursor' : 'wait'});
            e.preventDefault();
            e.stopImmediatePropagation();
      });


/**
 * Page Loaded Complete
 */
      $(document).on('pjax:complete', function(e) {
            $('[data-xhr-load!="false"]').css({'cursor' : ''});
            window.onload();
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * Page Loaded Start
 */
      $(document).on('pjax:start', function(e) {
            NProgress.inc();
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * Page Loaded End
 */
      $(document).on('pjax:end', function(e) {
            NProgress.done();
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * Pjax Loading Success
 */
      $(document).on('pjax:success', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * Pjax Loading Timeout
 */
      $(document).on('pjax:timeout', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * All Scripts Reloaded
 */
      $(document).on('ready pjax:end', function(e) {
            $(e.target);
            e.preventDefault();
            e.stopImmediatePropagation();
      });

    }


    },

    reload: function (url, scroll) {
            if (!scroll) scroll = false;
            if(scroll == true){
              var setScroll = 1;
            }else{
              var setScroll = false;
            }

            $.pjax.reload({
                container: settings.site.container,
                fragment: settings.site.container,
                timeout: 5000,
                maxCacheLength: 0,
                scrollTo: setScroll,
                url: url, 
                async: true
            });

    }


};