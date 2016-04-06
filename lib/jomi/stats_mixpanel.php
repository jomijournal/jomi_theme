<?php 
// Set up mixpanel!
if(WP_ENV == 'PROD') {
	define( 'MIXPANEL_KEY', 'c75c83d6b279b9f623cfa461d7b9a8bc' );
} else {
	define( 'MIXPANEL_KEY', '9f28013773e9c4bbed6df6d2f3013483' );
}

function on_wp_register_user( $user_id )
{
        $mp = Mixpanel::getInstance( MIXPANEL_KEY );

	$d_id = $_COOKIE['mixpanel_distinct_id'];
	$mp->createAlias( $d_id, $user_id );
	$mp->identify( $user_id );
	$mp->track( "Created an account", array( "source_ip" => $_SERVER[ 'REMOTE_ADDR' ] ) );

/*
	// We could do this here, but are instead doing it when the user is logging in
	// this way we catch any changes and catch users who may have been missed in the past.
        $mp->people->set( $user_id, array(
                                        '$email' 	=> $_POST['user_email'],
                                        '$first_name' 	=> $_POST['first_name'],
                                        '$last_name' 	=> $_POST['last_name'] ));
*/
}
add_action( 'user_register', 'on_wp_register_user', 10, 1 );

// Keeping track of activity using mixpanel
function on_wp_login( $user_login, $user ){

	$mp = Mixpanel::getInstance( MIXPANEL_KEY );
	
	$mp->identify( $user->ID );
	$mp->track( "Logged in", array( "source_ip" => $_SERVER[ 'REMOTE_ADDR' ] ) );

   	$mp->people->set( $user->ID, array(
                                      '$email'        => $user->user_email
                                    , '$first_name'   => $user->first_name
                                    , '$last_name'    => $user->last_name
									, 'ip'			=> $_SERVER[ 'REMOTE_ADDR' ]
									, '$institution_stated'	=> $user->rpr_institutional_association
									) 
   	);
}
add_action( 'wp_login', 'on_wp_login', 10, 2 );

// Logout is handled in tempaltes/header.php - client-side

function on_wp_footer()
{
	if( is_single() ){
		// mixpanel registration for the article is handled in templates/article.php 
	} else {
		echo '<script>';
	        $user = wp_get_current_user();
        	if( $user->ID <> 0 ){
                	echo 'mixpanel.identify('.$user->ID.');';
        	}	
		echo 'mixpanel.track( "Open " + location.pathname.substring(1), {"source_ip": "'. $_SERVER[ 'REMOTE_ADDR' ] . '"} );';
        
	        echo '</script>';


	}

}
add_action( 'wp_footer', 'on_wp_footer' );

?>