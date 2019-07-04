<?php

include( 'lib/lib.php' );

add_theme_support( 'post-thumbnails' );

//Constant changes all the js and css version on the go
define( 'SOAH_VERSION', '1.8.3' );


//Load child stylesheet after parent stylesheet
add_action('wp_enqueue_scripts', function(){

  // LOAD THE CHILD THEME CSS
  wp_enqueue_style( 'soah-child', get_stylesheet_directory_uri() .'/assets/css/style.css', array( 'sp-core-style' ), SOAH_VERSION );

  // STYLES FOR THE FORM
  wp_enqueue_style( 'soah-fep', get_stylesheet_directory_uri() .'/assets/css/fep.css', array( 'soah-child' ), SOAH_VERSION );

  // MULTIPART FORM
  wp_enqueue_script( 'meteor-slides', get_stylesheet_directory_uri().'/assets/js/meteor-slides.js', array('jquery'), SOAH_VERSION, true );

  // VALIDATION ON THE FORM
  wp_enqueue_script( 'soah-main', get_stylesheet_directory_uri().'/assets/js/form.js', array('meteor-slides'), SOAH_VERSION, true );

  // BATCH PROCESS ENQUEUE ASSETS
  $batch_process = ORBIT_BATCH_PROCESS::getInstance();
  $batch_process->enqueue_assets();

});

// Changing excerpt more
function excerpt_display($more) {
   global $post;
   return '... <a href="'. get_permalink($post->ID) . '" class="read-more">Continue reading</a>';
}
add_filter('excerpt_more', 'excerpt_display');


//Add google crimson text font
add_filter( 'sp_list_google_fonts', function( $fonts ){

  $fonts[] = array(
    'slug'	=> 'crimson',
    'name'	=> 'Crimson Text',
    'url'	  => 'Crimson+Text'
  );

} );

add_action( 'orbit_filter_form_header', function( $form ){
  _e("<p class='small'>Filter this data</p><hr style='margin:10px -18px 20px;'>");
});


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

class SOAH_ADMIN{

  function __construct(){
    add_action( 'admin_menu', function(){
      add_menu_page( 'Translations', 'SOAH Options', 'manage_options', 'soah-settings', array( $this, 'translations_page' ), 'dashicons-admin-site' );
      //add_submenu_page( 'soah-settings', 'Import', 'Import Reports', 'manage_options', 'import', array( $this, 'import_page') );
      //add_submenu_page( 'soah-settings', 'Demo', 'Demo', 'manage_options', 'demo', array( $this, 'demo_page') );
    } );
  }


  function translations_page(){

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


}

new SOAH_ADMIN;
