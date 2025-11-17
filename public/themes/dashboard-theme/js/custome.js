document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
      
      element.addEventListener('click', function (e) {
  
        let nextEl = element.nextElementSibling;
        let parentEl  = element.parentElement;	
  
          if(nextEl) {
              e.preventDefault();	
              let mycollapse = new bootstrap.Collapse(nextEl);
              
              if(nextEl.classList.contains('show')){
                mycollapse.hide();
              } else {
                  mycollapse.show();
                  // find other submenus with class=show
                  var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                  // if it exists, then close all of them
                  if(opened_submenu){
                    new bootstrap.Collapse(opened_submenu);
                  }
              }
          }
      }); // addEventListener
    }) // forEach
  }); 

(function ($) {
  // Unique behavior name for setting the cookie
  Drupal.behaviors.setCookieOnClick = {
    attach: function (context, settings) {
      $('#my-button', context).once('set-cookie-on-click').click(function () {
        document.cookie = "user_type=new; path=/demo;";
        alert('You are about to be redirected to the dashboard.');
      });
    }
  };
})(jQuery);

(function ($) {
  // Unique behavior name for unsetting the cookie
  Drupal.behaviors.unsetCookieOnLogout = {
    attach: function (context, settings) {
      $('#my-logout', context).once('unset-cookie-on-logout').click(function () {
        document.cookie = "user_type=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/demo;";
      });
    }
  };
})(jQuery);