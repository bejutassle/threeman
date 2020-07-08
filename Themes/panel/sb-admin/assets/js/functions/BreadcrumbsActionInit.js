var BreadcrumbsActionInit = {


    init: function(element) {

    $element = $(element);

      $('ul.treeview-menu > li.treeview[id]').on('click', function(event) {
        $.cookie('activeMenuID', this.id);
      });

	$('body').on('click', function(event) {
	       if(event.target.class == "treeview")
	          return;
	       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
	       if($(event.target).closest('.treeview').length)
	          return;             

	      //Do processing of click event here for every element except with id menu_content
			$.cookie('activeMenuID', 0);
	});

    }

};