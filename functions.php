<?php
include('Contact-Form/contact-form.php');
add_theme_support( 'post-thumbnails' );
//Load child stylesheet after parent stylesheet
add_action('wp_enqueue_scripts', function(){
  wp_enqueue_style( 'soah-child-style', get_stylesheet_directory_uri() .'/style.css', array( 'sp-core-style' ), time() );

});
//Add google crimson text font
add_filter( 'sp_list_google_fonts', function( $fonts ){

  $fonts[] = array(
    'slug'	=> 'crimson',
    'name'	=> 'Crimson Text',
    'url'	  => 'Crimson+Text'
  );

} );
