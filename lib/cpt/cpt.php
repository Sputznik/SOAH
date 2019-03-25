<?php

include( 'class-csv-helper.php' );

add_filter( 'orbit_post_type_vars', function( $post_types ){

	$post_types['reports'] = array(
		'slug' 		=> 'reports',
		'labels'	=> array(
			'name' 			=> 'Reports',
			'singular_name' => 'Report',
		),
		'public'	=> true,
		'supports'	=> array('title','editor')
	);

	return $post_types;
} );

/* PUSH INTO THE GLOBAL VARS OF ORBIT TAXNOMIES */
add_filter( 'orbit_taxonomy_vars', function( $taxonomies ){

	$taxonomies['report-type']	= array(
		'label'			=> 'Report Type',
		'slug' 			=> 'report-type',
		'post_types'	=> array( 'reports' )
	);

	$taxonomies['victims']	= array(
		'label'			=> 'Victims',
		'slug' 			=> 'victims',
		'post_types'	=> array( 'reports' )
	);

	$taxonomies['locations']	= array(
		'label'			=> 'Location',
		'slug' 			=> 'locations',
		'post_types'	=> array( 'reports' )
	);

	return $taxonomies;

} );
