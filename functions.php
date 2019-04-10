<?php
include( 'lib/lib.php' );
add_theme_support( 'post-thumbnails' );
//Load child stylesheet after parent stylesheet
add_action('wp_enqueue_scripts', function(){
  wp_enqueue_style( 'soah-child', get_stylesheet_directory_uri() .'/style.css', array( 'sp-core-style' ), '1.0.0' );
  wp_enqueue_style( 'soah-fep', get_stylesheet_directory_uri() .'/assets/css/fep.css', array( 'soah-child' ), '1.0.5' );

  wp_enqueue_script( 'meteor-slides', get_stylesheet_directory_uri().'/assets/js/meteor-slides.js', array('jquery'), '1.0.0', true );

  wp_enqueue_script( 'soah-main', get_stylesheet_directory_uri().'/assets/js/form.js', array('meteor-slides'), '1.0.3', true );
  // wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js?hl=hi', array(), '1.0.0', true );
});

/*
//Add asyn defer for ReCaptcha
add_filter( 'script_loader_tag', function( $tag, $handle, $src ){
  $async_scripts = array( 'recaptcha' );

  if ( in_array( $handle, $async_scripts ) ) {
        // $src = 'https://www.google.com/recaptcha/api.js';
        return '<script type="text/javascript" src="' . $src . '" async defer ></script>';
  }

  return $tag;
}, 10, 3 );
*/

//Add google crimson text font
add_filter( 'sp_list_google_fonts', function( $fonts ){

  $fonts[] = array(
    'slug'	=> 'crimson',
    'name'	=> 'Crimson Text',
    'url'	  => 'Crimson+Text'
  );

} );

//add new column to the admin dashboard
add_filter( 'manage_reports_posts_columns', function( $columns ){
  $columns['details']  = 'Additional Details';
  return $columns;
} );

add_filter('manage_reports_posts_custom_column', function( $columns ){
  global $post;
  switch( $columns ){
    case 'details':

      $meta_fields = array( 'contact-name', 'contact-phone', 'contact-email', 'incident-links' );

      // ITERATE THROUGH EACH META FIELD
      foreach ( $meta_fields as $meta_field ) {

        switch( $meta_field ){
          // SPECIAL PROCESSING FOR INCIDENT LINKS
          case 'incident-links':
            $links = get_post_meta( $post->ID, $meta_field, true );
            // CHECK IF THE VALUE IS NOT EMPTY OR INVALID
            if( $links ){
              // EACH NEW LINE LINK AS AN ITEM IN AN ARRAY
              $links = explode("\r\n", $links );

              if( is_array( $links ) ){
                echo "<p><br><br><b>Additional Links</b></p>";
                $link_i = 1;
                foreach( $links as $link ){
                  echo "<a target='_blank' href='$link'>Link $link_i</a><br>";
                  $link_i++;
                }
              }
            }
            break;
          default:
            echo get_post_meta( $post->ID, $meta_field, true )."<br>";
        }
      }
      break;
    }
});

add_action( 'admin_menu', function(){
  add_menu_page( 'Theme Options', 'Theme Options', 'manage_options', 'soah-settings', 'menu_page', 'dashicons-admin-site' );
} );


function menu_page(){



  /* ADD TAB SCREENS FOR EACH TAXONOMY IN THE SYSTEM */
  $screens = array();
  $taxonomies = get_object_taxonomies( 'reports', 'objects' );
  $i = 0;
  foreach( $taxonomies as $taxonomy ){
    $screens[ $taxonomy->name ] = array(
      'label'   => $taxonomy->label,
      'tab'     => get_stylesheet_directory().'/lib/taxonomy_translation.php',
    );
    if( $i ){
      $screens[ $taxonomy->name ][ 'action' ] = $taxonomy->name;
    }
    else{
      $first_taxonomy = $taxonomy->name;
    }
    $i++;
  }

  $screens = apply_filters( 'orbit_admin_translations_screens', $screens );

  $active_tab = '';

  _e( '<div class="wrap">' );
  _e( '<h1>Theme Translations</h1>' );
  _e( '<h2 class="nav-tab-wrapper">' );

  foreach( $screens as $slug => $screen ){
    $url =  admin_url( 'admin.php?page='.$_GET['page'] );
    if( isset( $screen['action'] ) ){
      $url =  esc_url( add_query_arg( array( 'action' => $screen['action'] ), admin_url( 'admin.php?page='.$_GET['page'] ) ) );
    }

    $nav_class = "nav-tab";

    if( isset( $screen['action'] ) && isset( $_GET['action'] ) && $screen['action'] == $_GET['action'] ){
      $nav_class .= " nav-tab-active";
      $active_tab = $slug;
    }

    if( ! isset( $screen['action'] ) && ! isset( $_GET['action'] ) ){
      $nav_class .= " nav-tab-active";
      $active_tab = $slug;
    }

    echo '<a href="'.$url.'" class="'.$nav_class.'">'.$screen['label'].'</a>';
  }

  _e( '</h2>' );



  if( file_exists( $screens[ $active_tab ][ 'tab' ] ) ){
    include( $screens[ $active_tab ][ 'tab' ] );
  }

  _e( '</div>' );

}
