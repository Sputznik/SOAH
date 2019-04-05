<?php

class FEP_FORM extends SOAH_BASE{

	function __construct(){

		add_shortcode( 'soah_fep', array( $this, 'shortcode' ) );
	}

	function getOptionsFromTaxonomy( $taxonomy, $args = array( 'hide_empty' => false, 'orderby' => 'term_id' ) ){

		$args['taxonomy'] = $taxonomy;

		$options = array();
		$terms  =   get_terms( $args );
		foreach( $terms as $term ){
			$temp = array(
				'slug'  => $term->term_id,
				'title' => $term->name
			);

			if( isset( $term->description ) && $term->description ){
				$temp['title'] = $temp['title']." <small>(".$term->description.")</small>";
			}

			array_push( $options, $temp );
		}
		return $options;
	}
	function display_field( $field ){
		$field['class'] = isset( $field['class'] ) ? $field['class']." form-field" : "form-field";
		echo "<div class='".$field['class']."'>";
		echo "<label>".$field['label']."</label>";
		include( "templates/" . $field['type'] . ".php" );
		echo "</div>";
	}
	function display_section( $section ){
		echo "<section class='".$section['class']."'>";
		if( isset( $section['title'] ) ){
			_e( "<h3>".$section['title']."</h3>" );
		}
		if( isset( $section['desc'] ) ){
			_e( "<p class='section-desc'>".$section['desc']."</p>" );
		}
		echo "<div class='section-fields'>";
		foreach( $section['fields'] as $field ){
			$this->display_field( $field );
		}
		echo "</div>";
		echo "</section>";
	}

	function shortcode( $atts ){
		ob_start();
		include( 'templates/form.php' );
		return ob_get_clean();
	}

	function getLabels(){
		$labels = apply_filters( 'soah-fep-labels', array() );
		return $labels;
	}

}


FEP_FORM::getInstance();

include( 'vars.php' );
