<?php
/*
Might be good to move this into a separate widget and follow as standard custom sidebar inclusion practice.
 */
?>
<!--time class="published" datetime="<?php echo get_the_time('c'); ?>">
	Published on <?php echo get_the_date(); ?>
</time-->

<?php 
// To add - addthis widget.
?>

<?php dynamic_sidebar('sidebar-article'); ?>