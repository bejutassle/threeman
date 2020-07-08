var FormCountCharInit = {


    init: function(element) {

    $(element).each(function(index, el) {

	    $max = $(this).attr('maxlength');
	    $deflength = $(this).val().trim().length;
	    $maxlength = $max - $deflength;
		$helpBlock = $(this).closest('.controls');
		$has_help_block = $helpBlock.find('span.help-block');
		if($has_help_block.length < 1){
			$helpBlock.append('<span class="help-block" data-count="text">'+$maxlength+'</span>');
		}

		$(this).keyup(function () {
			$max = $(this).attr('maxlength');
		    $tlength = $(this).val().length;
		    $(this).val($(this).val().substring(0, $max));
		    $remain = $max - parseInt($tlength);
		    $(this).closest('.controls').find('[data-count="text"]').text($remain);
		});

    });

    }


};