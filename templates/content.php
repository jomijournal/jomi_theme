<?php 

$id = get_the_ID();
$status = get_post_status($id);

if ( in_array($status, array('preprint', 'in_production', 'coming_soon') ) ) {
	// turn post status into pretty english
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

$categories = get_the_category($id);
//print_r($categories);

?>

<div class="article-thumbnail">

		<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>" >
		<?php 
		// load featured image if available. if not, load ugly default image
		if ( has_post_thumbnail() ) { ?>
			<?php the_post_thumbnail('large'); ?>
		<?php } else { ?>
			<img width="750" height="300" src="<?php echo ABSPATH . '/wp-content/themes/jomi/assets/img/01_standard_white.png';?>" class="attachment-large wp-post-image" alt="video thumbnail">
		<?php } ?>

		<?php 
		// cover unavailable articles in a dark grey mask
		if(in_array($status, array('in_production', 'coming_soon'))) { ?>
			<div class='unavailable'>
				<h3><?php echo $status_text; ?></h3>
			</div>

		<?php } ?>

		<div class="article-badges">

			<?php if ($status == 'preprint') { ?>
				<p class="preprint-badge"><?php echo $status_text ?></p>
			<?php } ?>
			

			<?php foreach($categories as $category) { 
				if($category->slug == 'fundamentals') { ?>
					<p class="fundamentals-badge">Fundamental</p>
				<?php } elseif($category->slug == 'orthopedics') { ?>
					<p class="orthopedics-badge">Orthopedic</p>
				<?php }
			} ?>

		</div>
		<?php 
		// show video duration
		if(in_array($status, array('preprint', ''))) {?>
			<div class="duration">
				<?php
					$id = get_the_ID();
					$vid_length = get_post_meta($id, 'vid_length', false);
					$vid_length = array_pop($vid_length);
				?>
				<p class="duration-text"><?php echo $vid_length; ?></p>
			</div>
		<?php } ?>
			<div class='article-overlay'>
				<a href="<?php echo $link; ?>" title="<?php the_title_attribute(); ?>" ><span></span></a>
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
</div>