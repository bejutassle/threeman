window.onload = function() {

    var storage = $.localStorage;
    $.cookie.defaults = { expires: 365, path: settings.site.path };
    $(':text').attr('autocomplete', 'total' + Math.random(0, 100000000));

    /*
     * Not use pjax selector
     */
    $('[href^="#"]').attr("data-pjax", "false");
    $('[target^="_blank"]').attr("data-pjax", "false");

    $('[href^="#"]').click(function(event) {
        $('[href^="#"]').attr("data-pjax", "false");
    });

    /**
     * Bootstrap Modal Events
     */
    $(window).on('shown.bs.modal', function() {
        $('[data-xhr-load!="false"]').css({ 'cursor': '' });
    });

    /*
     * External Link target blank
     */
    $("a").filter(function() {
        return this.hostname && this.hostname !== location.hostname;
    }).each(function() {
        $(this).attr({
            target: "_blank"
        });
    });

    $('ul[role="tablist"] > li > a').parents('li').removeClass('active');
    $('ul[role="tablist"] > li > a[href="' + window.location.origin + window.location.pathname + '"]').parents('li').addClass('active');

    //$.ajaxSetup({ cache: false});
    $.cookie('previousPage', window.location.pathname, { path: "/" });
    settings['previousPage'] = $.cookie('previousPage');

    //treeview search
    /*new treefilter($('#accordionSidebar'), {
        searcher : $('#sidebar-search-input'),
        multiselect : true,
        expanded: true,
    });*/

    /**
     * Init all scripts
     */
    PjaxFormInit.init('[data-form="normal"]');
    FormInit.init('form[data-form="true"]');
    FormSearchInit.init('[data-form="search"]');
    FormFilterInit.init('[data-form="filter"]');
    FormUpdateInit.init('[data-form="update"]');
    FormToggleInputInit.init('[data-toggle^="password"]');
    FormGeneratorInputInit.init('[data-generator^="password"]');
    FormActionInit.init('[data-action]');
    FormEditableInit.init('[data-editable]');
    FormVisilibleInit.init('[data-main-collapse]');
    FormCountCharInit.init('[data-count="char"]');
    FormLogoutInit.init('[data-logout="true"]');
    FormNestedCheckboxInit.init('[data-nested-checkbox="true"]');
    ModalActionInit.init('[data-modal="true"]');
    SortActionInit.init('[data-form="sortable"]');
    BreadcrumbsActionInit.init('[data-breadcrumb="true"]');
    HistroyBackInit.init('[data-history="back"]');

    /**
     * BS File type
     */
    $('[data-file="true"]').each(function() {
        var $this = $(this),
            options = {
                'input': $this.attr('data-input') !== 'false',
                'htmlIcon': $this.attr('data-icon'),
                'buttonBefore': $this.attr('data-buttonBefore') === 'true',
                'disabled': $this.attr('data-disabled') === 'true',
                'size': $this.attr('data-size'),
                'text': $this.attr('data-text'),
                'btnClass': $this.attr('data-btnClass'),
                'badge': $this.attr('data-badge') === 'true',
                'dragdrop': $this.attr('data-dragdrop') !== 'false',
                'badgeName': $this.attr('data-badgeName'),
                'placeholder': $this.attr('data-placeholder')
            };

        $this.filestyle(options);
    });
    /**
     * Tooltip
     */
    $('body').tooltip({
        selector: 'a[title], button[title], input[title], p[title], img[title], [data-title][title]',
        html: true,
        placement: 'auto'
    });
    if ($('[role="tooltip"]').length > 0) {
        $('[role="tooltip"]').empty();
    }
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
    Icheck
     */
    $('input:not([data-style])').iCheck({
        checkboxClass: 'icheckbox_square-total',
        radioClass: 'iradio_square-total',
        increaseArea: '20%' // optional
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

    //BS Table
    if ($('[data-toggle="table"]').length > 0) {
        $('[data-toggle="table"]').bootstrapTable();
    }

    //Spinner
    if ($('[data-trigger="spinner"]').length > 0) {
        $('[data-trigger="spinner"]').spinner();
    }

    //Colorpicker
    if ($('[data-color-picker="true"]').length > 0) {
        $('[data-color-picker="true"]').colorpicker();
    }

    //Select 2 dropdown
    if ($('select[data-select="true"]').length > 0) {
        $('select[data-select="true"]').select2({
            width: '100%',
            theme: 'bootstrap',
            language: settings.site.lang,
            formatSelection: function(data, container, escapeFn) {
                container.attr('title', data.text).text(data.text);
            }
        });
    }

    //Bootstrap Select dropdown
    if ($('select[data-bselect="true"]').length > 0) {
        $.fn.selectpicker.Constructor.BootstrapVersion = '4';
        $('select[data-bselect="true"]').selectpicker();
    }

    //BBCode editor
    if ($('[data-editor="true"]').length > 0) {

        TrumbowygFilterInit.init('.trumbowyg-reset-css video, .trumbowyg-reset-css audio, .trumbowyg-reset-css video');

        $('[data-editor="true"]').trumbowyg({
            semantic: false,
            lang: settings.site.lang,
            autogrow: true,
            urlProtocol: true,
            resetCss: true,
            svgPath: settings.site.host_address + '/img/admin/icons.svg',
            tagsToRemove: ['script', 'link'],
            tagsToKeep: ['i', 'script[src]', 'hr', 'img', 'embed', 'iframe', 'input'],
            btns: [
                ['viewHTML'],
                ['historyUndo', 'historyRedo'],
                ['formatting'],
                ['strong', 'em', 'del', 'foreColor', 'backColor'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage', 'noembed'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList', 'table'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen'],
            ],
        }).on('tbwinit', function() {
            TrumbowygFilterInit.appenWysiwygOrigin();
            TrumbowygFilterInit.removeTextareaOrigin();
        }).on('tbwchange', function() {
            TrumbowygFilterInit.appenWysiwygOrigin();
            TrumbowygFilterInit.removeTextareaOrigin();
        }).on('tbwfocus', function() {
            TrumbowygFilterInit.appenWysiwygOrigin();
            TrumbowygFilterInit.removeTextareaOrigin();
        }).on('tbwtoggle', function() {
            TrumbowygFilterInit.appenWysiwygOrigin();
            TrumbowygFilterInit.removeTextareaOrigin();
        });
    }
    /**
     * Other Scripts
     */
    $isImgSrc = $('img[img-id]').attr('src');

    if ($isImgSrc) {

    } else {
        $('img[img-id]').hide();
    }

    $('input[img-id]').change(function() {
        readURL(this, $(this).attr('img-id'));
    });

    $('[data-dismiss="fileinput"]').click(function(event) {
        $imgID = $(this).attr('img-id');
        $('img[img-id="' + $imgID + '"]').hide();
    });


    var selectOptionVal = $('select[data-hidden="true"] option:selected').val();
    $("[data-hidden-par='" + selectOptionVal + "']").slideToggle('slow', function() {
        // Animation complete.
    });

    $('select[data-hidden="true"]').on('change', function() {

        $('[data-hidden-par]').slideToggle('slow', function() {
            // Animation complete.
        });

        var optionVal = $(this).val();
        var valElement = $("[data-hidden-par='" + optionVal + "']");
        var divElement = $('div').find("[data-hidden-par='" + optionVal + "']").html();

        if (divElement === undefined) {
            valElement.hide();
        } else {
            valElement.show();
        }

    });

    $('body').find('[type="text/nanoscript"]').each(function() {
        eval($(this).html());
    });

};