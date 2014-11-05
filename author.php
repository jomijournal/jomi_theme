<div id="content" class="narrowcolumn">

	<?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>

	<div class="author-info">
		<h4>About: <?php echo $curauth->nickname; ?></h4>
		<ul>
			<li>Institution: <?php echo $curauth->user_description; ?></li>
		</ul>
		<h4>Articles by <?php echo $curauth->nickname; ?>:</h4>
	</div>
	<ul>

	<?php
	$args = array(
		'post_type' => 'article',
		'post_status' => array('publish', 'preprint', 'coming_soon', 'in_production'),
		'posts_per_page' => -1,
		'caller_get_posts'=> 1,
		'author_name' => $author_name
	);
	$my_query = new WP_Query($args);
	?>

	<?php if ($my_query->have_posts()) : while ($my_query->have_posts()) : $my_query->the_post(); ?>
		<?php get_template_part('templates/article', 'thumbnail'); ?>
	<?php endwhile; else: ?>
		<p>No posts by this author.</p>
	<?php endif; ?>
	
	</ul>
</div>