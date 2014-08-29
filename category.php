<?php #get_template_part('templates/page', 'header'); ?>

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
<?php endif; ?>

<?php 
$exclude = array(
  'internal_review'
);
$bottom = array(
  'in_production',
  'coming_soon'
);

while (have_posts()) : the_post(); 
   if(in_array(get_post_status(), $exclude)) continue;
   if(in_array(get_post_status(), $bottom)) continue;
   get_template_part('templates/content', get_post_format()); 
endwhile; 

rewind_posts();

while (have_posts()) : the_post(); 
   if(in_array(get_post_status(), $exclude)) continue;
   if(!in_array(get_post_status(), $bottom)) continue;
   get_template_part('templates/content', get_post_format()); 
endwhile; 

?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <nav class="post-nav">
    <ul class="pager">
      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
    </ul>
  </nav>
<?php endif; ?>