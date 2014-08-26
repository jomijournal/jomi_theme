<?php

/*
===============================
wp-login page style, redirects + hiding
===============================
 */
function jomi_login_head() {
  get_template_part('templates/head');
  do_action('get_header');
}
add_action('login_head', 'jomi_login_head');
function jomi_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/assets/css/main.min.css' );
    wp_enqueue_script( 'custom-login', get_template_directory_uri() . '/assets/js/scripts.min.js' );
}
add_action( 'login_enqueue_scripts', 'jomi_login_stylesheet' );
function jomi_login_header_url($url) {
  return site_url();
}
add_filter('login_headerurl', 'jomi_login_header_url');
function jomi_login_footer(){
  echo site_url('','relative');
  echo '
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
  $(function(){
    $("#loginform").attr("action", "' . site_url() . '/login/");
    $("#registerform").attr("action", "' . site_url() . '/register/");
    $("#login a").first().attr("title","Journal of Medical Insight");
    //$("input[name=' . "'redirect_to'" . ']").attr("value","'.site_url().'");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=register'" . ']").attr("href", "'.site_url().'/register");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=lostpassword'" . ']").attr("href", "'.site_url().'/forgot");
    $("a[href=' . "'" . site_url() . "/wp-login.php'" . ']").attr("href", "'.site_url().'/login");

    $("#registerform input[name=' . "'redirect_to'" . ']").attr("value","'.site_url('/login?checkemail=registered').'")
  });
  </script>
  ';
}
add_action('login_footer', 'jomi_login_footer');
add_action('register_footer', 'jomi_login_footer');
add_action('lostpassword_footer', 'jomi_login_footer');

function login_rewrite($wp_rewrite) {
  //add_rewrite_rule('^login/','wp-login.php?action=login','top');
  //add_rewrite_rule('^register/','wp-login.php?action=register','top');
}
add_filter('init', 'login_rewrite');


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

// hides wp-login.php from the url bar
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
  add_action('init','possibly_redirect');
}

?>