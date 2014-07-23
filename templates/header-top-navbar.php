<?php
global $user;
?>
<?php if(is_front_page()) : ?>
	<video id="video" autoplay="" loop="" class='background hidden-xs' poster="/wp-content/themes/jomi/assets/img/vid_poster.png">
		<source id="video-source" src="/wp-content/themes/jomi/assets/video/background.mp4" type="video/mp4">
	</video>
	<img src='/wp-content/themes/jomi/assets/img/background_mobile.png' class='background visible-xs'/>
	<div class='blackbox background'></div>

<?php endif; ?>

<nav class="navbar container" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Brand</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link</a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<div class="container" role="document">
  <div class="content row">
    <main class="col-sm-12" role="main">
      <header class='site-header'>

      	<div class='row'>




	      	<!--div class='col-xs-6'>
	        	<div class="logo"><a href="/"><img src="/wp-content/themes/jomi/assets/img/logo.png" alt="Journal of Medical Insight"></a></div>
	       	 	<input placeholder="Search articles" style="margin-top: -10px;" type="text" name="login" size="30" class="border hidden-xs" id="search-field"/>
	       	 	<span class="glyphicon glyphicon-search search-icon hidden-xs"></span>
	       	</div-->
	        <!-- Navbar buttons for desktop -->
        	<div class='col-xs-12'>

        		<div class="logo"><a href="/"><img src="/wp-content/themes/jomi/assets/img/logo.png" alt="Journal of Medical Insight"></a></div>
	       	 	<input placeholder="Search articles" type="text" name="login" size="30" class="border search hidden-xs" id="search-field"/>
	       	 	<span class="glyphicon glyphicon-search search-icon hidden-xs"></span>

		        <nav class='nav-top hidden-xs'>
		          <ul>
		          	<?php if(!$user): ?>
		            <li class="dropdown">
						<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Sign&nbsp;in</a>
						<div class="dropdown-menu pull-right" style="padding: 15px;">
							<div id="login-form">
								<span class="label label-danger" id="error-login" style="display:none;">User does not exist</span>
								<input placeholder="Username" id="user_username" style="margin-bottom: 15px;" type="text" name="login" size="30" />
								<span class="label label-danger" id="error-password" style="display:none;">Invalid password</span>
								<input placeholder="Password" id="user_password" style="margin-bottom: 15px;" type="password" name="password" size="30" />
								<input class="btn fat" style="clear: left; width: 100%;" type="submit" name="commit" value="Sign In" />
								<br><br>
									<button style="width:100%;" class="btn fat white social-login" data-provider="facebook"><i class="fa fa-facebook"></i>&nbsp;&nbsp;Log in with Facebook</button>
									<br><br>
									<button style="width:100%;" class="btn fat white social-login" data-provider="google"><i class="fa fa-google"></i>&nbsp;&nbsp;Log in with Google</button>
							</div>
						</div>
					</li>
					<?php else: ?>
					<li><a href="/?logout" id="logout-btn">Sign&nbsp;out</a></li>
					<?php endif; ?>

		            <li><a href='/subscribers/' class="hidden-xs<?php 	if( is_page( 'subscribers') ) echo " active"; ?>">Subscribers</a></li>
		            <li><a href="/pricing" class="hidden-xs<?php 		if( is_page( 'pricing') ) echo " active"; ?>">Pricing</a></li>
		            <li><a href="/contact" class="hidden-xs<?php 		if( is_page( 'contact') ) echo " active"; ?>">Contact</a></li>
		            <li><a href="/about" class="hidden-xs<?php 			if( is_page( 'about') ) echo " active"; ?>">About</a></li>
		            
		          </ul>
		        </nav>
		    <!--/div-->
		    <!-- Collapsable navbar for mobile -->
		    <!--div class='col-xs-6 visible-xs mobile-menu panel-group' id='accordion'-->



		    	<div class='panel-group mobile-menu visible-xs'>
			    	<div class='panel'>
					  <div class='panel-heading'><div class='panel-title'><a data-toggle='collapse' data-parent='#accordion' href='#menu'> 
					  		<!--
					  		<span class='glyphicon glyphicon-th-list'>Toggle Navigation</span>
					  		-->
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>

					  		</a></div></div>
					  <div class='panel-collapse collapse row' id='menu'>
						  <ul class='panel-body'>
						  	<?php if(!$user): ?>
						    <a href="#" data-toggle='collapse' data-target='#login'><li class='top'>Login</li></a>
						    <div id='login' class='panel-collapse collapse'>
						    	<div class='panel-body' id="login-form-2">
									<span class="label label-danger" id="error-login-2" style="display:none;">User does not exist</span>
									<input placeholder="Username" id="user_username_2" style="margin-bottom: 15px;" type="text" name="login" size="30" />
									<span class="label label-danger" id="error-password-2" style="display:none;">Invalid password</span>
									<input placeholder="Password" id="user_password_2" style="margin-bottom: 15px;" type="password" name="password" size="30" />
									<input id='submit-2' class="btn fat" style="clear: left; width: 100%;" type="submit" name="commit" value="Sign In" />
									<br><br>
									<button style="width:100%;" class="btn fat white social-login" data-provider="facebook"><i class="fa fa-facebook"></i>&nbsp;&nbsp;Log in with Facebook</button>
									<br><br>
									<button style="width:100%;" class="btn fat white social-login" data-provider="google"><i class="fa fa-google"></i>&nbsp;&nbsp;Log in with Google</button>						
								</div>
						    </div>
							<?php else: ?>
							<a href="/?logout" id="logout-btn"><li class='top'>Sign&nbsp;out</li></a>
							<?php endif; ?>
							<li>
								<input placeholder="Search articles" type="text" name="login" size="30" class="search-mobile" id="search-field-m"/>
							</li>
						    <a href='/about'><li>About</li></a>
						    <a href='/contact'><li>Contact</li></a>
						    <a href='/pricing'><li>Pricing</li></a>
						    <a href='/subscribers'><li class='bottom'>Subscribers</li></a>
						  </ul>
					  </div>
					</div>
				</div>
		    </div>
	    </div>
      </header>
    </main>
  </div>
