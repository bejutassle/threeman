var PjaxInit = {


    init: function(container, scrollto, cache, timeout) {

    if($.support.pjax){

      $.pjax.defaults.scrollTo = scrollto;
      $.pjax.defaults.timeout = timeout;
      $.pjax.defaults.maxCacheLength = cache;
      NProgress.configure({
        parent: 'body',
        showSpinner: false,
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
      $(document).on('pjax:send', function(e, xhr, options) {
            $('[data-xhr-load!="false"]').css({'cursor' : 'wait'});
      });


/**
 * Page Loaded Complete
 */
      $(document).on('pjax:complete', function(e, xhr, textStatus, options) {
            $('[data-xhr-load!="false"]').css({'cursor' : ''});
      });

/**
 * Page Loaded Start
 */
      $(document).on('pjax:start', function(e, xhr, options) {
            NProgress.inc();
            $('[data-loader="true"]').show();
      });

/**
 * Page Loaded End
 */
      $(document).on('pjax:end', function(e, xhr, options) {
            NProgress.done();
            $('[data-loader="true"]').hide();
      });

/**
 * Pjax Loading Success
 */
      $(document).on('pjax:success', function(e, data, status, xhr, options) {

      });

/**
 * Pjax Loading Timeout
 */
      $(document).on('pjax:timeout', function(e, xhr, options) {
            e.preventDefault();
            e.stopImmediatePropagation();
      });

/**
 * Pjax Popstate Completed
 */
      $(document).on('pjax:popstate', function() {
        
      });

/**
 * All Scripts Reloaded
 */
      $(document).on('ready pjax:end', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.onload();
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