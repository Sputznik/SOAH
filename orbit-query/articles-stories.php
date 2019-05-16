<ul id="<?php _e( $atts['id'] );?>" data-target="<?php _e('li.orbit-article');?>" data-url="<?php _e( $atts['url'] );?>" class="list-unstyled">
  <?php while( $this->query->have_posts() ) : $this->query->the_post();?>
	<li class="orbit-article" style="margin-bottom:30px;">
    <div class='orbit-post-image'>
    </div>
    <div class='orbit-post-desc'>
      <h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
    </div>
  </li>
  <?php endwhile;?>
</ul>
