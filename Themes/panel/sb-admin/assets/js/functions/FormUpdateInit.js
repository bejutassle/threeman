var FormUpdateInit = {


    init: function(element) {

    if($(element).length > 0){

	$('body').on('change', 'input[data-checkbox="all"]', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();

	$('input[type=checkbox][data-checkbox="step"]').prop('checked', this.checked);

    });

	//Form Manuel Post
	$('body').on('change', element, function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();

	$(this).submit();

	});

	//Form Manuel Post
	$('body').on('submit', element, function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();

	$url = $(this).attr('action');
	$method = $(this).attr('method');
	$data = $.merge($(this).serializeArray(), $('[data-checkbox="step"]:checked').serializeArray());
	$u = $data.filter(function(item) { return item.name === 'u'; });
	$type = isset($u[0].value) ? $u[0].value : 0;

	if($('[data-checkbox="step"]:checked').serialize() == '' && $type != '0'){
		toastr.error(lang.get('txt.checklist-is-empty'), lang.get('txt.toastr-error-title'));
		$('select[name="u"]').find('option[value="0"]').prop('selected', 'selected');
		$('select[name="u"]').selectpicker('refresh');
		return false;
	}

	switch($type) {
	  case 'report':

		swal({
		  title: lang.get('txt.swal-report-input-enter'),
		  type: 'info',
		  input: 'text',
		  inputAttributes: {
		    autocapitalize: 'off'
		  },
		  showCancelButton: true,
		  showLoaderOnConfirm: true,
		  confirmButtonText: lang.get('txt.swal-next-button'),
          cancelButtonText: lang.get('txt.swal-cancel-button'),
		  preConfirm: (login) => {
		  	return login === '' ? swal.showValidationError(lang.get('txt.swal-require-input')) : swal.resetValidationError();
		  },
		  allowOutsideClick: () => !swal.isLoading()
		  }).then(function(result) {
        	if(result.value){
        		$data.push({name:'report-name', value:result.value});

				$.ajax({
					url: $url,
					type: $method,
					dataType: 'JSON',
					data: $data,
				})
				.done(function(data) {

			              swal({
			                type: 'success',
			                title: data.message,
			                text: data.description,
			                buttonsStyling: true,
			                timer: 2000,
			                  onOpen: function () {
			                  swal.showLoading()
			                  setTimeout(function(){
			                    swal.close();
			                    PjaxInit.reload(data.url);
			                  }, 2000);
			              }});

				})
				.fail(function(xhr, status, response) {
					var error = jQuery.parseJSON(xhr.responseText);
					$.each(error.message, function (key, value) {
						toastr.error(value, lang.get('txt.toastr-error-title'));
					});
					$('select[name="u"]').find('option[value="0"]').prop('selected', 'selected');
					$('select[name="u"]').selectpicker('refresh');
				});

        	}
          });

	    break;
	  case 'del':
        swal({
          title: lang.get('txt.swal-delete-title'),
          text: lang.get('txt.swal-delete-text'),
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: lang.get('txt.swal-delete-confirm-button'),
          cancelButtonText: lang.get('txt.swal-cancel-button'),
        }).then(function(result) {
        	if(result.value){
				$.ajax({
					url: $url,
					type: $method,
					dataType: 'JSON',
					data: $data,
				})
				.done(function(data) {
			            toastr.error(data.description, data.message);
						PjaxInit.reload(window.location.href);
				})
				.fail(function(xhr, status, response) {
					var error = jQuery.parseJSON(xhr.responseText);
					$.each(error.message, function (key, value) {
						toastr.error(value, lang.get('txt.toastr-error-title'));
					});
				});
        	}
        });
	    break;
	  case 'active':
		$.ajax({
			url: $url,
			type: $method,
			dataType: 'JSON',
			data: $data,
		})
		.done(function(data) {
	            toastr.success(data.description, data.message);
				PjaxInit.reload(window.location.href);
		})
		.fail(function(xhr, status, response) {
			var error = jQuery.parseJSON(xhr.responseText);
			$.each(error.message, function (key, value) {
				toastr.error(value, lang.get('txt.toastr-error-title'));
			});
		});
	    break;
	  case 'passive':
		$.ajax({
			url: $url,
			type: $method,
			dataType: 'JSON',
			data: $data,
		})
		.done(function(data) {
	            toastr.warning(data.description, data.message);
				PjaxInit.reload(window.location.href);
		})
		.fail(function(xhr, status, response) {
			var error = jQuery.parseJSON(xhr.responseText);
			$.each(error.message, function (key, value) {
				toastr.error(value, lang.get('txt.toastr-error-title'));
			});
		});
	    break;
	  default:

	}

	});

    }



    }


};