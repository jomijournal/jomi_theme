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
		Please recommend JoMI to your institution or librarian.
		</p>
		<p>
		Free trials are currently available to institutions in your area.
		</p>
		<br>
		<br>
		<p>
		If you are not associated with an institution, contact us:
		</p>
	</div>
	<div class="row">
		<div class="wpcf7" id="wpcf7-f1099-p226-o4" lang="en-US" dir="ltr">
			<div class="screen-reader-response"></div>
			<form name="" action="<?php echo get_permalink( $id ) . '#wpcf7-f1099-p226-o4'; ?>" method="post" class="wpcf7-form" novalidate="novalidate">
				<div style="display: none;">
					<input type="hidden" name="_wpcf7" value="1099">
					<input type="hidden" name="_wpcf7_version" value="3.9.1">
					<input type="hidden" name="_wpcf7_locale" value="en_US">
					<input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f1099-p226-o4">
					<input type="hidden" name="_wpnonce" value="2bf1f332b1">
				</div>
				<p>
    				<span class="wpcf7-form-control-wrap your-email">
    					<input id="request-access-email" type="email" name="your-email" value="" placeholder="Email" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false">
    				</span>
					<input id="request-trial-submit" type="submit" value="Request Access" class="wpcf7-form-control wpcf7-submit btn border">
				</p>
				<div class="wpcf7-response-output wpcf7-display-none"></div>
			</form>
		</div>
	</div>
	<div class="row">
		<p>
		And we will work with you personally to help you access our content.
		</p>
	</div>
</div>
<script>
$(function() {
	$('#request-trial-submit').on('click', function(e) {
		var email = $('#request-access-email').val();
		if(isEmail(email)) {

		} else {
			e.preventDefault();
			console.log('submit');
		}
	})
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
?>