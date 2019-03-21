<?php

$inc_files = array(
	'class-soah-base.php',
	'fep/class-fep-form.php',
	'cpt/cpt.php'
);
	
foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}