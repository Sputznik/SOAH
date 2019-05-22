<?php

class CSV_HELPER extends SOAH_BASE{

	function __construct(){

		add_shortcode( 'soah_export', array( $this, 'export_shortcode' ) );

		add_action( 'wp_ajax_reset_locations', array( $this, 'reset_locations' ) );

		add_action( 'wp_ajax_reset_reports', array( $this, 'reset_reports' ) );

		add_action( 'wp_ajax_bulk_set_terms', array( $this, 'bulk_set_terms' ) );

		/* ACTION HOOK FOR AJAX CALL - import terms */
    add_action('orbit_batch_action_soah_export', function(){

			$orbit_csv = ORBIT_CSV::getInstance();

      // GET PARAMETERS
			$step = $_GET['orbit_batch_step'];
			$file_slug = $_GET['file_slug'];

			$header = array(
				'post_id',
				'post_title',
				'post_content',
				'post_date',
				'tax_locations',
				'tax_report-type',
				'tax_victims'
			);

			$headerInfo = $orbit_csv->getHeaderInfo( array( $header ) );

			// ADD HEADER ROW FOR THE FIRST BATCH REQUEST ONLY
			if( $step == 1 ){
				echo "<p>Header Row has been added in the CSV file</p>";
				$orbit_csv->addHeaderToCSV( $file_slug, $header );


			}

			$this->addReportsToCSV( $file_slug, $headerInfo );


		});


	}

	function addReportsToCSV( $file_slug, $headerInfo ){

		$orbit_csv = ORBIT_CSV::getInstance();

		$the_query = new WP_Query( array(
			'posts_per_page' => 3,
			'post_type'			 => 'reports',
			'post_status'		=> 'publish'
		) );

		print_r( $headerInfo );

		if ( $the_query->have_posts() ) {
      while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$row = array();

				global $post;

				// ACCUMULATING ALL POST INFORMATION
				foreach( $headerInfo['post_info'] as $slug => $value ){
					if( $slug == 'post_id' ){
						$slug = 'ID';
					}

					if( isset( $post->$slug ) ){ $row[ $value ] = $post->$slug; }
				}

				// ACCUMULATING ALL TAXONOMY RELATED INFORMATION
				foreach( $headerInfo['tax_info'] as $taxonomy => $value ){
					$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
					$term_names_arr = array();
					if( is_array( $terms ) && count( $terms ) ){
						foreach ( $terms as $term ){
	            array_push( $term_names_arr, $term->name );
	          }
					}
					$row[ $value ] = implode( ',', $term_names_arr );
	      }

				$orbit_csv->addRowToCSV( $file_slug, $row );

				echo "<pre>";
				print_r( $row );
				echo "</pre>";

				echo "<br>";

			}
			wp_reset_postdata();
		}



	}

	function export_shortcode( $atts ){
		$atts = shortcode_atts( array(
		), $atts, 'soah_export' );

		ob_start();
		include( 'templates/export.php' );
		return ob_get_clean();
	}



	function bulk_set_terms(){

		$count = 0;

		if( isset( $_GET['taxonomy1'] ) && ( isset( $_GET['taxonomy2'] ) ) && ( isset( $_GET['term1'] ) ) && isset( $_GET['term2'] ) ){
			$query = new WP_Query( array(
	    	'post_type' 		=> 'reports',
	      'posts_per_page'=>-1,
	      'tax_query' 		=> array(
	        array(
	          'taxonomy' => $_GET['taxonomy1'],
	          'field'    => 'slug',
	          'terms'    => $_GET['term1'],
	        ),
	     ) ) );

			while ($query->have_posts()) {
	    	$count++;
	      $query->the_post();
				global $post;
	      wp_set_object_terms( $post->ID , $_GET['term2'], $_GET['taxonomy2'] );
	    }
			wp_reset_query();
		}

		echo "<h1>$count posts have been updated</h1>";

		wp_die();
	}

	function reset_reports(){
		echo "Deleting reports <br>";
		$reports = get_posts( array('post_type'=>'reports','numberposts'=>-1) );
		print_r( $reports );

		foreach( $reports as $report ) {
			wp_delete_post( $report->ID, true );
		}

		wp_die();
	}

	function reset_locations(){
		$terms = get_terms( 'locations', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, 'locations' );
		}
	}

	function toArray( $path ){
		$orbit_csv = ORBIT_CSV::getInstance();
		return $orbit_csv->toArray( $path );
	}

	/*
	* USED IN IMPORT_REPORTS function
	* TAKES THE TAXONOMY AS PARAMETER
	* RETURNS AN ARRAY OF ELEMENTS WITH SLUG AND ID OF TERMS
	*/
	function getTermsArr( $taxonomy ){
	  $terms = get_terms( $taxonomy, array('hide_empty' =>  false));
	  $data = array();
	  foreach ($terms as $term) {
	    $slug = $this->slugify( $term->name );
	    $data[ $slug ] = $term->term_id;
	  }
	  return $data;
	}

	function slugify( $label ){
		return preg_replace('/\s+/', '', strtoupper( $label ) );
	}




}

CSV_HELPER::getInstance();
