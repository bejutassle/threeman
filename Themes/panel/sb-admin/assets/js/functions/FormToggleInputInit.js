var FormToggleInputInit = {


    init: function(element) {

	$('body').on('click', element, function(e) {
	        $input = $(this).parent().parent().find('input');
	        $icon = $(this).find('i');

	        $return = $input.prop('type') == 'password' ? 'text ': 'password';
	        if($input.prop('type') == 'password'){
	            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
	        }else{
	            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
	        }

	        $input.prop('type', $return);

			e.preventDefault();
			e.stopImmediatePropagation();
	});


    }


};