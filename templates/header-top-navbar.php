<?php
global $user;
?>
<?php if(is_front_page()) : ?>
	<video id="video" autoplay preload loop class='background hidden-xs video-js' poster="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/vid_poster.jpg" data-setup='{"controls":false}'>
		<source id="video-source" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background_vid.mp4" type="video/mp4">
		<source id="video-source-webm" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background.webm" type="video/webm">
	</video>
	<img src='https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/background_mobile.jpg' class='background visible-xs'/>
	<div class='blackbox background'></div>

<?php endif; ?>

<nav class="navbar navbar-default site-header" role="navigation">
	<div class="container-fluid container">
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
			<form class="navbar-form navbar-left box-shadow-light" role="form">
				<div class="form-group has-feedback">
			  		<input placeholder="Search Articles" type="text" name="login" size="30" class="form-control search-field" id="search-field"></input>
					<!--i class="form-control-feedback glyphicon glyphicon-search search-icon"></i-->
					<a id="search-submit"><span class="form-control-feedback glyphicon glyphicon-search search-icon"></span></a>
				</div>
				
			</form>

			<ul class="nav navbar-nav navbar-right">
				<?php //if(!is_user_logged_in()): ?>
				<li class="dropdown hidden-xs">
					<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Sign&nbsp;in</a>
						<div class="dropdown-menu pull-right">
							<div class="login-form" id="login-form">
								<form name="loginform" id="loginform" action="">
									<div id="greyout" class="greyout">
										<div id="signal" class="signal"></div>
									</div>
									<p class="error" id="error"></p>
									<p class="login-username">
										<label for="user_login">Username/Email</label>
										<input type="text" name="log" id="user_login" class="input" value="" size="20">
									</p>
									<p class="login-password">
										<label for="user_pass">Password</label>
										<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
									</p>
									<input name="rememberme" type="hidden" id="rememberme" value="forever" checked="checked">
									<div class="row">
										<div class="col-sm-7">
											<p class="login-submit">
												<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In">
												<input type="hidden" name="redirect_to" value="/">
											</p>
										</div>
										<div class="col-sm-5">
											<p>
												<a href="/register" class="register">Register</a>
											</p>
										</div>
									</div>
									<br>
									<!--p class="login-register" style="width:45%">
										<a href="/register"><btn type="register" name="register" id="register" class="btn btn-default" value="Register"/></a>
									</p-->
									<div class="social-box">
										<?php do_action('oa_social_login'); ?>
									</div>
								</form>
							</div>
					</div>
				</li>
				<?php //else: ?>
				<li>
					<a id="logout-btn" href="#">logout</a>
					<?php //wp_loginout($_SERVER['REQUEST_URI']); ?>
				</li>
				<?php //endif; ?>
				<li><a href='/login/' class=" active <?php 			if( is_user_logged_in() ) echo " hidden"; else echo " visible-xs"; ?>">Sign in</a></li>
				<li><a href="/about/" class="<?php 			if( is_page( 'about') ) echo " active"; ?>"      >About</a></li>
				<li><a href="http://blog.jomi.com" class=""                                                  >Blog</a></li>
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
		$('#loginform').on('submit', function(e) {
			e.preventDefault();

			//$('#greyout,#signal').show();

			var login = $('#login-form input[name="log"]').val();
			if(login === '') {
				//console.log('no username specified');
				$('#error').text("ERROR: No username entered");
				$('#error').show();
				return;
			}
			var pass = $('#login-form input[name="pwd"]').val();
			if(pass === '') {
				//console.log('no password specified');
				$('#error').text("ERROR: No password entered");
				$('#error').show();
				return;
			}
			//console.log('user: ' + login);
			//console.log('pass: ' + pass);

			var dataString = 'log='+ login + '&pwd=' + pass;
			//alert (dataString);return false;
			/*$.ajax({
			  type: "POST",
			  url: "/wp-login.php",
			  data: dataString,
			  success: function(data) {
			  	console.log(String(data));
			    if(String(data).indexOf("login_error") > 0) {
			    	$('#greyout,#signal').hide();
			    	// login error occured
			    	//console.log('whoops');
			    	$('#error').text("ERROR: Username and password do not match.\nPlease try again.")
			    } else {
			    	
			    	console.log('success');
			    	window.location.reload();
			    	$('#greyout,#signal').hide();
			    }
			  }
			});*/

			$.post(MyAjax.ajaxurl, {
				action: 'ajax-login',
				username: login,
				password: pass,
				remember: true
			}, function(response) {
				console.log(response);

				$('#login-btn').hide();
				$('#logout-btn').show();
			});
		});
		$('#logout-btn').on('click', function() {

			$.post(MyAjax.ajaxurl, {
				action: 'ajax-logout'
			}, function(response) {
				$('#login-btn').show();
				$('#logout-btn').hide();
			})

		});

		$('#login-btn').show();
		$('#logout-btn').show();
		<?php if(is_user_logged_in()) { ?>
			$('#login-btn').hide();
		<?php } else { ?>
			$('#logout-btn').hide();
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

		//$('.jsgate').hide();

		//if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {

		//}
		//else{
			//$('.iegate').hide();
   		//}

	});

</script>