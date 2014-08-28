<?php while (have_posts()) : the_post(); ?>
  <?php
  /**
   * ARTICLE ACCESS CHECK HERE
   */
  global $access_blocks;
  check_access();
  //block_deny();
  ?>
  <article <?php post_class(); ?>>
    <?php $wistia = get_field('wistia_id'); ?>
    <script>

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
    <!--[if gt IE 8]>
    <script>
      window.history.replaceState('', '', '/article/<?php echo get_field("publication_id"); ?>/<?php global $post; echo $post->post_name; ?>');
    </script>
    <![endif]-->
    <script>
    var blocked = false;



      $(document).ready(function(){
        $("#wistia").attr('id', 'wistia_<?php echo $wistia ?>').show();
        wistiaEmbed = Wistia.embed("<?php echo $wistia ?>", {
          videoFoam: true
        });

        // tracker for elapsed time (in seconds)
        var elapsed = 0;

        wistiaEmbed.bind("secondchange", function (s) {

          elapsed++;

          // GENERATED JAVASCRIPT
          <?php if(is_array($access_blocks)) { foreach($access_blocks as $block) { ?>

            <?php if($block['time_elapsed'] > 0) {?>
              if(elapsed == <?php echo $block['time_elapsed']; ?>) {
                // block it
                block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? true : false;?>);
              }
            <?php } elseif ($block['time_start'] > 0) { ?>
              if(s >= <?php echo $block['time_start']; ?>) {
                block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? true : false;?>);
              }
            <?php } elseif ($block['time_end'] > 0) { ?>
              if(s <= <?php echo $block['time_end']; ?>) {
                block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? true : false;?>);
              }
            // block immediately
            <?php } else { ?>
              block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? true : false;?>);
            <?php } ?>

          <?php } }?>
          // END GENERATED JAVASCRIPT
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
          console.log(response);
          response = response.substring(0, response.length - 1);
          $('.access-block').find('#content').empty().html(response);
         });
        }

      });
    </script>
  </article>
  <?php comments_template(); ?>
<?php endwhile; ?>
