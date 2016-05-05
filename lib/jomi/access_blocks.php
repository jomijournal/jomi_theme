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
	<?php # @COPY_SIGNIN ?>
	<div class="row" style="margin-bottom: 10px; margin-top: 10px;">
		<strong><h1 style="text-align:center;"><?php echo $msg; ?></h1></strong>
		<p style="text-align:center;">
			<strong>JoMI is not a free resource.</strong>
			&nbsp;Please&nbsp;
			<a title='Sign In' href='<?php echo wp_login_url($redirect_to) ?>'>sign in</a>
			&nbsp;or&nbsp;
			<a title='Register' href='<?php echo wp_registration_url($redirect_to) ?>'>register</a>
			&nbsp;to continue viewing this article.
		</p>
		<p style="text-align:center;">
			Please make a request to your librarian or&nbsp;
			<a href="mailto:lib@jomi.com?Subject=JoMI Subscription Request" target="_blank">
				<strong>send us an email</strong>
			</a>
			&nbsp;to maintain access.
		</p>
	</div>
</div>
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
	$redirect_to = $_POST['redirectto'];
?>
<div class="container free-trial">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<?php
		# @COPY_FREETRIAL
		# this was used for flysheet and isnt/shouldnt be used anymore
	?>
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
	$redirect_to = $_POST['redirectto'];

	$order = $_SESSION['order'];
	$inst = $_SESSION['inst'];

	$date_end = $order->date_end;

	$year = substr($date_end, 0, 4);

	$month = substr($date_end, 5, 2);
	$month = date('F', mktime(0, 0, 0, $month, 10));

	$day = substr($date_end, 8, 2);
	$day = date('jS', mktime(0, 0, 0, 0, $day));

	$logged_in = is_user_logged_in();

	$require_login = $order->require_login;
	//$require_login = (empty($_GET['requirelogin'])) ? $require_login : $_GET['requirelogin'];
?>
<div class="container free-trial free-trial-thanks">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<div class="row">
		<?php
			# @COPY_FREETRIAL
			# for letting users know that they're on a free trial
		?>
		<h1>Thank you for using JoMI</h1>
		<h4>Your institution, <span style="text-decoration: underline;"><?php echo $inst->name ?></span> is currently using trial access.</h4>
		<h4>Your trial expires on <?php echo $month . ' ' . $day . ', ' . $year; ?></h4>
		<?php if(($require_login == "T") && !$logged_in) { ?>
			<strong>Your institution requires you to <a title='Sign In' href='<?php echo wp_login_url($redirect_to)?>'>sign in</a> before viewing this content.</strong>
			<p>Or, if you do not have an account yet, <a title='Register' href='<?php echo wp_registration_url($redirect_to)?>'>register here</a>
		<?php } else { ?>
			<strong><u>Your Opinion Matters!</u></strong>
			<p>Please let your librarian know if you found our content valuable.</p>
		<?php } ?>
		<br>
	</div>
	<div class="row link-close">
		<?php if(($require_login == "T") && !$logged_in) { ?>
		<?php } else { ?>
			<a class="btn border" href="#" id="close-free-trial">Continue Watching</a>
		<?php } ?>
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

function block_subscribed_sign_in() {
	$id = $_POST['id'];
	$redirect_to = $_POST['redirectto'];

	$order = $_SESSION['order'];
	$inst = $_SESSION['inst'];

	$date_end = $order->date_end;

	$year = substr($date_end, 0, 4);

	$month = substr($date_end, 5, 2);
	$month = date('F', mktime(0, 0, 0, $month, 10));

	$day = substr($date_end, 8, 2);
	$day = date('jS', mktime(0, 0, 0, 0, $day));

	$logged_in = is_user_logged_in();

?>
<div class="container subscribe-sign-in">
	<div id="greyout" class="greyout">
		<div id="signal" class="signal"></div>
	</div>
	<?php
		# @COPY_REQUIRE_SIGN_IN
		# to let users know that their institution requires them to sign in/register first
	?>
	<div class="row">
		<h1>Thank you for using JoMI</h1>
		<h4>Your institution, <span style="text-decoration: underline;"><?php echo $inst->name ?></span> is currently subscribed</h4>
		<h4>Your subscription expires on <?php echo $month . ' ' . $day . ', ' . $year; ?></h4>
		<h4>Your institution requires you to sign in to access our content.</h4>
		<h4><a title='Sign In' href='<?php echo wp_login_url($redirect_to)?>'>Sign in</a> or <a title='Register' href='<?php echo wp_registration_url($redirect_to)?>'>create an account</a></h4>
		<br>
	</div>
</div>
<?php
}
add_action('wp_ajax_block-subscribed-sign-in', 'block_subscribed_sign_in');
add_action('wp_ajax_nopriv_block-subscribed-sign-in', 'block_subscribed_sign_in');
?>
