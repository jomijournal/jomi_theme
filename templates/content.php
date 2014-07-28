<article <?php post_class(); ?>>
	<?php
		if ( in_array(get_post_status(), array('preprint', 'in_production', 'coming_soon') ) ) {

			// turn post status into pretty english
			$status = get_post_status();
			switch ($status) {
				case 'preprint':
					$status_text = 'Preprint';
					$link = get_permalink();
					break;
				case 'in_production':
					$status_text = 'In Production';
					$link = '/notifications?area=' . get_the_title();
					break;
				case 'coming_soon':
					$status_text = "Coming Soon";
					$link = '/notifications?area=' . get_the_title();
					break;
			}
		} else {
			$link = get_permalink();
		}
		if ( has_post_thumbnail() ) { ?>
		<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>" >
			<?php the_post_thumbnail('large'); ?>
		<?php } ?>
		<?php if(in_array($status, array('in_production', 'coming_soon'))) { ?>
			<div class='unavailable'>
				<h3><?php echo $status_text; ?></h3>
			</div>
		<?php } else if ($status == 'preprint') { ?>
			<h5><?php echo $status_text ?></h5>
		<?php } ?>
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
		</a>
</article>
