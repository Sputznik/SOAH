<?php

$inc_files = array(
	'class-soah-base.php',
	'fep/fep.php',
	'cpt/cpt.php',
	'map/map.php'
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
