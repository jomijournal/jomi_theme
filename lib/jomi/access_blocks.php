<?php
/**
 * BLOCK TEMPLATES
 */

/**
 * block to show when access is denied
 * @return [type] [description]
 */
function block_deny() {
	$id = $_POST['id'];
	$msg = $_POST['msg'];
	$redirect_to = $_POST['redirectto'];
?>
<div class="container sign-up">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>

	<!-- header -->
	<div class="row">
		<strong><h1 style="text-align:center;"><?php echo $msg; ?></h1></strong>
		<p style="text-align:center;">Please sign in or register to continue viewing this article</p>
		<p style="text-align:center;">To subscribe (or have us speak to your librarian), please <a href="mailto:lib@jomi.com?Subject=JoMI Subscription Request"><strong>send us an email</strong></a>.</p>
	</div>

	<div class="row">
		<div class="col-xs-6" style="border-right: 3px dashed #fff;text-align:right;">
			<h3>Sign In</h3>
			<div id="login-form" class="aligncenter" style="">
				<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php'); ?>" method="post">
					<p class="error" id="block-error"></p>
					<div class="login-username">
						<label for="user_login">Username/Email<br>
						<input type="text" name="log" id="user_login" class="input" value="" size="15"></label>
					</div>
					<div class="login-password">
						<label for="user_pass">Password (<a href="/login/?action=lostpassword">Lost Password?</a>)<br>
						<input type="password" name="pwd" id="user_pass" class="input" value="" size="15"></label>
					</div>
					<p class="login-remember"><input name="rememberme" type="hidden" id="rememberme" value="forever" checked="checked"></p>

					<p>&nbsp;</p>
					<p class="login-submit">
						<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In">
						<input type="hidden" name="redirect_to" value="/">
					</p>
					<!--p class="login-register" style="width:45%">
						<a href="/register"><btn type="register" name="register" id="register" class="btn btn-default" value="Register"/></a>
					</p-->
					
				</form>
			</div>
		</div>
		<div class="col-xs-6">
			<h3>Register</h3>
			<div id="login">
				<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register'); ?>" method="post">
					<div id="user_login-p" style="display: none;">
						<label for="user_login" id="user_login-label">Username/Email<br>
						<input type="text" name="user_login" id="user_login" class="input" value=""></label>
					</div>
					<div id="user_email-p">
						<label for="user_email" id="user_email-label">Username/E-mail<br>
						<input type="text" name="user_email" id="user_email" class="input" value=""></label>
					</div>
					<p id="pass1-p">
						<label id="pass1-label" for="pass1">Password<br>
						<input type="password" autocomplete="off" name="pass1" id="pass1"></label>
					</p>
					<p id="pass1-p">
						<label id="pass1-label" for="pass1">Confirm Password<br>
						<input type="password" autocomplete="off" name="pass2" id="pass2"></label>
					</p>
					<p id="pass_strength_msg">Your password must be at least 6 characters long.</p>
					<input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">
					<p class="submit">
						<input type="submit" name="wp-submit" id="wp-submit" class="btn btn-default" value="Register">
					</p>
				</form>
			</div>
        </div>

	</div>
	<br>
	<div class="social-box">
		<?php do_action('oa_social_login'); ?>
	</div>
</div>
<script>
$(function() {
	$('#loginform').on('submit', function(e) {
		e.preventDefault();

		var login = $('#login-form input[name="log"]').val();
		if(login === '') {
			$('#block-error').text("ERROR: No username entered");
			$('#block-error').show();
			return;
		}
		var pass = $('#login-form input[name="pwd"]').val();
		if(pass === '') {
			$('#block-error').text("ERROR: No password entered");
			$('#block-error').show();
			return;
		}
		//var dataString = 'log='+ login + '&pwd=' + pass;
		
		$('#greyout,#signal').show();

		$.post(MyAjax.ajaxurl, {
			action: 'ajax-login',
			username: login,
			password: pass,
			remember: true
		}, function(response) {
			response = response.substr(0, response.length - 1);
			//console.log(response);
			$('#greyout,#signal').hide();

			if(response == "success") {
				$('.login-dropdown').hide();
				$('.login-dropdown').dropdown('toggle');
				$('.logout-dropdown').show();

				$('#access_block').hide();
			} else {
				$('#block-error').text("Incorrect username or password");
				$('#block-error').show();
			}
		});
	});
});
</script>
<?php
}
add_action( 'wp_ajax_block-deny', 'block_deny' );
add_action( 'wp_ajax_nopriv_block-deny', 'block_deny' );

/**
 * free trial block
 * @return [type] [description]
 */
