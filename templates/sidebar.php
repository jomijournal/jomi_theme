<?php #dynamic_sidebar('sidebar-primary'); ?>
<?php $args = array(
  'type' => 'article',
  'exclude' => '1',
  'orderby' => 'count',
  'order' => 'DESC',
  'hide_empty' => 0
); ?>
<?php $categories = get_categories($args); ?>
<ul>
<li class="cat-item cat-item-all <?php if(is_page('articles')) echo 'current-cat';?>">
  <a href="<?php echo site_url();?>/articles/">All Articles</a>
</li>
<?php foreach($categories as $category) { ?>
  <li class="cat-item cat-item-<?php echo $category->cat_ID?> <?php if(is_category($category->cat_ID)) echo 'current-cat'; ?><?php if($category->count == 0) echo 'coming-soon' ?>">
    <a href="<?php if($category->count == 0) { echo site_url();?>/notifications/?area=<?php echo $category->cat_name; } else { echo get_category_link($category->cat_ID); }?>"><?php echo $category->cat_name; ?></a>
  </li>
<?php } ?>
</ul>

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