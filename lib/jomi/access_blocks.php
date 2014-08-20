<?php
/**
 * BLOCK TEMPLATES
 */

function block_deny() {
	//echo "hello";
	$id = $_POST['id'];
?>

<strong>DENIED TO THE MAX</strong>
<a href="#">CHAMPION OF THE SUN</a>
<p><?php echo $id; ?></p>

<?php
}
add_action( 'wp_ajax_block-deny', 'block_deny' );
add_action( 'wp_ajax_nopriv_block-deny', 'block_deny' );
?>