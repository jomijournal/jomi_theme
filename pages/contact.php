<?php
/*
Template Name: Contact
*/
get_template_part('page', 'header');
?>

<?php while (have_posts()) : the_post(); ?>
  <div class='contact'>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#thoughts" role="tab" data-toggle="tab">Send your Thoughts</a></li>
      <li><a href="#request" role="tab" data-toggle="tab">Request a Topic</a></li>
      <li><a href="#propose" role="tab" data-toggle="tab">Propose a Procedure</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="thoughts"><br><?php echo do_shortcode('[contact-form-7 id="247" title="Send Thoughts"]'); ?></div>
      <div class="tab-pane" id="request"><br>        <?php echo do_shortcode('[contact-form-7 id="246" title="Request a Topic"]'); ?></div>
      <div class="tab-pane" id="propose"><br>        <?php echo do_shortcode('[contact-form-7 id="243" title="Propose a Procedure"]'); ?></div>
    </div>
  </div>

<?php endwhile; ?>