function block_free_trial() {
	$id = $_POST['id'];
?>
<div class="container free-trial">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<strong><h1>Subscription Required</h1></strong>
			<br/>
			<p>
			Please enter your email below to request access:
			</p>
			<br/>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<form name="" action="" method="">
				<div class="error" id="email-error"></div>
				<p>
					<div class="row">
						<div class="col-xs-12 col-sm-7">
							<input id="request-access-email" type="email" name="your-email" value="" placeholder="Email" class="" aria-required="true" aria-invalid="false">
						</div>
						<div class="col-xs-12 col-sm-5">
							<input id="request-trial-submit" type="submit" value="Request Access" class="btn border">
						</div>
					</div>
				</p>
			</form>
			<br/>
			<p>
			We have institutional and individual subscriptions available. If you are unable to afford access, please send an email at <a href="mailto:lib@jomi.com?Subject=Need%20Based%request">lib@jomi.com</a>
			</p>
			<br/>
		</div>
	</div>
</div>
<script>
$(function() {

	$('#close-free-trial').on('click', function() {
		$('#access_block').hide();
	});

	$('#request-trial-submit').on('click', function(e) {
		var email = $('#request-access-email').val();
		if(isEmail(email)) {
			e.preventDefault();

			$('#greyout,#signal').show();

			$('#email-error').hide();
			$.post(MyAjax.ajaxurl, {
				action: 'send-free-trial',
				email: email
			}, function(response) {
				console.log(response);

				$('#greyout,#signal').hide();

				$('#email-error').show();
				$('#email-error').text('Request Sent!');
				$('#email-error').css("background", "rgb(71, 155, 71)");
				$("#email-error").css("-webkit-animation", "none");
				$("#email-error").css("animation", "none");
			});
		} else {
			$('#email-error').text('Invalid Email Address!');
			$("#email-error").css("-webkit-animation-play-state", "running");
			$("#email-error").css("animation-play-state", "running");
		     setTimeout(function() { 
		       $("#email-error").css("-webkit-animation-play-state", "paused");
		       $("#email-error").css("animation-play-state", "paused");
		     }, 280);
			$('#email-error').show();
			e.preventDefault();
		}
	});
})
//stolen from http://badsyntax.co/post/javascript-email-validation-rfc822
function isEmail(email){
    return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
}
</script>
<?php
}
add_action( 'wp_ajax_block-free-trial', 'block_free_trial' );
add_action( 'wp_ajax_nopriv_block-free-trial', 'block_free_trial' );

/**
 * uses wp mail functions to send a notification for a free trial
 * @return [type] [description]
 */
function send_free_trial(){
	$email = $_POST['email'];

	// let us know about it
	
	$dev_body = $email . ' would like to request a free trial' . '<br>';
	$dev_body .= 'http_referer:' . $_SERVER['HTTP_REFERER'];

	wp_mail('contact@jomi.com', 'Free Trial Request', $dev_body);

	// let them know about it
	wp_mail($email, 'Free Trial Request', 'Thanks for requesting a free trial! We will get back to you as soon as possible, and work with you personally so we can get you access to our video articles.');
}
add_action( 'wp_ajax_send-free-trial', 'send_free_trial' );
add_action( 'wp_ajax_nopriv_send-free-trial', 'send_free_trial' );

/**
 * "thank you" for institutions on a free trial
 * @return [type] [description]
 */
function block_free_trial_thanks() {
	$id = $_POST['id'];

	$order = $_SESSION['order'];

	$date_end = $order->date_end;

	$year = substr($date_end, 0, 4);

	$month = substr($date_end, 5, 2);
	$month = date('F', mktime(0, 0, 0, $month, 10));

	$day = substr($date_end, 8, 2);
	$day = date('jS', mktime(0, 0, 0, 0, $day));

?>
<div class="container free-trial free-trial-thanks">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<div class="row">
		<h1>Thank you for using JoMI</h1>
		<h4>Your trial expires on <?php echo $month . ' ' . $day . ', ' . $year; ?></h4>
		<strong><u>Your Opinion Matters!</u></strong>
		<p>Please let your librarian know if you found our content valuable.</p>
		<br>
	</div>
	<div class="row link-close">
		<a class="btn border" href="#" id="close-free-trial">Continue Watching</a>
	</div>
</div>
<script>
$(function() {
	$('#close-free-trial').on('click', function(e) {
		e.preventDefault();
		$('#access_block').hide();
		wistiaEmbed.play();
	});
});
</script>

<?php
}
add_action( 'wp_ajax_block-free-trial-thanks', 'block_free_trial_thanks' );
add_action( 'wp_ajax_nopriv_block-free-trial-thanks', 'block_free_trial_thanks' );
?>