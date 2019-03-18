<?php get_header();?>
        <h1>Hello Wordpress</h1>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php the_content('Read the rest of this entry »'); ?>
				<?php endwhile; endif; ?>
<?php get_footer();?>
