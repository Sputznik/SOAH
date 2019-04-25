<?php

  $taxonomy = $first_taxonomy;
  if( isset( $_GET['action'] ) ){
    $taxonomy = $_GET['action'];
  }



  $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

  $labels = array();
  foreach ( $terms as $term ) {
    $labels[ $term->term_id ] = array(
      'label' => $term->name,
    );
  }


?>
<div class="wrap">
<?php
  if( class_exists('ORBIT_TRANSLATIONS') ){
    $orbit_translation = ORBIT_TRANSLATIONS::getInstance();

    $orbit_translation->formForLabels( $labels, $taxonomy, 'hi' );

}

?>
</div>
