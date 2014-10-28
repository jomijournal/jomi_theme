<?php 
// get current user info
global $current_user;
get_currentuserinfo();

?>

<div class="site-notification" id="site-notification" style="display:none;"></div>


<?php if(is_front_page()) : ?>
	<video id="video" autoplay preload loop class='background hidden-xs video-js' poster="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/vid_poster.jpg" data-setup='{"controls":false}'>
		<source id="video-source" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background_vid.mp4" type="video/mp4">
		<source id="video-source-webm" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background.webm" type="video/webm">
	</video>
	<img src='https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/background_mobile.jpg' class='background visible-xs'/>
	<div class='blackbox background'></div>

<?php endif; ?>

<nav class="navbar navbar-default site-header" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="logo"><a href="/"><img src="/wp-content/themes/jomi/assets/img/logo.png" alt="Journal of Medical Insight"></a></div>
		</div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

			<div class="form-group has-feedback">
		  		<input placeholder="Search Articles" type="text" name="login" class="form-control search-field" id="search-field"></input>
				<a id="search-submit"><span class="form-control-feedback glyphicon glyphicon-search search-icon"></span></a>
			</div>

			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown login-dropdown">
					<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Sign&nbsp;in</a>
					<div class="dropdown-menu pull-right" role="menu">

						<div id="greyout" class="greyout">
							<div id="signal" class="signal"></div>
						</div>

						<p class="error" id="error"></p>
						<p class="login-username">
							<label for="user_login">Username/Email</label>
							<input type="text" name="log" id="user_login" class="input" value="" size="20">
						</p>
						<p class="login-password">
							<label for="user_pass">Password (<a tabindex="3" href="/login/?action=lostpassword">Lost Password?</a>)</label>
							<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
						</p>
						<input name="rememberme" type="hidden" id="rememberme" value="forever" checked="checked">
						<div class="row login-buttons">
							<div class="col-xs-7">
								<p class="login-submit">
									<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In">
									<input type="hidden" name="redirect_to" value="/">
								</p>
							</div>
							<div class="col-xs-5">
								<a href="/register" class="register">Register</a>
							</div>
						</div>
						<div class="social-box">
							<?php do_action('oa_social_login'); ?>
						</div>
					</div>
				</li>
				<li class="logout-dropdown">
					<a id="logout-btn" href="#">Logout</a>
					<?php //wp_loginout($_SERVER['REQUEST_URI']); ?>
				</li>
				<!--li class="dropdown logout-dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" id="logout-btn" href="#"><?php echo get_avatar($current_user->ID,24); ?> <?php echo $current_user->user_login; ?></a>
					<div class="dropdown-menu" role="menu">
						<p><a>Help</a></p>
						<p><a>Logout</a></p>
					</div>
				</li-->
			</ul>
			<ul class="nav navbar-nav">
				<!---li><a href='/login/' class=" active <?php 			if( is_user_logged_in() ) echo " hidden"; else echo " visible-xs"; ?>">Sign in</a></li-->
				<li><a href="/about/" class="<?php 			if( is_page( 'about') ) echo " active"; ?>"      >About</a></li>
				<li><a href="http://blog.jomi.com" class="" target="_blank"                                  >Blog</a></li>
				<li><a href='/subscribers/' class="<?php 	if( is_page( 'subscribers') ) echo " active"; ?>">Subscribe</a></li>
				<li><a href="/articles/" class="<?php 			if( is_page( 'articles') ) echo " active"; ?>">Articles</a></li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>

<script>


	if ($(window).width() < 768) {
		$('#video-source').attr("src", "");
		$('#video-source-webm').attr("src", "");
	}
	/* VIDEO LOAD CONDITIONALS */
	$(window).resize(function() {
		if ($(window).width() < 768 && $('#video-source').attr("src") != '') {
			$('#video-source').attr('src', '');
			$('#video-source-webm').attr('src', '');
			$('#video').load();
		} else if ($(window).width() >= 768 && $('#video-source').attr("src") == ''){
			$('#video-source').attr('src', 'https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background_vid.mp4');
			$('#video-source-webm').attr('src', 'https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background.webm');
			$('#video').load();
		}
	});


	/* SIGNUP & LOGIN */
	$(function() {



		$('.login-submit #submit').on('click', function(e) {
			e.preventDefault();

			// input idiotproofing
			var login = $('input[name="log"]').val();
			if(login === '') {
				//console.log('no username specified');
				$('#error').text("ERROR: No username entered");
				$('#error').show();
				return;
			}
			var pass = $('input[name="pwd"]').val();
			if(pass === '') {
				//console.log('no password specified');
				$('#error').text("ERROR: No password entered");
				$('#error').show();
				return;
			}

			// visual indicator
			$('#greyout,#signal').show();

			// login ajax
			$.post(MyAjax.ajaxurl, {
				action: 'ajax-login',
				username: login,
				password: pass,
				remember: true
			}, function(response) {

				response = response.substr(0, response.length - 1);
				console.log(response);
				$('#greyout,#signal').hide();

				if(response == "success") {

					$('.login-dropdown').hide();
					$('.login-dropdown').dropdown('toggle');
					
					$('.logout-dropdown').show();
					//$('a#logout-btn').show();
					
				} else {
					$('#error').text("Incorrect username or password");
					$('#error').show();
				}
			});
		});

		// logout ajax
		$('#logout-btn').on('click', function() {

			//$('#greyout,#signal').show();

			$.post(MyAjax.ajaxurl, {
				action: 'ajax-logout'
			}, function(response) {
				//$('#greyout,#signal').hide();

				//$('.login-dropdown').show();
				//$('.logout-dropdown').hide();

				//refresh page
				window.location.reload();
			});
		});

		$('.logout-dropdown').show();
		$('.logout-dropdown a').text("Logout");
		$('.login-dropdown').show();

		//$('#logout-btn').show();
		<?php if(is_user_logged_in()) { ?>
			$('.login-dropdown').hide();
		<?php } else { ?>
			$('.logout-dropdown').hide();
			//$('#logout-btn').hide();
		<?php } ?>

		$('#error').hide();

		// Setup drop down menu
		$('.dropdown-toggle').dropdown();

		// Fix input element click problem
		$('.dropdown input, .dropdown label').click(function(e) {
			e.stopPropagation();
		});

		//search
		<?php if(is_search()) {?>
		$('#search-field').attr('value','<?php echo get_search_query(); ?>');
		<?php } ?>
		$('#search-field').keydown(function(event){
			if(event.which == 13)
			{
				event.preventDefault();
				window.location.href = "/?s="+$(this).val();
			}
		});
		$('#search-submit').on('click', function() {
			window.location.href = "/?s="+$('#search-field').val();
		});
	});

</script>