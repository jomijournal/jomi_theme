<!--div class="jsgate" style="background-color: #111; color: #fff; position:fixed; width:100%; height:100%; top:0px; left:0px; z-index:1000; text-align: center; padding-top: 50px; font-size: 24px;">
Your browser is too old to run javascript. If you continue, you may experience trouble.<br><br><br><br><a href="#hidejsgate">continue</a></div-->

<?php
global $user;
?>
<?php if(is_front_page()) : ?>
	<video id="video" autoplay="" loop="" class='background hidden-xs' poster="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/vid_poster.jpg">
		<source id="video-source" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background_vid.mp4" type="video/mp4">
		<source id="video-source-webm" src="https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/video/background.webm" type="video/webm">
	</video>
	<img src='https://jomicom.a.cdnify.io/wp-content/themes/jomi/assets/img/background_mobile.png' class='background visible-xs'/>
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
	 <form class="navbar-form navbar-left" role="search">
        <div class="form-group has-feedback has-feedback-left">
        	<!--form class="navbar-form navbar-left" role="search"-->
	    	<input placeholder="Search Articles" type="text" name="login" size="30" class="border search from-control" id="search-field"></input>
	    	<i class="form-control-feedback glyphicon glyphicon-search"></i>
	    	<!--/form-->
	    </div>
      </form>

      <ul class="nav navbar-nav navbar-right">
 
	      <?php if(!is_user_logged_in()): ?>
		        <li class="dropdown hidden-xs">
					<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Sign&nbsp;in</a>
						<div class="dropdown-menu pull-right" style="padding: 15px; z-index: 5;">
							<div id="login-form">
								<form name="loginform" id="loginform" action="">
									<p class="error" id="error"></p>
									<p class="login-username">
										<label for="user_login">Username/Email</label>
										<input type="text" name="log" id="user_login" class="input" value="" size="20">
									</p>
									<p class="login-password">
										<label for="user_pass">Password</label>
										<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
									</p>
									<p class="login-remember"><label class="active"><input name="rememberme" type="checkbox" id="rememberme" value="forever" checked="checked"> Remember Me</label></p>

									<p class="login-submit">
										<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In">
										<input type="hidden" name="redirect_to" value="/">
									</p>
									<p>
									<a href="/register" class="register">Register</a>
									</p>
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
				<?php else: ?>
				<li><?php wp_loginout($_SERVER['REQUEST_URI']); ?></li>
			<?php endif; ?>
			<li><a href='/login/' class=" active <?php 			if( is_user_logged_in() ) echo " hidden"; else echo " visible-xs"; ?>">Sign in</a></li>
	        <li><a href="/about/" class="<?php 			if( is_page( 'about') ) echo " active"; ?>"      >About</a></li>
	        <li><a href="http://blog.jomi.com" class=""                                                  >Blog</a></li>
	        <li><a href='/subscribers/' class="<?php 	if( is_page( 'subscribers') ) echo " active"; ?>">Subscribe</a></li>
	        <li><a href="/articles/" class="<?php 			if( is_page( 'articles') ) echo " active"; ?>">Articles</a></li>

     </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
</div>





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
			$.ajax({
			  type: "POST",
			  url: "/wp-login.php",
			  data: dataString,
			  success: function(data) {
			  	console.log(String(data));
			    if(String(data).indexOf("login_error") > 0) {
			    	// login error occured
			    	//console.log('whoops');
			    	$('#error').text("ERROR: Username and password do not match.\nPlease try again.")
			    } else {
			    	console.log('success');
			    	window.location.reload();
			    }
			  }
			});
		});

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

		$('.jsgate').hide();}
	});

</script>