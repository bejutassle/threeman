var FormNestedCheckboxInit = {


    init: function(element) {
    	if($(element).length > 0){

            $(element+' input:checkbox').on('ifChanged', function (event) {
                $checked = $(this).is(":checked");
                $parent = $(this).closest('li');
                $pid = $parent.data('parent');
                $id = $parent.data('id');
                $main = $('li[data-id="'+$pid+'"]');
                $mainf = $main.find('input:checkbox').first();
                $mainfc = $mainf.is(':checked');
                $subcheck = $('li[data-parent="'+$pid+'"]').find('input:checkbox').is(':checked');

                if($pid === 0 && $checked === true){
                        $('li[data-parent="'+$id+'"]').find('input:checkbox').iCheck('check');
                }else if($pid === 0 && $checked === false){
                        $('li[data-parent="'+$id+'"]').find('input:checkbox').iCheck('uncheck');
                }else{
                        if($subcheck === false && $mainfc === true){
                            $main.find('input:checkbox').iCheck('uncheck');
                        }
                        if($checked === true){
                                if($mainfc === false){
                                    $main.find('input:checkbox').iCheck('check');
                                }
                        }else{
                                //$main.find('input:checkbox').iCheck('uncheck');
                        }
                }

                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            });

		}


    }


};