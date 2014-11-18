<?php

/*
===============================
everything login related

wp-login page style, redirects + hiding
===============================
 */

/**
 * load head so we get access to all of our js and css
 * @return [type] [description]
 */
function jomi_login_head() {
  get_template_part('templates/head');
  do_action('get_header');
}
add_action('login_head', 'jomi_login_head');

/**
 * load stylesheets manually just in case
 * @return [type] [description]
 */
function jomi_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/assets/css/main.min.css' );
    //wp_enqueue_script( 'custom-login', get_template_directory_uri() . '/assets/js/scripts.min.js' );
}
//add_action( 'login_enqueue_scripts', 'jomi_login_stylesheet' );

/**
 * link to our website, not wordpress's
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function jomi_login_header_url($url) {
  return site_url();
}
add_filter('login_headerurl', 'jomi_login_header_url');

/**
 * jquery to change some login form stuff around
 * @return [type] [description]
 */
function jomi_login_footer(){
  echo '<div class="blackbg"></div>';
  
  echo site_url('','relative');
?>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
  $(function(){
    $("#loginform").attr("action", "<?php echo site_url('/login/'); ?>");
    $("#registerform").attr("action", "<?php echo site_url('/register/');?>");
    $("#login a").first().attr("title","Journal of Medical Insight");
    $("input[name=' . "'redirect_to'" . ']").attr("value","<?php echo $_POST['redirect_to']; ?>");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=register'" . ']").attr("href", "'.site_url().'/register");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=lostpassword'" . ']").attr("href", "'.site_url().'/forgot");
    $("a[href=' . "'" . site_url() . "/wp-login.php'" . ']").attr("href", "'.site_url().'/login");

    $("p#backtoblog a").attr("href", "' . $_POST['redirect_to'] . '");

    //console.log("redirect link: ' . $_POST['redirect_to'] . '");

    $("#registerform input[name=' . "'redirect_to'" . ']").attr("value","'.site_url('/login?checkemail=registered').'");
  });
  </script>
<?php
}
add_action('login_footer', 'jomi_login_footer');
add_action('register_footer', 'jomi_login_footer');
add_action('lostpassword_footer', 'jomi_login_footer');

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function my_login_redirect( $redirect_to, $request, $user ) {
  //is there a user to check?
  global $user;
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    //check for admins
    if ( in_array( 'administrator', $user->roles ) ) {
      // redirect them to the default place
      return site_url('/wp-admin');
    } else {
      return home_url();
    }
  } else {
    return $redirect_to;
  }
}

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

/**
 * hides login from url bar
 * phils code from lingualift
 * currently disabled due to buggy behavior
 */
if (!function_exists('possibly_redirect'))
{
  function possibly_redirect()
  {
    global $pagenow;
    if( 'wp-login.php' == $pagenow )
    {
      $action = $_GET["action"];
      if($action=="logout"){
         wp_logout();
         header("Location: /wp-login.php?loggedout=true");
      }
      # hide wp-login
      if($_SERVER['REQUEST_URI'] == "/wp-login.php?action=".$action)
      {
        //wp_redirect('/login?action='.$action);
      }
      if($_SERVER['REQUEST_URI'] == "/wp-login.php?checkemail=registered")
      {
        //wp_redirect('/login?checkemail=registered');
      }
      /*else
      {
        wp_redirect('/');       
      }*/
      //exit();
    }
  }
 // add_action('init','possibly_redirect');
}

/**
 * log in via ajax. no redirects required
 * @return [type] [description]
 */
function ajax_login() {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $remember = $_POST['remember'];

  $creds = array(
    'user_login' => $username,
    'user_password' => $password,
    'remember' => $remember
  );

  $login_result = wp_signon($creds, true);

  if (is_wp_error($login_result))
    //echo $login_result->get_error_message();
    echo "failure";
  else {
    echo $username;
  }
}
add_action( 'wp_ajax_ajax-login', 'ajax_login');
add_action( 'wp_ajax_nopriv_ajax-login', 'ajax_login');

function ajax_logout() {
  wp_logout();

  echo "success";
}
add_action('wp_ajax_ajax-logout', 'ajax_logout');
add_action('wp_ajax_nopriv_ajax-logout', 'ajax_logout');

?>