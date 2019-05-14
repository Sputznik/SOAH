<?php if($atts['pagination'] != '0'):?>
<?php //print_r( $atts );
// the_posts_pagination(
//   array(
//     'mid_size'  =>  '2',
//     'prev_text' =>  'Previous Posts',
//     'next_text' =>  'Next'
//   )
// );

  $paged = ( get_query_var('orbit-paged')) ? get_query_var('orbit-paged') : 1;

  $total_pages = $this->query->max_num_pages;


  if( $total_pages > 1 ){

    $current_page = max( 1, get_query_var('orbit-paged') );

    echo paginate_links(array(
      'base' => get_pagenum_link(1) . '%_%',
      'format' => '?orbit-paged/%#%/',
      'current' => $current_page,
      'total' => $total_pages,
      'prev_text'    => __('« Prev'),
      'next_text'    => __('Next »'),
    ));
  }

?>
<?php endif;?>
