<?php
/**
 * BLOCK TEMPLATES
 */

function block_deny() {
	//echo "hello";
	$id = $_POST['id'];
?>
<div class="container" style="position:relative; width: 94%; height: 100%; margin: auto; overflow: auto; background: rgba(0, 0, 0, 0.75);">
	<div class="row">
		<strong><h1 style="text-align:center;">ACCESS DENIED</h1></strong>
	</div>
	<div class="row">
		<div class="col-xs-6" style="border-right: 3px dashed #fff; padding: 0 30px;">
			<h3>Log In</h3>
			<div id="login-form" class="aligncenter" style="">
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
		<div class="col-xs-6">
			<h3>Register</h3>
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
});
</script>
<?php
}
add_action( 'wp_ajax_block-deny', 'block_deny' );
add_action( 'wp_ajax_nopriv_block-deny', 'block_deny' );
?>