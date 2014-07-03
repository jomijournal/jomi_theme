<time class="published" datetime="<?php echo get_the_time('c'); ?>">Published on <?php echo get_the_date(); ?></time>
<p class="byline author vcard">
<?php
if ( function_exists( 'coauthors_posts_links' ) ) {
    coauthors_posts_links();
} else {
    the_author_posts_link();
}
?>
</a></p>
