<footer class='front-footer container wrap'>
  <div class='row'>
	  <h4>Specialties:</h4>
  </div>
  <div class='row'>
  	<nav class="nav-bottom">
  		<div class='row'>
	  		<ul>
				<div class='col-md-4'>
					<li><a href="/articles/" class="border">All</a></li>
				</div>
<?php 
$args = array(
  'type' => 'article',
  'exclude' => '1',
  'orderby' => 'count',
  'order' => 'DESC',
  'hide_empty' => 0
);
$categories = get_categories($args);
foreach($categories as $category) { 
	$cat_ID_text = "cat-item-" . $category->cat_ID; 
	$current = (is_category($category->cat_ID)) ? true : false;

	$post_args = array(
		'post_type' => 'article',
		'category' => $category->cat_ID,
		'post_status' => array('publish', 'preprint')
	);
	$posts = get_posts($post_args);

	$coming_soon = (count($posts) == 0) ? true : false;
	$notification_url = site_url() . "/notifications/?area=" . $category->cat_name;

	$cat_count = 2;

	if($cat_count == 1) { ?>
<div class='row'>
	<ul>
		<div class='col-md-4'>
			<li class="<?php if($coming_soon) echo 'coming-soon'?>"><a href="<?php if($coming_soon) echo $notification_url; else echo '/'.$category->slug.'/';?>" class="border"><?php echo $category->name; ?></a></li>
		</div>
<?php $cat_count++;
	} elseif ($cat_count == 2) { ?>
		<div class='col-md-4'>
			<li class="<?php if($coming_soon) echo 'coming-soon'?>"><a href="<?php if($coming_soon) echo $notification_url; else echo '/'.$category->slug.'/';?>" class="border"><?php echo $category->name; ?></a></li>
		</div>
<?php $cat_count++;
	} elseif ($cat_count == 3) { ?>
		<div class='col-md-4'>
			<li class="<?php if($coming_soon) echo 'coming-soon'?>"><a href="<?php if($coming_soon) echo $notification_url; else echo '/'.$category->slug.'/';?>" class="border"><?php echo $category->name; ?></a></li>
		</div>
	</ul>
</div>
<?php $cat_count = 1; 
	}
}
?>
</footer>

<script>
$('li').each(function(index){
	if($(this).hasClass('coming-soon')) {
		$(this).attr('og', $(this).find('a').text());
		$(this).hover( function() {
			$(this).find('a').text('Coming Soon');
		}, function() {
			$(this).find('a').text($(this).attr('og'));
		});
	}
});
</script>