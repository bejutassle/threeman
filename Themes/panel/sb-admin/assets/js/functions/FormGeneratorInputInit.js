var FormGeneratorInputInit = {


    init: function(element) {

	$('body').on('click', element, function(e) {
	        $input = $(this).parent().parent().find('input');
	        $show = $(this).parent().parent().find('[data-toggle="password"]');

	        if($input.prop('type') == 'password'){
	        	$show.trigger('click');
	        }
	        
			$input.val(randString($input));
	        $input.select();

			e.preventDefault();
			e.stopImmediatePropagation();
	});


    }


};