<?php

class CHOROPLETH_MAP extends SOAH_BASE{

  function __construct(){

		add_shortcode( 'soah_map', array( $this, 'shortcode' ) );

    /** TO LOAD THE ASSETS - SCRIPTS AND STYLES */
		add_action( 'the_posts', array( $this, 'assets' ) );

    add_action( 'wp_ajax_map_data', array( $this, 'map_data' ) );
    add_action( 'wp_ajax_nopriv_map_data', array( $this, 'map_data' ) );

    add_filter( 'terms_clauses', function ( $pieces, $taxonomies, $args ){

        // Bail if we are not currently handling our specified taxonomy
        if ( !in_array( 'locations', $taxonomies ) )
          return $pieces;

        // Check if our custom argument, 'wpse_parents' is set, if not, bail
        if ( !isset ( $args['wpse_parents'] ) || !is_array( $args['wpse_parents'] ) )
          return $pieces;

        // If 'wpse_parents' is set, make sure that 'parent' and 'child_of' is not set
        if ( $args['parent'] || $args['child_of'] )
          return $pieces;

        // Validate the array as an array of integers
        $parents = array_map( 'intval', $args['wpse_parents'] );

        // Loop through $parents and set the WHERE clause accordingly
        $where = [];
        foreach ( $parents as $parent ) {
          // Make sure $parent is not 0, if so, skip and continue
          if ( 0 === $parent )
            continue;

          $where[] = " tt.parent = '$parent'";
        }

        if ( !$where )
          return $pieces;

        $where_string = implode( ' OR ', $where );
        $pieces['where'] .= " AND ( $where_string ) ";

        return $pieces;
    }, 10, 3 );

	}

  function getReportCount( $params_string, $location_id ){

    // INCASE THE PARAMS STRING IS NOT SET PROPERLY
    if( !is_array( $params_string ) ){ $params_string = array(); }
    if( !isset( $params_string['tax'] ) ){ $params_string['tax'] = ''; }
    if( !isset( $params_string['date'] ) ){ $params_string['date'] = ''; }

    $orbit_util = ORBIT_UTIL::getInstance();

    $query_args = array(
      'post_status'     => 'publish',
      'post_type'       => 'reports',
      'posts_per_page'  =>  -1,
    );

    $query_args['tax_query'] = $orbit_util->getTaxQueryParams( $params_string['tax'] );

    // SPECFIC TO DISTRICT DATA
    array_push( $query_args['tax_query'], array(
      'taxonomy'  => 'locations',
      'field'     => 'term_id',
      'terms'    => $location_id
    ) );

    $query_args['date_query'] = $orbit_util->getDateQueryParams( $params_string['date'] );

    $query = new WP_Query( $query_args );


    return 0;
    //return $query->found_posts;
  }

  // THIS SECTION IS CREATING A URL THAT WILL DISPLAY THE LIST OF INCIDENTS
  function getReportsURL( $params ){
    $url = site_url('reports');
    $i = 0;
    foreach ( $params as $slug => $value ) {
      if( ! in_array( $slug, array( 'tax_locations' ) ) ){
        if( $i == 0 ){ $url .= "?"; }
        else{ $url .= "&"; }

        if( is_array( $value ) ){
          foreach ( $value as $index => $item_value ) {
            if( $index > 0 ){ $url .= "&"; }
            $url .= $slug."[]=".$item_value;
          }
        }
        else{ $url .= $slug."=".$value; }
        $i++;
      }
    }
    return $url;
  }

  // GET ARRAY OF STATE ID IF THE STATE NAMES HAVE BEEN PASSED
  function getStates( $state_names = array() ){
    $states = [];
    if( isset( $state_names ) && is_array( $state_names ) ){
      foreach ( $state_names as $state_name ) {
        $stateTerm = get_term_by( 'name', $state_name, 'locations' );
        if( isset( $stateTerm->term_id ) && $stateTerm->term_id ){
          $states[ $stateTerm->term_id ] = $stateTerm->name;
        }
      }
    }
    return $states;
  }

