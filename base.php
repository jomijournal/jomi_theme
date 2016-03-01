<?php
/*
Base is the entry point for most pages. 
*/
?>
<?php 
    // The Counter page is unique in that it directly serves xml, so it needs to be treated completely different than all the other pages.
    if(is_page('Counter')){ 
        require_once(ABSPATH.'/wp-content/themes/jomi/Counter/counter.php');
    } else {
        // The rest of this file fits in this else block.
        get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>
  <?php
    do_action('get_header');
    get_template_part('templates/header');
  ?>
  <div class="container wrap" role="document">
    <div class="content row">
      <main class="main <?php echo roots_main_class(); ?>" role="main">
        <?php include roots_template_path(); ?>
      </main>
      <!-- /.main -->
      <?php if (roots_display_sidebar() && !is_page('Usage Statistics') && !is_page('authors')) : ?>
        <aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
          <?php 
          if(is_page('socialinfo') || is_page('about') || is_page('contact') || is_page('pricing') || is_page('area-notification-request') || is_page('additional-journal-information') ) {
            include about_sidebar_path();
          } else if ( is_single()) {
            include article_sidebar_path();
          } else {
            include roots_sidebar_path();
          }
          ?>
        </aside>
        <!-- /.sidebar -->
      <?php endif; ?>
    </div>
    <!-- /.content -->
  </div>
  <!-- /.wrap -->

  <?php if(!is_front_page()){ ?>
    <?php get_template_part('templates/footer'); ?>
  <?php } ?>

</body>
</html>
<?php } ?>
