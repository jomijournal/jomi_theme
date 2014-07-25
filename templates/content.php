<article <?php post_class(); ?>>
	<?php
		if ( in_array(get_post_status(), array('preprint', 'in_production', 'coming_soon') ) {
			$link = '/notifications';
		} else {
			$link = get_permalink();
		}
		if ( has_post_thumbnail() ) {
		?>
			<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>" >
				<?php the_post_thumbnail('large'); ?>
			</a>
		<?php
		}
	?>
	<div class='article-overlay'>
		<h3 class="entry-title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
		<p class="byline vcard">
		<?php
			if ( function_exists( 'coauthors_posts_links' ) ) {
			    coauthors_posts_links();
			} else {
			    the_author_posts_link();
			}
		?>
		</p>
		<h4><?php $a = get_coauthors(); $b = $a[0]; print($b->description); ?></h4>
	</div>
</article>