</div>

<script>
	if ($(window).width() < 768) {
		$('#video-source').attr("src", "");
	}
	/* VIDEO LOAD CONDITIONALS */
	$(window).resize(function() {
		if ($(window).width() < 768 && $('#video-source').attr("src") != '') {
			$('#video-source').attr('src', '');
			$('#video').load();
		} else if ($(window).width() >= 768 && $('#video-source').attr("src") == ''){
			$('#video-source').attr('src', '/wp-content/themes/jomi/assets/video/background.mp4');
			$('#video').load();
		}
	});


	/* SIGNUP & LOGIN */
	$(function() {
		// Setup drop down menu
		$('.dropdown-toggle').dropdown();

		// Fix input element click problem
		$('.dropdown input, .dropdown label').click(function(e) {
			e.stopPropagation();
		});
	});
	$(document).ready(function(){
		UserApp.initialize({ appId: "53b5e44372154" });

		$('.social-login').click(function(){
			socialLogin($(this).data('provider'));
		});

		function onLoginSuccessful(token){
			Cookies.set('ua_session_token', token);
			window.location.href = "/articles";
		}

		function emailLogin(user, pass){
			UserApp.User.login({ "login": user, "password": pass}, function(error, result) {
			    if (error) {
			        // Something went wrong...
			        // Check error.name. Might just be a wrong password?
			        $('#login-form .label').hide();
			        $('#login-form input').removeClass('error');
			        if($('#login-form input[name="password"]').val() == '')
			        {
			        	$('#login-form input[name="password"]').addClass('error');
			        	$('#error-password').show();
			    	}
			        else if(error.name == 'INVALID_ARGUMENT_LOGIN'){
			        	$('#login-form input[name="login"]').addClass('error');
			        	$('#error-login').show();
			        }
			        else if(error.name == 'INVALID_ARGUMENT_PASSWORD' || error.name == 'INVALID_ARGUMENT_LOGIN'){
			        	$('#login-form input[name="password"]').addClass('error');
			        	$('#error-password').show();
			        }
			    } else if (result.locks && result.locks.length > 0) {
			        // This user is locked
			    } else {
			        // User is logged in, save result.token in a cookie called 'ua_session_token'	        
			        onLoginSuccessful(result.token);
			    }
			});
		}

		function socialLogin(providerId) {
			var redirectUrl = window.location.protocol+'//'+window.location.host+window.location.pathname;
			UserApp.OAuth.getAuthorizationUrl({ provider_id: providerId, redirect_uri: redirectUrl },
				function(error, result) {
					if (!error) {
						window.location.href = result.authorization_url;
					}
				}
			);
		}

		var matches = window.location.href.match(/ua_token=([a-z0-9_-]+)/i);
		if (matches && matches.length == 2) {
			var token = matches[1];
			UserApp.setToken(token);
			onLoginSuccessful(token);
		}
		$('#login-form input[type=submit]').click(function(){
			emailLogin($('#login-form input[name="login"]').val(), $('#login-form input[name="password"]').val());
		});
		$('#submit-2').click(function(){
			emailLogin($('#login-form-2 input[name="login"]').val(), $('#login-form-2 input[name="password"]').val());
		});
		$('#login-form input[name="password"]').keypress(function (event) {
			if ( event.which == 13 || event.which == 10) {
		      emailLogin($('#login-form input[name="login"]').val(), $('#login-form input[name="password"]').val());
		    }
		});
		$('#search-field').keydown(function(event){
			if(event.which == 13)
			{
				event.preventDefault();
				window.location.href = "/?s="+$(this).val();
			}
		});
		$('#search-field-m').keydown(function(event){
			if(event.which == 13)
			{
				event.preventDefault();
				window.location.href = "/?s="+$(this).val();
			}
		});
	});
</script>