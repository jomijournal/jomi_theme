<footer class="content-info" role="contentinfo">
	<div class="footer">
		<div class="container">
			<div class="row">
				<div class="col-sm-5 col-xs-12">
				<a href="<?php site_url('/'); ?>"><img src="<?php echo site_url('/wp-content/themes/jomi/assets/img/logo.png'); ?>" alt="Journal of Medical Insight"></a>
				<h5>ISSN: 2373-6003</h5>
				</div>
				<div class="col-sm-2 col-xs-6">
					<h3>About</h3>
					<ul class="about-list">
						<li><a                 href="<?php echo site_url('/about/'); ?>">About JoMI</a></li>
						<li><a                 href="<?php echo site_url('/index/'); ?>">Article Index</a></li>
						<li><a target="_blank" href="blog.jomi.com">JoMI Blog</a></li>
						<li><a target="_blank" href="http://eepurl.com/GL-7L">Newsletter</a></li>
						<li><a                 href="<?php echo site_url('/pricing/'); ?>">Pricing</a></li>
					</ul>
				</div>
				<div class="col-sm-3 col-xs-6">
					<h3>Contact</h3>
					<a class="contact-us" href="<?php echo site_url('/contact/'); ?>">Contact Us</a>
					<br>
					<!--Phone: 555-555-5555-->
					101 Arch Street, Suite 1950
					<br>
					Boston, MA 02110
					<br>
					Email: <a href="mailto:contact@jomi.com">contact@jomi.com</a>
					<br>
					<br>
				</div>
				<div class="col-sm-2 col-xs-12">
					<h3>Connect</h3>
					<ul class="social-icons">
						<li>
							<a href="http://www.facebook.com/JomiJournal" target="_blank" data-original-title="Facebook" class="social_facebook"></a>
						</li>
						<li>
							<a href="https://twitter.com/JoMIJournal" target="_blank" data-original-title="Twitter" class="social_twitter"></a>
						</li>
						<li>
							<a href="https://plus.google.com/107119186321929822567/posts" target="_blank" data-original-title="Google Plus" class="social_googleplus"></a>
						</li>
						<!--li>
							<a href="#" data-original-title="Feed" class="social_rss"></a>
						</li-->
					</ul>

				</div>
			</div>
		</div>
	</div>
</footer>

<script src="https://app.userapp.io/js/userapp.client.js"></script>
<script src="<?php echo site_url('/wp-content/themes/jomi/assets/js/vendor/cookies.min.js?v=2'); ?>"></script>
<script>
	browserBlast({
		devMode: false, // Show warning on all browsers for testing
		supportedIE: '9', // Supported IE version, warning will display on older browsers
		message: 'Hey! Your browser is unsupported. Please <a href="http://browsehappy.com" target="_blank">upgrade</a> for the best experience.' // Set custom message
	});

	$(function() {
		$("img.lazy").show().lazyload({
			threshold : 200,
			effect : "fadeIn"
		});
	});
</script>

<?php wp_footer(); ?>