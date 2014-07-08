<article <?php post_class(); ?> style="margin-bottom:35px;">
	<?php
		if ( has_post_thumbnail() ) {
		?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
				<?php the_post_thumbnail('large'); ?>
			</a>
		<?php
		}
	?>
	<header>
		<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php get_template_part('templates/entry-meta'); ?>
	</header>
</article>
