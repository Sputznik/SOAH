<?php get_header();?>
<div class="container" style="max-width:800px;padding-top: 80px; padding-bottom: 80px;">
  <div class="row">
    <div class="col-sm-12"><div class="paper-box" style="border: #eee solid 1px;padding: 25px;margin-top:20px;box-shadow:#eee 4px 5px 5px 2px;">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h3><?php the_title();?></h3>
        <?php

          $metainfo = array(
            array(
              'icon'      => 'fa fa-calendar',
              'label'     => '',
              'shortcode' => '[orbit_date]'
            ),
            array(
              'icon'      => 'fa fa-map-marker',
              'label'     => '',
              'shortcode' => '[orbit_terms taxonomy="locations"]'
            ),
            array(
              'icon'      => 'fa fa-bell',
              'label'     => 'Reported: &nbsp;',
              'shortcode' => '[orbit_terms taxonomy="report-type"]'
            ),
            array(
              'icon'      => 'fa fa-users',
              'label'     => 'Affected Victims: &nbsp;',
              'shortcode' => '[orbit_terms taxonomy="victims"]'
            ),
          );

          foreach ($metainfo as $meta) {
            if( $meta['shortcode'] ){

              $value = do_shortcode( $meta['shortcode'] );

              if( $value ){
                _e("<p class='small'>");
                if( $meta['icon'] ){ _e( "<i class='". $meta['icon'] ."'></i> &nbsp; " ); }
                if( $meta['label'] ){ _e( "<label>". $meta['label'] ."</label>" ); }
                echo $value;
                _e("</p>");
              }

            }

          }
        ?>
        <hr>
        <?php the_content('Read the rest of this entry »'); ?>
        <hr>
        <p class="small"><strong>Spread the word:</strong><p>
        <?php
          $social_icons = array(
            'fb'  => array(
              'link'  => 'https://www.facebook.com/sharer.php?u='.get_the_permalink(),
              'icon'  => 'fa fa-facebook'
            ),
            'tw'  => array(
              'link'  => 'https://twitter.com/intent/tweet?text='.get_the_title().'&url='.get_the_permalink(),
              'icon'  => 'fa fa-twitter'
            ),
            'li'  => array(
              'link'  => 'https://www.linkedin.com/sharing/share-offsite/?url='.get_the_permalink(),
              'icon'  => 'fa fa-linkedin'
            ),
          );
          echo "<ul class='list-inline'>";
          foreach ($social_icons as $slug => $social_icon) {
            echo '<li><a target="_blank" href="'.$social_icon['link'].'"><i class="'.$social_icon['icon'].'"></i></a></li>';
          }
          echo "</ul>";
        ?>
      <?php endwhile; endif; ?>
    </div></div>
  </div>
</div>
<?php get_footer();?>
