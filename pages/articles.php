<?php
/*
Template Name: Articles
*/
?>

<?php #get_template_part('templates/page', 'header'); ?>

<?php
$type = 'article';
$args=array(
  'post_type' => $type,
  'post_status' => array('publish', 'preprint'),
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);
$my_query = new WP_Query($args);

//global $num_articles;
//echo $num_articles;

if (!$my_query->have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
<?php endif; ?>

<div class='article-container'>
<?php

while ($my_query->have_posts()) : 
  $my_query->the_post();
  get_template_part('templates/content', get_post_format());
endwhile; 

$args=array(
  'post_type' => $type,
  'post_status' => array('coming_soon', 'in_production'),
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);
$my_query = new WP_Query($args);

while ($my_query->have_posts()) : 
  $my_query->the_post();
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

</div>
