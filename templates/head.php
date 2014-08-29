<?php
global $user;
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php wp_title('|', true, 'right'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php if ( have_posts() and is_single()) : while ( have_posts() ) : the_post(); ?>
	<!-- GOOGLE SCHOLAR META DATA / SEO -->
	<meta name="citation_title" content="<?php echo the_title(); ?>">
	<?php
	$coauthors = get_coauthors();
	foreach( $coauthors as $coauthor ) { ?>
	<meta name="citation_author" content="<?php echo $coauthor->last_name; ?>, <?php echo $coauthor->first_name; ?>">
	<?php } ?>
	<meta name="citation_journal_title" content="Journal of Medical Insight">
	<meta name="citation_publication_date" content="<?php echo the_date(); ?>">
	<meta name="citation_volume" content="<?php #the_year(); ?>">
	<meta name="citation_issue" content="<?php #the_month(); ?>">
	<?php endwhile; ?>
	<?php endif; ?>

	<!-- scripts.php is injected here -->
	<?php wp_head(); ?>

	<!-- TYPEKIT -->
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">

	<!-- SEGMENT.IO -->
	<script type="text/javascript">
	  window.analytics=window.analytics||[],window.analytics.methods=["identify","group","track","page","pageview","alias","ready","on","once","off","trackLink","trackForm","trackClick","trackSubmit"],window.analytics.factory=function(t){return function(){var a=Array.prototype.slice.call(arguments);return a.unshift(t),window.analytics.push(a),window.analytics}};for(var i=0;i<window.analytics.methods.length;i++){var key=window.analytics.methods[i];window.analytics[key]=window.analytics.factory(key)}window.analytics.load=function(t){if(!document.getElementById("analytics-js")){var a=document.createElement("script");a.type="text/javascript",a.id="analytics-js",a.async=!0,a.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.io/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n)}},window.analytics.SNIPPET_VERSION="2.0.9",
	  window.analytics.load("g0tsfo2n3d");
	  window.analytics.page();
	</script>

	<!-- LEGACY SCRIPTS N STUFF -->
	<!--[if lt IE 9]>
	  <script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendor/respond.min.js"></script>
	  <script src="<?php echo get_template_directory_uri(); ?>/assets/js/vendor/placeholders.min.js"></script>
	  <link href="//vjs.zencdn.net/4.7/video-js.css" rel="stylesheet">
	  <script src="//vjs.zencdn.net/4.7/video.js"></script>
	  <style>
	  body{
		font: 10pt/1.6 Arial !important;
	  }
	  </style>
	<![endif]-->

	
</head>