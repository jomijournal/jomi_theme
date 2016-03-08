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
	<meta name="citation_volume" content="<?php the_time('Y'); ?>">
	<meta name="citation_issue" content="<?php the_time('n'); ?>">
	<?php endwhile; ?>
	<?php endif; ?>

	<!-- scripts.php is injected here -->
	<?php wp_head(); ?>

	<!-- TYPEKIT -->
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo esc_url(get_feed_link()); ?>">

	<!-- OpenSearch -->
	<link title="<?php echo site_url('/'); ?>" href="<?php echo site_url('/opensearch.xml'); ?>" type="application/opensearchdescription+xml" rel="search">

	<!-- ONLY LOAD ANALYTICS FOR PRODUCTION SITE -->

	<?php if(WP_ENV == 'PROD') { ?>

	<!-- SEGMENT.IO -->
	<script type="text/javascript">
		window.analytics=window.analytics||[],window.analytics.methods=["identify","group","track","page","pageview","alias","ready","on","once","off","trackLink","trackForm","trackClick","trackSubmit"],window.analytics.factory=function(t){return function(){var a=Array.prototype.slice.call(arguments);return a.unshift(t),window.analytics.push(a),window.analytics}};for(var i=0;i<window.analytics.methods.length;i++){var key=window.analytics.methods[i];window.analytics[key]=window.analytics.factory(key)}window.analytics.load=function(t){if(!document.getElementById("analytics-js")){var a=document.createElement("script");a.type="text/javascript",a.id="analytics-js",a.async=!0,a.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.io/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n)}},window.analytics.SNIPPET_VERSION="2.0.9",
		window.analytics.load("g0tsfo2n3d");
		window.analytics.page();
	</script>

	<!-- HOTJAR -->
	<script>
	(function(f,b,g){
		var xo=g.prototype.open,xs=g.prototype.send,c;
		f.hj=f.hj||function(){(f.hj.q=f.hj.q||[]).push(arguments)};
		f._hjSettings={hjid:2618, hjsv:1};
		if(b.addEventListener){b.addEventListener("DOMContentLoaded",function(){f.hj.documentHtml=b.documentElement.outerHTML;c=b.createElement("script");c.async=1;c.src="//static.hotjar.com/c/hotjar-2618.js?sv=1";b.getElementsByTagName("head")[0].appendChild(c);})}
		if(!f._hjPlayback && b.addEventListener){
			g.prototype.open=function(l,j,m,h,k){this._u=j;xo.call(this,l,j,m,h,k)};
			g.prototype.send=function(e){var j=this;function h(){if(j.readyState===4){f.hj("_xhr",j._u,j.status,j.response)}}this.addEventListener("readystatechange",h,false);xs.call(this,e)};
		}
	})(window,document,window.XMLHttpRequest);
	</script>

	<?php } ?>

        <!-- start Mixpanel -->

        <script type="text/javascript">

	/* If the hostname is anything other than your production domain, initialize the Mixpanel library with your Development Token */

	(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
        for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);


	var productionHost = 'jomi.com';

	var devToken = '9f28013773e9c4bbed6df6d2f3013483';
	var prodToken = 'c75c83d6b279b9f623cfa461d7b9a8bc';
	if (window.location.hostname.toLowerCase().search(productionHost) < 0) {
    		mixpanel.init(devToken);
	} else {
    		mixpanel.init(prodToken);
	}	


</script>
        <!-- end Mixpanel -->



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
