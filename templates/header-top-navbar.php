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
		            <li><a href="#" class="border" id="login-btn">Login</a></li>
		            <li><a href="#">Pricing</a></li>
		            <li><a href="#">Contact</a></li>
		          </ul>
		        </nav>
		    </div>
		    <!-- Collapsable navbar for mobile -->
		    <div class='col-xs-8 visible-xs'>
		    	<div class='dropdown alignright mobile-menu'>
				  <a class='dropdown-toggle' data-toggle='dropdown' href='#'> <span class='glyphicon glyphicon-th-list'></span> </a>

				  <ul class='dropdown-menu dropdown-menu-right' role='menu'>
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" id="login-btn">Login</a></li>
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Pricing</a></li>
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Contact</a></li>
				  </ul>
				</div>
		    </div>
	    </div>
      </header>
    </main>
  </div>
</div>

<script>
	/* SIGNUP & LOGIN */
	$(document).ready(function(){
		UserApp.initialize({ appId: "53b5e44372154" });

		function onLoginSuccessful(token){
			Cookies.set('ua_session_token', token);
			window.location.href = "/";		
		}

		function emailLogin(){
			UserApp.User.login({ "login": 'seifip@gmail.com', "password": 'jomicat' }, function(error, result) {		
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
			    }
			});
		}

		var matches = window.location.href.match(/ua_token=([a-z0-9_-]+)/i);
		if (matches && matches.length == 2) {
			var token = matches[1];
			UserApp.setToken(token);
			onLoginSuccessful(token);
		}

		$('#login-btn').click(function(){
			emailLogin();
		});
	});
</script>