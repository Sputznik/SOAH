<?php

class CHOROPLETH_MAP extends SOAH_BASE{

  function __construct(){

		add_shortcode( 'soah_map', array( $this, 'shortcode' ) );

    /** TO LOAD THE ASSETS - SCRIPTS AND STYLES */
		add_action( 'the_posts', array( $this, 'assets' ) );

    add_action( 'wp_ajax_map_data', array( $this, 'map_data' ) );
    add_action( 'wp_ajax_nopriv_map_data', array( $this, 'map_data' ) );

	}

  function getStates(){
    $terms = get_terms( 'locations', array( 'hide_empty' => false ) );
    print_r( $terms );
  }

  function map_data(){

    $data = array();
    $terms = get_terms( 'locations', array( 'hide_empty' => false ) );
    foreach( $terms as $term ){
      $temp = array(
        'district'  => $term->name,
        'reports'   => rand( 0, 100 )
      );
      array_push( $data, $temp );

    }


    //echo "<pre>";
    //print_r( $data );
    //echo "</pre>";

    print_r( wp_json_encode( $data ) );

    wp_die();
  }

  /** CHECK IF THE CONTENT HAS THE SHORTCODE */
	function has_shortcode( $content, $tag ) {
		if(stripos($content, '['.$tag.']') !== false)
			return true;
		return false;
	}

  /** LOAD SCRIPTS AND STYLES IF THE SHORTCODE IS USED */
	function assets($posts){

		$found = false;
		if ( !empty($posts) ){
			foreach ($posts as $post) {
				if ( $this->has_shortcode($post->post_content, 'soah_map') ){
					$found = true;
					break;
				}
			}
		}

		if( $found ){
      // STYLES FOR LEAFLET
      wp_enqueue_style( 'soah-leaflet', get_stylesheet_directory_uri() .'/assets/css/leaflet.1.4.0.css', array(), SOAH_VERSION );

      // STYLES FOR CHOROPLETH MAP
      wp_enqueue_style( 'soah-choropleth', get_stylesheet_directory_uri() .'/assets/css/choropleth.map.css', array(), SOAH_VERSION );

      // POPPER SCRIPT
      wp_enqueue_script( 'popper', get_stylesheet_directory_uri().'/assets/js/popper.1.14.3.js', array( 'jquery' ), SOAH_VERSION, true );

      // LEAFLET SCRIPT
      wp_enqueue_script( 'leaflet', get_stylesheet_directory_uri().'/assets/js/leaflet.1.4.0.js', array( 'jquery', 'popper' ), SOAH_VERSION, true );

      // LEAFLET GEOCSV SCRIPT
      wp_enqueue_script( 'leaflet-geocsv', get_stylesheet_directory_uri().'/assets/js/leaflet.geocsv.js', array( 'leaflet' ), SOAH_VERSION, true );

      // INDIA DISTRICTS SCRIPT
      wp_enqueue_script( 'leaflet-india-dist', get_stylesheet_directory_uri().'/assets/js/india_dist_662.js', array( 'leaflet' ), SOAH_VERSION, true );

      // INDIA STATES SCRIPT
      wp_enqueue_script( 'leaflet-india-states', get_stylesheet_directory_uri().'/assets/js/states.js', array( 'leaflet' ), SOAH_VERSION, true );

      // SAMPLE DATA SCRIPT
      wp_enqueue_script( 'soah-sample-data', get_stylesheet_directory_uri().'/assets/js/sample_data.js', array( 'leaflet' ), SOAH_VERSION, true );

      // CHOROPLETH MAP SCRIPT
      wp_enqueue_script( 'choropleth', get_stylesheet_directory_uri().'/assets/js/choropleth.js', array( 'leaflet' ), SOAH_VERSION, true );

		}

		return $posts;

	}


  function shortcode( $atts ){

		$atts = shortcode_atts( array(
			'title'	=> 'A Simple Choropleth Map',
      'url'   => admin_url('admin-ajax.php?action=map_data')
		), $atts, 'soah_map' );

    $atts['color_rules'] = array(
      'default' => '#EDE7F6',
      'min'	=> array(
        'value'	=> 30,
        'color'	=> '#B39DDB'
      ),
      'max'	=> array(
        'value'	=> 85,
        'color'	=> '#311B92'
      ),
      'ranges'  => array(
        array(
          'min_value' => 60,
          'max_value'	=> 85,
          'color'			=> '#5E35B1'
        ),
        array(
          'min_value' => 30,
          'max_value'	=> 59,
          'color'			=> '#7E57C2'
        ),
      )
    );


		ob_start();

    include( "templates/map.php" );
		return ob_get_clean();
	}

}

CHOROPLETH_MAP::getInstance();
