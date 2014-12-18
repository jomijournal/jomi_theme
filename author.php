<div id="content" class="narrowcolumn">

	<?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>
	
	<div class="author-container">
		<h1><?php echo $curauth->nickname; ?></h1>
		<div class="author-info row">
			<div class="headshot col-xs-12 col-sm-4 col-md-2">
				<?php echo get_wp_user_avatar($curauth->ID); ?>
			</div>
			<div class="info col-xs-12 col-sm-6 col-md-6">
				
				<h4><?php echo $curauth->user_description; ?></h4>
			</div>
			
		</div>
		
		<div class="row">
			<h4 class="col-xs-12">Articles by <?php echo $curauth->nickname; ?>:</h4>
		</div>
		<div class="author-article-list row">

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
				<?php get_template_part('templates/article', 'thumbnail-sm'); ?>
			<?php endwhile; else: ?>
				<p>No posts by this author.</p>
			<?php endif; ?>
			<script>
				$(window).resize(function() {
					fitText();
				});
				$(document).ready(function() {
					fitText();
				});
				function fitText() {
					console.log('triggered');
					$('.author-article-list .entry-title').textfill({
						//explicitWidth: 200
						widthOnly: false,
						innerTag: 'a',
						maxFontPixels: 20
						//, debug: true
					});
				}
			</script>
		</div>
		
	</div>
</div>