<?php get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
<?php endif; ?>

<?php 
$exclude = array(
  'internal_review'
);
$status_order = array(
  'publish', 
  'preprint', 
  'in_production', 
  'coming_soon'
);

foreach($status_order as $status) {
  while (have_posts()) : the_post(); 
     if(in_array(get_post_status(), $exclude)) continue;
     if($status != get_post_status()) continue;
     get_template_part('templates/content', get_post_format()); 
  endwhile; 
  rewind_posts();
}

?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <nav class="nav-post">
    <!--hr-->
    <ul>
      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
    </ul>
  </nav>
<?php endif; ?>