  function map_data(){

    $url = $this->getReportsURL( $_GET );

    $year = 0;
    if( isset( $_GET['postdate_year'] ) ){
      $year = $_GET['postdate_year'];
    }

    // GET ARRAY OF STATE ID IF THE STATE NAMES HAVE BEEN PASSED
    $states = [];
    if( isset( $_GET['tax_locations'] ) ){ $states = $this->getStates( $_GET['tax_locations'] ); }
    $state_ids = array_keys( $states );

    // FINAL DATA
    $data = array();

    // QUERY THE LOCATIONS TABLE
    $term_query_args = array(
      'hide_empty'  => false,
      'taxonomy'    => 'locations',
    );

    // IF STATES ARE PASSED AS PARAMS THEN FIND THE DISTRICTS OF ONLY THOSE THAT ARE PASSED
    if ( is_array( $state_ids ) && count( $state_ids ) ){ $term_query_args['wpse_parents'] = $state_ids; }

    $terms = get_terms( $term_query_args );
    // END OF QUERYING THE LOCATIONS TABLE

    // GET PARAMETERS FOR FURTHER QUERY TO FIND THE TOTAL COUNT OF REPORTS
    $orbit_util = ORBIT_UTIL::getInstance();
    $params = $_GET;
    unset( $params['tax_locations'] );
    $params_string = $orbit_util->paramsToString( $params );

    $ranges = array();
    $max_count = 0;
    $total_count = 0;

    // ITERATE THROUGH EACH TERM - LOCATIONS WHICH IS INCLUSIVE OF STATE AND DISTRICTS
    foreach( $terms as $term ){
      if( $term->parent ){

        $report_count = $this->getReportCount( $params_string, $term->term_id );

        // FINDS THE MAX COUNT OF THE REPORTS IN THE DATA SET
        if( $report_count > $max_count ){ $max_count = $report_count; }

        // TOTAL COUNT OF REPORTS
        $total_count += $report_count;

        // UPDATING THE RANGES DATA
        if( $report_count > 0 ){
          if( !isset( $ranges[ $report_count ] ) ){
            $ranges[ $report_count ]  = 0;
          }
          $ranges[ $report_count ]++;
        }


        $temp = array(
          'district'  => $term->name,
          'parent'    => $term->parent,
          'url'       => $url."&tax_locations[]=".$term->name,
          'reports'   => $report_count //> 0 ? $report_count : rand( 0, 100 )
        );
        array_push( $data, $temp );
      }
      else{
        $states[ $term->term_id ] = $term->name;
      }
    }


    $color_rules = $this->getColorRules( $ranges, array( '#FA8072', '#ED2939', '#B80F0A', '#5e1914' ) );

    // ITERATE THROUGH DATA TO CALCULATE PERCENTILE AND ADD STATE INFORMATION
    foreach ( $data as $index => $row ) {
      $data[ $index ]['percentile'] = 0;
      if( $row['reports'] ){
        $data[ $index ]['percentile'] = ceil( ( $row['reports'] / $max_count ) * 100 );
      }

      // ADDING STATE INFORMATION
      if( isset( $states[ $row['parent'] ] ) ){
        $data[ $index ]['state'] = $states[ $row['parent'] ];
        unset( $data[ $index ]['parent'] );
      }

      $data[ $index ]['color'] = $this->getColorKey( $row['reports'], $color_rules );

    }

    $context = $this->getContext( $_GET, array(
      array(
        'label' => 'Total Reports',
        'value' => $total_count
      )
    ) );

    $final_data = array(
      'context'     => $context,
      'color_rules' => $color_rules,
      'data'        => $data,

    );

    //$orbit_util->test( $final_data );

    print_r( wp_json_encode( $final_data ) );

    wp_die();
  }

  function getColorText( $color ){

    switch( $color ){
      /*
      case '#FFF':
        return 'Lowest';
      */

      case '#5e1914':
        return 'Highest';

      case '#FA8072':
        return 'Lowest';

      case '#ED2939':
        return 'Mid Range';

      case '#B80F0A':
        return 'Upper Mid Range';

    }
    return 'Default';
  }

  function getContext( $data, $appendData = array() ){

    $labels = array(
      'postdate_after'    => 'From',
      'postdate_before'   => 'To',
      'tax_locations'     => 'State',
      'tax_report-type'   => 'Reported',
      'tax_victims'       => 'Victims'
    );

    $context = array();

    foreach ( $labels as $key => $text ) {
      if( isset( $data[ $key ] ) && $data[ $key ] ){

        $value = is_array( $data[ $key ] ) ? implode( ', ', $data[ $key ] ) : $data[ $key ];

        // IF THE VALUE IS DATE STRING THEN CONVERT THE FORMAT OF DISPLAYING
        if( in_array( $key, array( 'postdate_after', 'postdate_before' ) ) ){
          $date = strtotime( $data[ $key ] );
          $value = date('d M, Y', $date );
        }

        array_push( $context, array(
          'label' => $text,
          'value' => $value
        ));
      }
    }

    if( !count( $context ) ){
      $context = array(
        array(
          'label' => "India",
          'value' => "since 2014"
        )
      );
    }

    foreach ( $appendData as $data ) {
      array_push( $context, $data );
    }

    return $context;
  }

