<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article');?>" data-url="<?php _e( $atts['url'] );?>" class="list-unstyled">
  <?php while( $this->query->have_posts() ) : $this->query->the_post();?>
	<li class="orbit-article" style="margin-bottom:25px;">
    <h4 style="margin-bottom: 5px;"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
    <p class="small">Reported on <?php _e( do_shortcode( '[orbit_date]' ) );?></p>
  </li>
  <?php endwhile;?>
</ul>
