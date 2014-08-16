<?php require_once('wistiaapi.php');
$w = new WistiaApi('181632d7604b54b358a5db48a85cec7d7665be6f');
$ID = get_field('wistia_id');
//echo $w->mediaShow($ID); 
echo '<pre>';
print_r($w);
//print_r($w->mediaShow($ID));
echo $ID;
print_r($w->projectList());
echo '</pre>';
?>

<div class="article-thumbnail">
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
				<h3 class="video-duration"><?php $w->mediaShow($ID)->duration; ?></h3>
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
</div>