  function getColorKey( $num_data, $color_rules ){
    //$this->printArray( $color_rules );
    $color = "#fff";

    if ( $num_data >= $color_rules['max']['value'] || $num_data < $color_rules['min']['value'] ){

      $color = $color_rules['min']['color'];
      if( $num_data >= $color_rules['max']['value'] ){
        $color = $color_rules['max']['color'];
      }
    }
    else{

      foreach( $color_rules['ranges'] as $color_rule ){
        if ( $num_data >= $color_rule['min_value'] && $num_data <= $color_rule['max_value'] ){
          $color = $color_rule['color'];
        }
      }

    }

    return $color;
  }

  function printArray( $data ){
    echo "<pre>";
    print_r( $data );
    echo "</pre>";
  }

  function getBuckets( $ranges, $colors, $total_buckets ){
    $buckets = array();

    $bucket_length = floor( count( $ranges ) / ( $total_buckets ) );

    $ranges = array_keys( $ranges );

    //$this->printArray( $ranges );

    asort( $ranges );

    //$this->printArray( $ranges );

    $max_value = 1;

    for( $bucket_i = 0; $bucket_i < $total_buckets; $bucket_i++ ){

      $temp_ranges = array_slice( $ranges, $bucket_i * $bucket_length, $bucket_length );

      if( count( $temp_ranges ) ){
        $temp = array();
        $temp['min_value'] = $temp_ranges[0];
        $temp['max_value'] = $temp_ranges[ count( $temp_ranges) - 1 ];

        if( $max_value < $temp['max_value'] ){
          $max_value = $temp['max_value'];
        }

        $temp['color']   = $colors[ $bucket_i ];
        $temp['text']    = $this->getColorText( $temp['color'] );
        if( $temp['min_value'] < $temp['max_value'] ){
          array_push( $buckets, $temp );
        }

      }
    }

    if( is_array( $buckets ) && !count( $buckets ) ){
      $min_value = 1;
      if( $max_value <= $min_value ){
        $max_value = $min_value + 1;
      }

      $buckets = array(
        array(
          'min_value' => $min_value,
          'max_value' => $max_value,
          'color'     => $colors[0],
          'text'      => $this->getColorText( $colors[0] )
        )
      );
    }

    //krsort( $buckets );

    //$this->printArray( $buckets );

    return $buckets;
  }

  function getColorRules( $ranges, $colors ){

    //$this->printArray( $ranges );

    // sort array by keys in ascending order
    ksort( $ranges );

    //$this->printArray( $ranges );

    $total_buckets = count( $colors ) - 1;

    if( 2 * $total_buckets > count( $ranges ) ){
      $total_buckets = count( $ranges ) / 2;
    }

    $buckets = $this->getBuckets( $ranges, $colors, $total_buckets );

    $color_rules = array(
      'min' => array(
        'value' => $buckets[ 0 ]['min_value'],
        'color' => '#FFF',
        'text'  => $this->getColorText( '#FFF' )
      ),
      'max'	=> array(
        'value'	=> $buckets[ count( $buckets ) - 1 ]['max_value'],
        'color'	=> $colors[ count( $colors ) - 1 ],
        'text'  => $this->getColorText( $colors[ count( $colors ) - 1 ] )
      ),
      'ranges'  => $buckets
    );

    //$this->printArray( $color_rules );

    return $color_rules;
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
			'title'	=> 'Map Violence',
      'url'   => admin_url('admin-ajax.php?action=map_data')
		), $atts, 'soah_map' );

    // Purple Colors - #B39DDB, #7E57C2, #5E35B1, #311B92
    // Red Colors - #FA8072, #ED2939, #B80F0A, #5E1914

    //RED COLOR SCHEME WITH CONTEXTUAL VALUES
    $atts['color_rules'] = array(
      'default' => '#EDE7F6',
      'min'	=> array(
        'value'	=> 1,
        'color'	=> '#FFF'
      ),
      'max'	=> array(
        'value'	=> 36,
        'color'	=> '#5e1914'
      ),
      'ranges'  => array(
        array(
          'min_value' => 26,
          'max_value'	=> 35,
          'color'			=> '#B80F0A'
        ),
        array(
          'min_value' => 11,
          'max_value'	=> 25,
          'color'			=> '#ED2939'
        ),
        array(
          'min_value' => 1,
          'max_value'	=> 10,
          'color'			=> '#FA8072'
        ),
      )
    );



		ob_start();

    include( "templates/map.php" );
		return ob_get_clean();
	}

}

CHOROPLETH_MAP::getInstance();
