<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <?php $wistia = get_post_meta( $post->ID, 'wistia', true ); ?>
    <script>
      $("#wistia").attr('id', 'wistia_<?php echo $wistia ?>').show();
      wistiaEmbed = Wistia.embed("<?php echo $wistia ?>", {
        videoFoam: true
      });
      </script>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#main">Main Text</a></li>
        <li><a href="#outline">Procedure Outline</a></li>
      </ul>
    </header>
    <div class="entry-content">
      <div class="tab-content">
        <div class="tab-pane active" id="main"><?php the_content(); ?></div>
        <div class="tab-pane" id="outline"><?php the_block( 'Procedure Outline' ); ?></div>
      </div>
    </div>
    <script>
      $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
    </script>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
  </article>
<?php endwhile; ?>