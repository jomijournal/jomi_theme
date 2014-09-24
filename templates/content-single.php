<?php while (have_posts()) : the_post(); ?>
  <?php
  /**
   * ARTICLE ACCESS CHECK HERE
   */
  global $access_blocks;
  check_access();
  //block_deny();
  
  $id = get_the_ID();

  // get a custom stop time, if it exists
  // check wordpress meta first
  $custom_stop = get_post_meta($id, 'custom_stop', true);
  if(empty($custom_stop)) $custom_stop = get_field('custom_stop');

  // get custom time start from url
  $get_time_code = (empty($_GET['t'])) ? '' : $_GET['t'];



  ?>

  <?php 
  if(get_post_status() == "preprint") { ?>
  <div class="container preprint-container">
    <div class="preprint"><strong>PREPRINT</strong></div>
  </div>
  <?php } 
  ?>
  <div class="container video-container">
    <div class="video-area row">
      <div id="access_block" class="access-block">
        <div id="content" style="width: 100%; height: 100%;"></div>
      </div>
      <div id="chapters" class="col-sm-4">
        <ul></ul>
      </div>
      <div class="video-holder col-sm-8" id="video">
        <div id="wistia" class="wistia_embed">&nbsp;</div>
        <script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js"></script>
      </div>
    </div>
  </div>

  <div class="col-sm-8">
    <article <?php post_class(); ?>>
      <?php $wistia = get_field('wistia_id'); ?>

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
            <?php echo get_field('meta'); ?>
            <?php the_content(); ?>
            <h3>Citations</h3>
            <?php echo get_field('citations'); ?>
          </div>
          <div class="tab-pane" id="outline"><?php echo get_field('outline'); ?></div>
        </div>
      </div>
      <?php comments_template(); ?>
    </article>
  </div>
  <div class="col-sm-4">
    <?php require_once('sidebar-article.php'); ?>
  </div>
  <!-- replace state is an html5 feature. if ie8 tries to do this, it will stop the video from loading -->
  <!--[if gt IE 8]>
  <script>
    window.history.replaceState('', '', '/article/<?php echo get_field("publication_id"); ?>/<?php global $post; echo $post->post_name; ?>');
  </script>
  <![endif]-->
  <script>
  var blocked = false;

    $(function(){

      // load the wistia id (used for getting the video from wistia)
      $("#wistia").attr('id', 'wistia_<?php echo $wistia; ?>').show();
      wistiaEmbed = Wistia.embed("<?php echo $wistia; ?>", {
        // video foam = auto resizing (very good thing)
        videoFoam: true
      });

      // grab GET variables
      var time_code = "<?php echo $get_time_code; ?>";

      var sec_regex = /(\d*)(?=s)/g;
      var min_regex = /(\d*)(?=m)/g;
      var hr_regex = /(\d*)(?=h)/g;

      var seconds = sec_regex.exec(time_code);
      seconds = (seconds == null) ? 0 : parseInt(seconds[0]);

      var minutes = min_regex.exec(time_code);
      minutes = (minutes == null) ? 0 : parseInt(minutes[0]);

      var hours = hr_regex.exec(time_code);
      hours = (hours == null) ? 0 : parseInt(hours[0]);

      var total = seconds + (minutes * 60) + (hours * 3600);

      if(total > 0) {
        wistiaEmbed.time(total);
        wistiaEmbed.play();
      }

      // tracker for elapsed time (in seconds)
      var elapsed = 0;

      // runs each time the video advances a second
      wistiaEmbed.bind("secondchange", function (s) {

        //increment elapsed time
        elapsed++;

        //generate share url
        hours = Math.floor(wistiaEmbed.time() / 3600);
        minutes = Math.floor((wistiaEmbed.time() - (hours * 3600)) / 60);
        seconds = Math.floor(wistiaEmbed.time() - (hours * 3600) - (minutes * 60));

        //var share_url = window.location.href + '?t=';
        var share_url = "<?php echo (site_url() . '/article/' . get_field('publication_id') . '/?t='); ?>";
        if(hours > 0) share_url += (hours + 'h');
        if(minutes > 0) share_url += (minutes + 'm');
        if(seconds > 0) share_url += (seconds + 's');

        if(!$('#url-share-box').is(':focus')) {
          $('#url-share-box').attr('value',share_url);
        }

        //======================
        // GENERATED JAVASCRIPT
        // =====================
        <?php if(is_array($access_blocks)) { foreach($access_blocks as $block) { ?>
          <?php if($block['time_elapsed'] == 'custom' && !empty($custom_stop)) {?>
            // custom elapsed time
            if(elapsed >= <?php echo $custom_stop; ?>) {
              // block it
              block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
            }
          <?php } elseif($block['time_elapsed'] == 'custom' && empty($custom_stop)) {?>
            
          <?php } elseif($block['time_elapsed'] > 0) {?>
            if(elapsed >= <?php echo $block['time_elapsed']; ?>) {
              // block it
              block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
            }
          // custom start time
          <?php } elseif ($block['time_start'] == 'custom' && !empty($custom_stop)) { ?>
            if(s >= <?php echo $custom_stop ?>) {
              block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
            }
          <?php } elseif($block['time_start'] == 'custom' && empty($custom_stop)) {?>

          <?php } elseif ($block['time_start'] > 0) { ?>
            if(s >= <?php echo $block['time_start']; ?>) {
              block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
            }
          // block immediately
          <?php } else { ?>
            block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
          <?php } ?>

        <?php } }?>
        // ==========================
        // END GENERATED JAVASCRIPT
        // ==========================
        
        // chapter control
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

      $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
      });

      $('#meta-chapters section').each(function(){
        $('#chapters ul').append('<li class="vtime-item" data-time="'+$(this).data('time')+'"><a href="#video" onclick="wistiaEmbed.time('+$(this).data('time')+').play();">'+$(this).data('title')+'</a></li>');
      });

      $('#chapters').show();

      $('.nav-tabs li a').click(function (e) {
        history.pushState( null, null, $(this).attr('href') );
      });

      function block(msg, closable) {

        if(blocked) return;

        if(closable) blocked = true;

        var function_name = msg;

       $('.access-block').show();
       wistiaEmbed.pause();

       console.log(function_name);

       $.post(MyAjax.ajaxurl, {
        action: function_name,
        id: <?php echo get_the_ID(); ?>,
        msg: 'ACCESS RESTRICTED'
       }, 
       function(response) {
        response = response.substring(0, response.length - 1);
        $('.access-block').find('#content').empty().html(response);
       });
      }

      // scroll up function from the jquery plugin
      $.scrollUp({
        scrollName: 'scrollUp', // Element ID
        topDistance: '300', // Distance from top before showing element (px)
        topSpeed: 300, // Speed back to top (ms)
        animation: 'fade', // Fade, slide, none
        animationInSpeed: 200, // Animation in speed (ms)
        animationOutSpeed: 200, // Animation out speed (ms)
        //scrollText: 'Scroll to top', // Text for element
        activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
      });
    });
  </script>
<?php endwhile; ?>
