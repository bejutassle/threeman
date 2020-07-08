var FormSearchInit = {


    init: function(element) {

	if(getParameterByName('q')){

		$('table>tbody>tr>td').highlight(getParameterByName('q'), {
			element: 'mark',
			className: 'spoiler',
			caseSensitive: false,
		});

	}

	//Set the value query object element
	$(element+' input,'+element+' select').each(function(index){
	        var input = $(this);

			$searchInputVal = $(element).find('[name="'+input.attr('name')+'"]');
			$getQueryURL = getParameterByName(input.attr('name'));

			if($getQueryURL){
			$searchInputVal.val($getQueryURL);
			}

	});


	//Form Post
	$('body').on('submit', element, function(e) {

	var form     = $(this).attr('form');
	var data     = $(this).serialize();
	var action   = $(this).attr('action');
	var method   = $(this).attr('method');
	var type     = $(this).attr('data-type');
	var formID = 'form#'+$(this).attr('id');
	var name = $(formID).find('input[type="text"]').attr('name');
	var val = $(formID).find('input[type="text"]').val();
	var url = new URL(window.location.href);
	var search_params = url.searchParams;
	search_params.set(name, val);
	if(val.length < 1){
	search_params.delete(name);
	search_params.delete('undefined');
	}
	url.search = search_params.toString();

	PjaxInit.reload(decodeURIComponent(url.toString()));

	e.preventDefault();
	e.stopImmediatePropagation();

	});


    }


};