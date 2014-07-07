<?php if(is_front_page()) : ?>
	<video autoplay="" loop="" class='background hidden-xs' poster="https://d2ysendh9zluod.cloudfront.net/images/web_still_blur.jpg">
		<source src="/wp-content/themes/jomi/assets/video/background.mp4" type="video/mp4">
	</video>
<?php endif; ?>
<div class="container" role="document">
  <div class="content row">
    <main class="col-sm-12" role="main">
      <header class='site-header'>
      	<div class='row'>
	      	<div class='col-xs-4'>
	        	<div class="logo"><a href="/"><img src="/wp-content/themes/jomi/assets/img/logo.png" alt="Journal of Medical Insight"></a></div>
	        </div>
	        <!-- Navbar buttons for desktop -->
	        <div class='col-xs-8 hidden-xs'>
		        <nav class="nav-top">
		          <ul>
		            <li class="dropdown">
						<a class="dropdown-toggle border" href="#" data-toggle="dropdown" id="login-btn">Login</a>
						<div class="dropdown-menu" style="padding: 15px;">
							<div id="login-form">
							  <input placeholder="Username" id="user_username" style="margin-bottom: 15px;" type="text" name="login" size="30" />
							  <input placeholder="Password" id="user_password" style="margin-bottom: 15px;" type="password" name="password" size="30" />
							  <input class="btn" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="submit" name="commit" value="Sign In" />
							</div>
						</div>
					</li>
		            <li><a href="#">Pricing</a></li>
		            <li><a href="#">Contact</a></li>
		          </ul>
		        </nav>
		    </div>
	    </div>
      </header>
    </main>
  </div>
</div>

<script>
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

		function onLoginSuccessful(token){
			Cookies.set('ua_session_token', token);
			window.location.href = "/";		
		}

		function emailLogin(){
			UserApp.User.login({ "login": $('#login-form input[name="login"]').val(), "password": $('#login-form input[name="password"]').val() }, function(error, result) {		
			    if (error) {
			        // Something went wrong...
			        // Check error.name. Might just be a wrong password?
			        console.log(error);
			    } else if (result.locks && result.locks.length > 0) {
			        // This user is locked
			    } else {
			        // User is logged in, save result.token in a cookie called 'ua_session_token'	        
			        onLoginSuccessful(result.token);
			        console.log(result);
			        location.reload();
			    }
			});
		}

		var matches = window.location.href.match(/ua_token=([a-z0-9_-]+)/i);
		if (matches && matches.length == 2) {
			var token = matches[1];
			UserApp.setToken(token);
			onLoginSuccessful(token);
		}
		$('#login-form input[type=submit]').click(function(){
			emailLogin();
		});
	});
</script>