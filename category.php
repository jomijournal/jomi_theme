<?php 
// gets the id of the category we are in
$category_id = get_cat_id( single_cat_title("",false) );
// change default query. This gets rid of pagination so we can do our own sorting.

$type = 'article';
$args=array(
  'post_type' => $type,
  'posts_per_page' => -1,
  'cat' => $category_id
);
query_posts($args);

if (!have_posts()){
    echo '<div class="alert alert-warning">Sorry, no results were found.</div>';
}

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
     get_template_part('templates/article', 'thumbnail'); 
  endwhile; 
  
  rewind_posts();
}

?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <nav class="nav-post">
    <ul>
      <li class="previous"><?php next_posts_link   ('&larr; Older posts'); ?></li>
      <li class="next">   <?php previous_posts_link('Newer posts &rarr;'); ?></li>
    </ul>
  </nav>
<?php endif; ?>
