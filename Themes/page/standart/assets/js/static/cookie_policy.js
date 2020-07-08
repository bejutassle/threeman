    window.addEventListener("load", function(){

    window.cookieconsent.initialise({
      "palette": {
        "popup": {
          "background": "#07a3e0",
          "text": "#fff"
        },
        "button": {
          "background": "#f60",
          "text": "#fff"
        }
      },
      "theme": "classic",
      "content": {
        "message": lang.get('txt.cookie-policy-content-message'),
        "dismiss": lang.get('txt.cookie-policy-content-dismiss'),
        "link": lang.get('txt.cookie-policy-content-link'),
        "href": settings.site.cookie_policy_url,
      }
    })

  });