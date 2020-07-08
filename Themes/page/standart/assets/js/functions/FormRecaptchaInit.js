      var recaptchaCallback = function(element, key) {
      			if (!element) element = 'recaptcha_element';
      			if (!key) key = settings.site.recaptcha_key;
      			if(element || key)

				  $container = $('#'+element);
				  if($container.length > 0){
					  $container.html('');
					  var recaptcha = document.createElement('div');
					  grecaptcha.render(recaptcha, {
					    'sitekey': key
					  });
					  $container.append(recaptcha);
				  }
      };