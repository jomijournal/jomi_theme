<?php
/*
Base is the entry point for most pages. 
*/
?>
<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>
  <?php
    do_action('get_header');
    get_template_part('templates/header-top-navbar');
  ?>
  <div class="container wrap" role="document">
    <div class="content row">
      <main class="main <?php echo roots_main_class(); ?>" role="main">
        <?php include roots_template_path(); ?>
      </main>
      <!-- /.main -->
      <?php if (roots_display_sidebar()) : ?>
        <aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
          <?php 
          if(is_page('about') || is_page('contact') || is_page('pricing') || is_page('area-notification-request')) {
            include about_sidebar_path();
          } else if ( is_single() ) {
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