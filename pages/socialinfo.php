<?php
/*
Template Name: Social Login Information
*/
get_template_part('page','header');
?>

<?php while (have_posts()) : the_post(); ?>
    <div class='contact'><?php echo do_shortcode('[contact-form-7 id="3122", title="Social Login Information"]'); ?></div>
<?php endwhile; ?>
