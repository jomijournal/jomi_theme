<?php
global $user;

/* USERAPP */
/*
use \UserApp\Widget\User;

$auth = false;
if (User::authenticated()) {
	$auth = true;
}
else
{
	if(isset($_COOKIE["ua_session_token"]))
	{
		$token = $_COOKIE["ua_session_token"];
		try
		{
			$auth = User::loginWithToken($token);
		}
		catch(Exception $e)
		{
			//
		}
	}
}
if($auth)
{
	try{
		$user = User::current();
	}
	catch(Exception $e){
		echo $e;
	}
}
elseif(!is_front_page())
{
	//header("Location: /");
*/

/* LOGOUT */
/*
if($user && isset($_GET["logout"]))
{
	$user->logout();
	$user = null;
	if(isset($_COOKIE['ua_session_token'])) {
		unset($_COOKIE['ua_session_token']);
		setcookie('ua_session_token', '', time() - 3600, "/"); // empty value and old timestamp
	}
}
*/
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
	<!-- TYPEKIT -->
	<script type="text/javascript" src="//use.typekit.net/wjg6rds.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	<!-- FONT AWESOME -->
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

	<!-- ADDTHIS -->
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-538f69071761b8d2"></script>

	<?php wp_head(); ?>

	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">

	<script type="text/javascript">
	  window.analytics=window.analytics||[],window.analytics.methods=["identify","group","track","page","pageview","alias","ready","on","once","off","trackLink","trackForm","trackClick","trackSubmit"],window.analytics.factory=function(t){return function(){var a=Array.prototype.slice.call(arguments);return a.unshift(t),window.analytics.push(a),window.analytics}};for(var i=0;i<window.analytics.methods.length;i++){var key=window.analytics.methods[i];window.analytics[key]=window.analytics.factory(key)}window.analytics.load=function(t){if(!document.getElementById("analytics-js")){var a=document.createElement("script");a.type="text/javascript",a.id="analytics-js",a.async=!0,a.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.io/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n)}},window.analytics.SNIPPET_VERSION="2.0.9",
	  window.analytics.load("g0tsfo2n3d");
	  window.analytics.page();
	</script>
	<script>
	 var user = '<?php echo $user->user_id; ?>';
	</script>
</head>