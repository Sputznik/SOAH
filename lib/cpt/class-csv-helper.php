<?php

class CSV_HELPER extends SOAH_BASE{
	
	function __construct(){
		
		add_action( 'wp_ajax_import_victims', array( $this, 'import_victims' ) );
		
		add_action( 'wp_ajax_import_categories', array( $this, 'import_categories' ) );
		
		add_action( 'wp_ajax_import_locations', array( $this, 'import_locations' ) );
		//add_action( 'wp_ajax_reset_locations', array( $this, 'reset_locations' ) );
	}
	
	function exportToArray( $path ){
		$file = fopen( $path, "r" );
		$arrayCsv = array();
		while( !feof( $file ) ) {
			$fpTotal = fgetcsv( $file );
			array_push( $arrayCsv, $fpTotal );
		}
		fclose( $file );
		return $arrayCsv;
	}
	
	// CHECK IF THE TERM EXISTS, IF NOT CREATE A NEW TERM
	function getTermID( $text, $taxonomy, $parent = 0 ){
		$term = term_exists( $text, $taxonomy, $parent );
		
		if( !$term ){
			
			
			// TERM DOES NOT EXIST, SO CREATE NEW TERM
			$term = wp_insert_term( $text, $taxonomy, array( 'parent' => $parent ) );
			
		}
		
		
		
		if( isset( $term['term_id'] ) ){ return $term['term_id']; }
		return 0;
	}
	/*
	function reset_locations(){
		$terms = get_terms( 'locations', array( 'fields' => 'ids', 'hide_empty' => false ) );
		foreach ( $terms as $value ) {
			wp_delete_term( $value, 'locations' );
		}
	}
	*/
	
	function import_locations(){
		
		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/locations.csv';
		
		$this->syncTerms( $csv_path, 'locations' );
		
	}
	
	function import_categories(){
		
		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/categories.csv';
		
		$this->syncTerms( $csv_path, 'report-type' );
		
	}
	
	function import_victims(){
		
		$csv_path = get_stylesheet_directory().'/lib/cpt/csv/victims.csv';
		
		$this->syncTerms( $csv_path, 'victims' );
		
	}
	
	function syncTerms( $csv_path, $taxonomy ){
		
		$arrayCsv = $this->exportToArray( $csv_path );
		
		$i = 0;
		foreach( $arrayCsv as $rowCsv ){
			
			if( $i ){
				
				$parent_id = $this->getTermID( $rowCsv[0], $taxonomy );
				
				if( $parent_id && count( $rowCsv ) > 1 ){
					$this->getTermID( $rowCsv[1], $taxonomy, $parent_id );
				}
				
				echo '<pre>';
				print_r( $rowCsv );
				echo '</pre>';
			}
			$i++;
		}
		
		wp_die();
	}
  
}

CSV_HELPER::getInstance();