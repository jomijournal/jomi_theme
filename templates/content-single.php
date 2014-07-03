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
    </header>
    <div class="entry-content">
      <section data-title="Main Text">
        <?php the_content(); ?>
      </section>
      <section data-title="Procedure Outline">
        <?php the_block( 'Procedure Outline' ); ?>
      </section>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
  </article>
<?php endwhile; ?>