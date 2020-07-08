var HistroyBackInit = {


    init: function(element) {

		$('body').on('click', element, function(e) {
		  e.preventDefault();
		  history.back();
		});

    }

};