<?php while (have_posts()) : the_post(); ?>
  <?php
  /**
   * ARTICLE ACCESS CHECK HERE
   */
  
  global $wpdb;
  global $access_table_name;

  $selector_meta = extract_selector_meta(get_the_ID());
  echo '<pre>';
  //print_r($selector_meta);
  //echo $selector_meta['status'];
  $institution_meta = extract_institution_meta();
  //print_r($institution_meta);
  //$institution_id = $institution_meta['id'];

  $all_rules_query = "SELECT * 
                      FROM $access_table_name";
  $all_rules = $wpdb->get_results($all_rules_query);
  //print_r($all_rules);

  $rules = collect_rules($selector_meta, $institution_meta);

  $check_info = load_check_info();

  check_access($rules, $check_info);

  echo '</pre>';

  ?>
  <article <?php post_class(); ?>>
    <?php $wistia = get_field('wistia_id'); ?>
    <script>
      $("#wistia").attr('id', 'wistia_<?php echo $wistia ?>').show();
      wistiaEmbed = Wistia.embed("<?php echo $wistia ?>", {
        videoFoam: true
      });
      wistiaEmbed.bind("secondchange", function (s) {
        //if(s > 60*10 && !is_user_logged_in()) {
        //  $('.wistia_embed').empty().append('<h2 style="color:#fff">Please sign in to watch the rest of the video.</h2><a href="/" class="btn white fat" style="margin-top:25px">Back to front page</a>').attr('style', 'height: 100%;text-align: center;padding-top: 150px;padding-bottom: 150px;border: 3px solid #eee;');
        //}
        $('.vtime-item').removeClass('done').removeClass('current');
        $('.vtime-item').each(function(index){
          if($(this).data('time') < s)
          {
            $(this).addClass('done');
          }
          else
          {
            $('.vtime-item:nth-child('+index+')').addClass('current');
            return false;
          }
        });
      });
    </script>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
      <ul class="nav nav-tabs" role="tablist" data-toggle="tabs">
        <li class="active"><a href="#main" data-toggle="tab">Main Text</a></li>
        <li><a href="#outline" data-toggle="tab">Procedure Outline</a></li>
      </ul>
    </header>
    <div class="entry-content">
      <div class="tab-content">
        <div class="tab-pane active" id="main">
          <?php the_content(); ?>
          <h3>Citations</h3>
          <?php the_block('citations'); ?>
        </div>
        <div class="tab-pane" id="outline"><?php the_block('outline'); ?></div>
      </div>
    </div>

    <script>
      window.history.replaceState('', '', '/article/<?php echo get_field("publication_id"); ?>/<?php global $post; echo $post->post_name; ?>');
      $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
      $(document).ready(function(){
        $('#meta-chapters section').each(function(){
          $('#chapters ul').append('<li class="vtime-item" data-time="'+$(this).data('time')+'"><a href="#video" onclick="wistiaEmbed.time('+$(this).data('time')+').play();">'+$(this).data('title')+'</a></li>');
        });
        $('#chapters').show();
      });
    </script>
    <script>
    $('.nav-tabs li a').click(function (e) {
    history.pushState( null, null, $(this).attr('href') );
    });
    </script>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
  </article>
  <?php comments_template(); ?>
<?php endwhile; ?>
