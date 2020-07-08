var ModalActionInit = {


    init: function(element) {

		eModal.setEModalOptions({
			loadingHtml: '<center><span class="fa fa-circle-o-notch fa-spin fa-3x text-primary"></span></center>',
    	});

        $('body').on('click', element, function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            $size = $(this).data('size');
            $url = $(this).data('url');
            $title = $(this).data('title');
            $data = $(this).data('json');
            $button = $(this).data('submit-button');
            $download = $(this).data('download-button');
            $buttons = [];
            $buttons.push({text: lang.get('txt.close'), style: 'secondary', close: true});
            if($button == true){
            $buttons.push({text: lang.get('txt.save'), style: 'primary', close: false, click: sendForm});
            }
            if($download == true){
            $buttons.push({text: lang.get('txt.download'), style: 'danger', close: false});
            }
            if($data == 'undefined'){
                $data = {};
            }

            var modal = {
                url: $url,
                title: $title,
                size: eModal.size[$size],
                buttons: $buttons,
                datas: $data,
                useBin: true
            };

            return eModal.ajax(modal).then(function () { 
                window.onload();
            });
        });

    	function sendForm(){
    		$('.modal-body').find('form').submit();
    	}

    }

};