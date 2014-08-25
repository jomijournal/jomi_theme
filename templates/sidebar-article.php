<?php #dynamic_sidebar('sidebar-article'); ?>

<h3>Authors</h3>
<?php
$coauthors = get_coauthors();
foreach( $coauthors as $coauthor )
{
	?>
	<a class="author" href="/author/<?php echo $coauthor->user_nicename; ?>">
	  	<div class="avatar hidden-sm"><?php echo get_wp_user_avatar($coauthor->ID, 64); ?></div>
	  	<div class="bio">
	    	<h4><?php echo $coauthor->display_name; ?></h4>
	    	<h5><?php echo $coauthor->description; ?></h5>
	  	</div>
	  	<div style="clear:both;"></div>
	</a>
<?php
}
?>

<h3>Institution</h3>

<h5 style="text-align:center;"><?php the_field('hospital_name'); ?></h5>
<div id="view1">
<?php
$location = get_field('hospital_map');
if( ! empty($location) ):
?>
<div id="map" style="width: 100%; height: 200px;"></div>
<script src='https://maps.googleapis.com/maps/api/js?sensor=false' type='text/javascript'></script>

<script type="text/javascript">
  //<![CDATA[
	function load() {
	var lat = <?php echo $location['lat']; ?>;
	var lng = <?php echo $location['lng']; ?>;
// coordinates to latLng
	var latlng = new google.maps.LatLng(lat, lng);
// map Options
	var myOptions = {
	zoom: 14,
	center: latlng,
	mapTypeId: google.maps.MapTypeId.ROADMAP
   };
//draw a map
	var map = new google.maps.Map(document.getElementById("map"), myOptions);
	var marker = new google.maps.Marker({
	position: map.getCenter(),
	map: map
   });
}
// call the function
   load();
//]]>
</script>
<?php endif; ?> 

</div>

<h3>Information</h3>
<div class="pub-info">
  <strong>Publication Date: </strong><?php echo get_the_date(); ?>
</div>
<div class="pub-info">
  <strong>Article ID: </strong>
  <?php the_field('publication_id'); ?>
</div>

<h3>Share This Article</h3>

<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_sharing_toolbox"></div>