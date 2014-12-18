<?php
/*
Template Name: Frontpage
*/
?>

<div class="taglines">
		<h1>Surgical Video Articles <br/> for Attendings, Residents, and Medical Students</h1>
		<h2>The Journal of Medical Insight seeks to improve outcomes through publication  
		of videos of cutting-edge and standard of care surgical procedures.
		<br/><a class="mini-link" href="<?php echo site_url('/about'); ?>">Learn more about us →</a></h2>
		<div class="partners">
			<h3>JoMI is used at prestigious medical schools and hospitals around the world:</h3>
			<div class="logos">
				<img src="<?php echo site_url('/wp-content/themes/jomi/assets/img/clients/logo-ucsf.png'); ?>">
				<img src="<?php echo site_url('/wp-content/themes/jomi/assets/img/clients/logo-mskcc.png'); ?>">
			</div>
			<a class="mini-link" href="<?php echo site_url('/subscribers/'); ?>">See all subscribers →</a>
		</div>
</div>

<?php require_once(ABSPATH . '/wp-content/themes/jomi/templates/front-footer.php'); ?>