var FormFilterInit = {


    init: function(element) {

//Selected filter query object element

	$changeResult = $(element).data('change-result');

	$.each(getAllUrlParams(window.location.href), function(index, val) {
		 var data = val.split(',');
		 var value = data[0];
		 var sort = decodeURIComponent(data[1]);
		 var sort = sort.split('+').join(' ');
		 var name = index;

		 $select = $(element).find('select');
		 if(sort != 'undefined'){
		 $options = $select.find('option[value="'+value+'"][data-sort="'+sort+'"][data-form-name="'+name+'"]');
		 $options.attr('selected', 'selected');
		 }else{
		 $options = $select.find('option[value="'+value+'"][data-form-name="'+name+'"]');
		 $options.attr('selected', 'selected');
		 }
	});


if($changeResult == true){

//Form Change Post
	$('body').on('change', element, function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();

	$select = $(this).find('select');
	$option = $(this).find('option:selected');
	$form_name = $option.data('form-name');

	if($form_name){
		$select.attr('name', $form_name);
	}else{
		$select.attr('name', 'f');
	}

	$(element).submit();

	});

}


//Form Manuel Post
	$('body').on('submit', element, function(e) {

	e.preventDefault();
	e.stopImmediatePropagation();

	$serialize = $(element).find('option:selected').map(function(i, e) {
		if($(e).data('form-name') == undefined){
			$name = 'null';
		}else{
			$name = $(e).data('form-name');
		}
		if($(e).data('sort') == undefined){
			$sort = 'null';
		}else{
			$sort = $(e).data('sort');
		}
		if($(e).val() == undefined){
			$val = 'null';
		}else{
			$val = $(e).val();
		}
		return {
			name: $name, 
			sort: $sort, 
			value: $val,
		};
	}).get();

	var url = new URL(window.location.href);
	var search_params = url.searchParams;
	search_params.delete('w');
	search_params.delete('f');
	search_params.delete('a');
	search_params.delete('undefined');
	
	$.each($serialize, function(index, val) {
		if(val.sort == 'none'){
			search_params.set(val.name, val.value);
		}else{
			search_params.set(val.name, val.value+','+val.sort);
		}
	});

	if(search_params.get('a') == 'all,division'){
			search_params.delete('undefined');
	}

	if(search_params.get('l')){
			search_params.delete('p');
			search_params.delete('undefined');
			search_params.delete('null');
	}


	if(search_params.get('null') == 'unique,null'){
			search_params.delete('w');
			search_params.delete('f');
			search_params.delete('a');
			search_params.delete('undefined');
			search_params.delete('null');
	}

	url.search = search_params.toString();
	var redirect = decodeURIComponent(url.toString());
	PjaxInit.reload(redirect);

	});


    }


};