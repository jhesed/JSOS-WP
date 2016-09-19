jQuery(document).ready(function($){
   
   /** Variables from Customizer for Slider settings */
    if( benevolent_data.auto == '1' ){
        var slider_auto = true;
    }else{
        slider_auto = false;
    }
    
    if( benevolent_data.loop == '1' ){
        var slider_loop = true;
    }else{
        var slider_loop = false;
    }
    
    if( benevolent_data.pager == '1' ){
        var slider_control = true;
    }else{
        slider_control = false;
    }
    
    /** Home Page Slider */
    $('.flexslider').flexslider({
        slideshow: slider_auto,
        animationLoop : slider_loop,
        directionNav: false,
        animation: benevolent_data.animation,
        controlNav: slider_control,
        slideshowSpeed: benevolent_data.speed,
        animationSpeed: benevolent_data.a_speed
    });
   
   $('.number').counterUp({
        delay: 10,
        time: 1000
    });
   
   $( "#tabs" ).tabs();
   
   $('#responsive-menu-button').sidr({
      name: 'sidr-main',
      source: '#site-navigation',
      side: 'right'
    });

   $('#responsive-secondary-menu-button').sidr({
      name: 'sidr-main2',
      source: '#top-navigation',
      side: 'left'
    });
   
});