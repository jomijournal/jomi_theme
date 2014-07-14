<p class="byline author vcard">
<?php
if ( function_exists( 'coauthors_posts_links' ) ) {
    coauthors_posts_links();
} else {
    the_author_posts_link();
}
?>
</a></p>
