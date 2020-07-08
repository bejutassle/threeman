var FormLogoutInit = {


    init: function(element) {

	$('body').on('click', element, function(e) {

		var dataURL = $(this).attr('href');

		swal({
		  title: lang.get('txt.swal-logout-title'),
		  type: 'question',
		  showCancelButton: true,
		  confirmButtonText: lang.get('txt.swal-logout-confirm-button'),
		  cancelButtonText: lang.get('txt.swal-cancel-button'),
		}).then(function(result) {

		   if (result.value) {
		      window.location.href = dataURL;
		   }
		   
		});

		e.preventDefault();
		e.stopImmediatePropagation();

	});

}


};