<?php
include( 'lib/lib.php' );
add_theme_support( 'post-thumbnails' );
//Load child stylesheet after parent stylesheet
add_action('wp_enqueue_scripts', function(){
  wp_enqueue_style( 'soah-child', get_stylesheet_directory_uri() .'/style.css', array( 'sp-core-style' ), '1.0.0' );
  wp_enqueue_style( 'soah-fep', get_stylesheet_directory_uri() .'/assets/css/fep.css', array( 'soah-child' ), '1.0.1' );
  wp_enqueue_script( 'contact-form-js', get_stylesheet_directory_uri().'/assets/js/form.js', array('jquery'), '1.0.0', true );
  wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0.0', true );
});

//Add asyn defer for ReCaptcha
add_filter( 'script_loader_tag','my_async_scripts',10,3 );

function my_async_scripts($tag,$handle,$src )
{

  $async_scripts = array( 'recaptcha' );

  if ( in_array( $handle, $async_scripts ) ) {
      // $src = 'https://www.google.com/recaptcha/api.js';
      return '<script type="text/javascript" src="' . $src . '" async defer ></script>';
  }

return $tag;

}
//Add google crimson text font
add_filter( 'sp_list_google_fonts', function( $fonts ){

  $fonts[] = array(
    'slug'	=> 'crimson',
    'name'	=> 'Crimson Text',
    'url'	  => 'Crimson+Text'
  );

} );
