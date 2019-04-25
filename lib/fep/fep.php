<?php


$inc_files = array(
	'vars.php',
	'class-fep-form.php',
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
