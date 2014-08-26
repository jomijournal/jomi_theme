<?php
/**
 * BLOCK TEMPLATES
 */

function block_deny() {
	//echo "hello";
	$id = $_POST['id'];
	$msg = $_POST['msg'];
?>
<div class="container">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<div class="row">
		<strong><h1 style="text-align:center;"><?php echo $msg; ?></h1></strong>
		<p style="text-align:center;">Please sign in or register to continue:</p>
	</div>
	<div class="row">
		<div class="col-xs-6" style="border-right: 3px dashed #fff; padding: 0 30px;">
			<h3>Log In</h3>
			<div id="login-form" class="aligncenter" style="">
				<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php'); ?>" method="post">
					<p class="error" id="error"></p>
					<div class="login-username">
						<label for="user_login">Username/Email<br>
						<input type="text" name="log" id="user_login" class="input" value="" size="20"></label>
					</div>
					<div class="login-password">
						<label for="user_pass">Password<br>
						<input type="password" name="pwd" id="user_pass" class="input" value="" size="20"></label>
					</div>
					<p class="login-remember"><label class="active"><input name="rememberme" type="checkbox" id="rememberme" value="forever" checked="checked"> Remember Me</label></p>

					<p class="login-submit">
						<input type="submit" name="submit" id="submit" class="btn btn-default" value="Log In">
						<input type="hidden" name="redirect_to" value="/">
					</p>
					<!--p class="login-register" style="width:45%">
						<a href="/register"><btn type="register" name="register" id="register" class="btn btn-default" value="Register"/></a>
					</p-->
					<div class="social-box">
						<?php do_action('oa_social_login'); ?>
					</div>
				</form>
			</div>
		</div>
		<div class="col-xs-6">
			<h3>Register</h3>
			<div id="login">
				<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post'); ?>" method="post">
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
					<p id="pass_strength_msg">Your password must be at least 6 characters long.</p>
					<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<p class="submit">
						<input type="submit" name="wp-submit" id="wp-submit" class="btn btn-default" value="Register">
					</p>
				</form>
			</div>
			<script type="text/javascript">
			try{document.getElementById('user_login').focus();}catch(e){}
			if(typeof wpOnload=='function')wpOnload();
			</script>
			<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery("#user_login").removeAttr("size");
						jQuery("#user_login").parent().attr("id", "user_login-label");
						jQuery("#user_login").parent().parent().attr("id", "user_login-p");
						jQuery("#user_email").removeAttr("size");
						jQuery("#user_email").parent().attr("id", "user_email-label");
						jQuery("#user_email").parent().parent().attr("id", "user_email-p");
					});
			</script>
        </div>
	</div>
</div>
<script>
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
		
		$('#greyout,#signal').show();

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
		    	$('#greyout,#signal').hide();
		    	window.location.reload();
		    }
		  }
		});
	});
});
</script>
<?php
}
add_action( 'wp_ajax_block-deny', 'block_deny' );
add_action( 'wp_ajax_nopriv_block-deny', 'block_deny' );

function block_free_trial() {
	$id = $_POST['id'];
?>
<div class="container free-trial">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<div class="row">
		<strong><h1>ACCESS RESTRICTED</h1></strong>
		<p>
		Please recommend JoMI to your institution or librarian. Free trials are currently available to participating institutions.
		</p>
	</div>
</div>
<?php
}
add_action( 'wp_ajax_block-free-trial', 'block_free_trial' );
add_action( 'wp_ajax_nopriv_block-free-trial', 'block_free_trial' );
?>