<article <?php post_class(); ?>>
	<?php
		if ( has_post_thumbnail() ) {
		?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
				<?php the_post_thumbnail('large'); ?>
			</a>
		<?php
		}
	?>
	<div class='article-overlay'>
		<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php get_template_part('templates/entry-meta'); ?>
		<h4>
		<?php $a = get_coauthors(); $b = $a[0]; print($b->aim); ?>
		</h4>
	</div>
</article>
