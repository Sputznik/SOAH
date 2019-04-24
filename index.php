<?php get_header();?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php the_content('Read the rest of this entry »'); ?>
				<?php endwhile; endif; ?>
<?php get_footer();?>
