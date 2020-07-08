var FormVisilibleInit = {


    init: function(element) {

	$collapseElement = $(element);

		FormVisilibleInit.collapse($collapseElement);

	$collapseElement.on('change, ifChecked', 'input[type="checkbox"], input[type="radio"]', function () {
		FormVisilibleInit.collapse($collapseElement);
	});

	$collapseElement.find('select').change(function() {
		FormVisilibleInit.collapse($collapseElement);
	});


    },

    collapse: function(element){
    		var $collapseElement = element;

		$.each($collapseElement, function(index, val) {
		     $selectedVal = $(this).find('input:checked, option:selected').val();
		     $selectedGroupName = $(this).data('main-collapse');

		     $subCollapseElement = $('[data-sub-collapse='+$selectedGroupName+'][data-val-collapse*='+$selectedVal+']');
		     $subCollapseElements = $('[data-sub-collapse='+$selectedGroupName+']');

		     if($subCollapseElement.length > 0){
		        $subCollapseElement.show();
		     }else{
		        $subCollapseElements.hide();
		     }
		});
    }


};