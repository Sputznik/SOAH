<?php if($atts['pagination'] != '0'):?>
<?php

  $paged = ( get_query_var('orbit-paged')) ? get_query_var('orbit-paged') : 1;

  $total_pages = $this->query->max_num_pages;

  if( $total_pages > 1 ){

    $current_page = max( 1, get_query_var('paged') );

    $big = 9999999;

    echo paginate_links( array(
      'base'       => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	    'format'     => '?paged=%#%',
      'current'    => $current_page,
      'total'      => $total_pages,
      'prev_text'  => __('« Prev'),
      'next_text'  => __('Next »'),
    ) );
  }

?>
<?php endif;?>
