<?php get_template_part('templates/page', 'header'); ?>

<div id="content" class="narrowcolumn">

<!-- This sets the $curauth variable -->

    <?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    ?>

    <h4>About: <?php echo $curauth->nickname; ?></h4>
    <ul>
        <!--li>Website</li>
        <li><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></li-->
        <li>Institution: <?php echo $curauth->user_description; ?></li>
    </ul>

    <h4>Posts by <?php echo $curauth->nickname; ?>:</h4>

    <ul>
<!-- The Loop -->

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php get_template_part('templates/content', get_post_format()); ?>
    <?php endwhile; else: ?>
        <p><?php _e('No posts by this author.'); ?></p>

    <?php endif; ?>

<!-- End Loop -->

    </ul>
</div>