<?php 
/**
 * Save post metadata when a post is saved.
 *
 * @param int $post_id The ID of the post.
 */
function save_vid_length_meta( $post_id ) {

    $type = 'article';

    // If this isn't a 'article' post, don't update it.
    if ( $type != $_POST['post_type'] ) {
        return;
    }
    if (empty(get_field('wistia_id', $post_id))) {
      return;
    }
    $wistia_id = get_field('wistia_id', $post_id);
    // wistia API call
    $api_keys = file_get_contents(ABSPATH . 'wp-content/themes/jomi/api_keys.json');
    $api_keys = json_decode($api_keys);

    $video_meta = file_get_contents("https://api.wistia.com/v1/medias/" . $wistia_id . ".json?api_password=" . $api_keys->wistia);
    $video_meta = json_decode($video_meta);

    //print_r($video_meta);
    // duration in seconds
    $duration = $video_meta->duration;
    $duration = floor($duration);
    $hours = floor($duration / 3600);

    if($hours > 0) {
      $minutes = floor(($duration - ($hours * 3600)) / 60);
      $seconds = $duration % 60;
      if($minutes < 10) {
        $minutes = '0' . $minutes;
      }
      if($seconds < 10) {
        $seconds = '0' . $seconds;
      }

      $vid_length = $hours . ':' . $minutes . ':' . $seconds;
    } else {
      $minutes = floor($duration / 60);
      $seconds = $duration % 60;
      if($seconds < 10) {
        $seconds = '0' . $seconds;
      }

      $vid_length = $minutes . ":" . $seconds;
    }
    
    // - Update the post's metadata.
    update_post_meta( $post_id, 'vid_length', $vid_length, '0:00');

}
add_action( 'save_post', 'save_vid_length_meta', 9999, 1 );

?>