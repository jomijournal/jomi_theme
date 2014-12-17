<?php while (have_posts()) : the_post(); ?>
	<?php
	/**
	 * ARTICLE ACCESS CHECK HERE
	 */
	

	if(!empty(get_field('password'))) {

		if($_POST['password'] != get_field('password')) {
	?>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
		<p>THIS ARTICLE IS PASSWORD PROTECTED</p>
		<input type="text" name="password">
		<input type="submit" value="unlock">
	</form>

	<?php
		exit();

		} else {
			//clear password
			$_POST['password'] = '';
		}
	}


	global $access_blocks;
	check_access();
	
	$id = get_the_ID();

	// get a custom stop time, if it exists
	// check wordpress meta first
	$custom_stop = get_post_meta($id, 'custom_stop', true);
	if(empty($custom_stop)) $custom_stop = get_field('custom_stop');

	// get custom time start from url
	$get_time_code = (empty($_GET['t'])) ? '' : $_GET['t'];

	$chapters = array();
	$chapter_count = 0;

	// temp fields
	$ch = [];
	$sch = [];

	//load acf chapter repeater field
	if(get_field('chapters')) {
		while(has_sub_field('chapters')) {
			$ch = [];
			$ch['title'] = get_sub_field('title');
			$ch['time'] = get_sub_field('time');
			$ch['title_clean'] = sanitize_title($ch['title']);

			// catch incomplete chapters
			if(empty($ch['title']) || $ch['time'] < 0) continue;

			if(get_sub_field('subchapters')) {
				$ch['subchapters'] = [];
				while(has_sub_field('subchapters')) {
					$sch = [];
					$sch['title'] = get_sub_field('title_sub');
					$sch['time'] = get_sub_field('time_sub');
					$sch['title_clean'] = sanitize_title($ch['title']);

					// catch incomplete subchapters
					if(empty($sch['title']) || empty($sch['time'])) continue;

					array_push($ch['subchapters'],$sch);
				}
			}

			// load into chapter array
			array_push($chapters, $ch);
		}
		$chapter_count = count($chapters);
	}

	//print_r_pre($chapters);

	?>

	<?php 
	if(get_post_status() == "preprint") { ?>
	<div class="container preprint-container">
		<div class="preprint"><strong>PREPRINT</strong></div>
	</div>
	<?php } 
	?>
	<div class="container video-container">
		<div class="video-area row">
			<div id="access_block" class="access-block">
				<div id="content" style="width: 100%; height: 100%;"></div>
			</div>

			<!-- CHAPTER GENERATION -->
			<?php if(!empty($chapters)) { ?>

			<div class="panel-group col-sm-3" role="tablist" aria-multiselectable="true" id="chapters">

				<?php foreach($chapters as $chapter) { ?>

				<div class="panel panel-chapter">

					<?php if(empty($chapter['subchapters'])) { ?>

					<div class="panel-heading" role="tab" id="<?php echo $chapter['title_clean'] . 'heading'; ?>">
						<h4 class="panel-title">

							<span class="vtime-item no-subchapters" href="#<?php echo trim($chapter['title_clean']); ?>" 
							onclick="wistiaEmbed.time(<?php echo $chapter['time']; ?>).play();" data-time="<?php echo $chapter['time']; ?>">
								<span class="vtime-title-text"><?php echo $chapter['title']; ?></span>
							</span>

						</h4>
					</div>

					<?php } else { ?>

					<div class="panel-heading" role="tab" id="<?php echo $chapter['title_clean'] . 'heading'; ?>">
						<h4 class="panel-title">
							<span class="vtime-item" data-time="<?php echo $chapter['time']; ?>">

								<span class="vtime-title-text" onclick="wistiaEmbed.time(<?php echo $chapter['time']; ?>).play();"
										href="#<?php echo trim($chapter['title_clean']); ?>"><?php echo $chapter['title']; ?></span>

								<span class="glyphicon glyphicon-chevron-right collapsed" href="#<?php echo trim($chapter['title_clean']); ?>" data-toggle="collapse" 
										data-parent="#chapters" aria-controls="<?php echo $chapter['title_clean']; ?>" aria-expanded="false"></span>

							</span>
						</h4>
					</div>
					<div id="<?php echo $chapter['title_clean']; ?>" class="panel-collapse collapse" role="tabpanel" 
					aria-labelledby="<?php echo $chapter['title_clean'] . 'heading'; ?>">
						<div class="panel-body">
							<?php foreach($chapter['subchapters'] as $subchapter) { ?>
							<span class="vtime-item" href="#" onclick="wistiaEmbed.time(<?php echo $subchapter['time']; ?>).play();"
							 data-time="<?php echo $subchapter['time']; ?>" data-parent-chapter="<?php echo $chapter['title_clean']; ?>">
								<?php echo $subchapter['title']; ?>
							</span>
							<?php } ?>
						</div>
					</div>

					<?php } ?>

				</div> <!-- /chapter -->
				<?php } ?>

			</div> <!-- /chapters -->
			<?php } ?> 
			<!--div class="hide-chapter">
				<a href="#" id="hide-chapter-btn">hide chapters</a>
			</div-->

			<div class="video-holder col-sm-9" id="video">
				<div id="wistia" class="wistia_embed">&nbsp;</div>
				<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js"></script>
			</div>
		</div>
	</div>

	<div class="col-sm-8 col-xs-12">
		<article <?php post_class(); ?>>
			<?php $wistia = get_field('wistia_id'); ?>

			<h1 class="entry-title"><?php the_title(); ?></h1>
			
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs" role="tablist" data-toggle="tabs">
					<li class="active"><a href="#main" data-toggle="tab">Main Text</a></li>
					<li><a href="#outline" data-toggle="tab">Procedure Outline</a></li>
				</ul>

				<!-- FOR REGULAR ARTICLES -->
				<div class="tab-content" id="content-article">

					<?php
					// fetch content custom fields
					$meta = get_field('meta');
					$abstract = get_field('abstract');
					$citations = get_field('citations');
					$outline = get_field('outline');
					?>

					<div class="tab-pane active" id="main">
						<?php echo $meta; ?>
						<!-- separating into a div for easier jquery grabbing-->
						<div id="the-content">
							<?php if(!empty($abstract)) { ?>
								<h4>Abstract</h4>
								<?php echo $abstract; ?>
							<?php } ?>
							<?php the_content(); ?>
						</div>
						<?php if(!empty($citations)) { ?>
							<h3>Citations</h3>
							<?php echo $citations; ?>
						<?php } ?>
					</div>

					<div class="tab-pane" id="outline">
						<div id="toc_container" class="toc_wrap_right toc_white no_bullets">
							<p class="toc_title">Table of Contents</p>
							<ul class="toc_list">
								<?php echo toc_get_index($outline);?>
							</ul>
						</div>
						<?php echo $outline; ?>
					</div>

				</div>

			</div>

			<!-- FOR FUNDAMENTALS -->
			<div class="entry-content" id="content-simple"></div>

			<?php comments_template(); ?>
		</article>
	</div>
	<div class="col-sm-4 col-xs-12">
		<?php require_once('sidebar-article.php'); ?>
	</div>

	<!-- replace state is an html5 feature. if ie8 tries to do this, it will stop the video from loading -->
	<!--[if gt IE 8]>
	<script>
		window.history.replaceState('', '', '/article/<?php echo get_field("publication_id"); ?>/<?php global $post; echo $post->post_name; ?>');
	</script>
	<![endif]-->

	<script>
		var blocked = false;

		var chapters = [];

		$(function(){

			// load chapters into object for looping
			$('.vtime-item').each(function(index) {
				chapters[index] = $(this);
			});

			// handle glyph switching with subchapters
			$(".vtime-item span.glyphicon").on('click', function() {

				// skip chapters without subchapters
				if($(this).attr('data-parent') != "#chapters") return;

				if($(this).hasClass('collapsed') && $(this).hasClass('glyphicon-chevron-right')) {
					$(this).removeClass('glyphicon-chevron-right');
					$(this).addClass('glyphicon-chevron-down');

					for(var chapter in chapters) {
						var glyph = chapter.find('span.glyphicon');
						if(glyph.hasClass('glyphicon-chevron-down')) {
							glyph.addClass('glyphicon-chevron-right');
							glyph.removeClass('glyphicon-chevron-down');
						}
					}

				} else if ($(this).hasClass('glyphicon-chevron-down') && !$(this).hasClass('collapsed')) {
					$(this).addClass('glyphicon-chevron-right');
					$(this).removeClass('glyphicon-chevron-down');
				}
			});

			//console.log(chapters);


			// load the wistia id (used for getting the video from wistia)
			$("#wistia").attr('id', 'wistia_<?php echo $wistia; ?>').show();
			wistiaEmbed = Wistia.embed("<?php echo $wistia; ?>", {
				// video foam = auto resizing (very good thing)
				videoFoam: true
			});

			$('ul.nav.nav-tabs li a').on('click', function(e) {
				history.pushState( null, null, $(this).attr('href') );
			});

			// show a simple article container if there's no procedure outline
			//if($('article').hasClass('category-fundamentals')) {
			if(<?php echo (empty(get_field('outline'))) ? "true" : "false"; ?>) {
				$('#content-simple').html($('#content-article div.tab-pane#main #the-content').html()).show();
				$('#content-article').hide();
				$('ul.nav-tabs').hide();
			}



			// generate anchors for all h4s in outline (because toc plugin doesn't work for ACF fields)
			$('#outline h4').each(function(index) {
				var text = $(this).text();
				link_id = text.replace(/\s+/g, '-');
				$(this).html('<span id="' + link_id + '">' + text + '</span>');
			});

			// load chapters from meta tags
			// DEPRECATED
			//loadChapters();

			/*$('#hide-chapter-btn').on('click', function() {
				$('#chapters').attr('class', 'col-sm-1').css('width', '8.33333%');

				$('.video-holder').attr('class', 'col-sm-11').css('width', '91.6666666%');
				$(this).text('show chapters');
			});*/

			// load url time code, if given
			loadTimeCode();

			// tracker for elapsed time (in seconds)
			var elapsed = 0;

			// runs each time the video advances a second
			wistiaEmbed.bind("secondchange", function (s) {

				//increment elapsed time
				elapsed++;

				//generate share url
				var hours = Math.floor(wistiaEmbed.time() / 3600);
				var minutes = Math.floor((wistiaEmbed.time() - (hours * 3600)) / 60);
				var seconds = Math.floor(wistiaEmbed.time() - (hours * 3600) - (minutes * 60));

				//var share_url = window.location.href + '?t=';
				var share_url = "<?php echo (site_url() . '/article/' . get_field('publication_id') . '/?t='); ?>";
				if(hours > 0) share_url += (hours + 'h');
				if(minutes > 0) share_url += (minutes + 'm');
				if(seconds > 0) share_url += (seconds + 's');

				if(!$('#url-share-box').is(':focus')) {
					$('#url-share-box').attr('value',share_url);
				}

				//======================
				// GENERATED JAVASCRIPT
				// =====================
				<?php //$access_blocks = array_reverse($access_blocks); 
				 if(is_array($access_blocks)) { foreach($access_blocks as $block) { 
					if($block['time_elapsed'] == 'custom' && !empty($custom_stop)) {?>
						// custom elapsed time
						if(elapsed >= <?php echo $custom_stop; ?>) {
							// block it
							block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
						}
					<?php } elseif($block['time_elapsed'] == 'custom' && empty($custom_stop)) {?>
						
					<?php } elseif($block['time_elapsed'] > 0) {?>
						if(elapsed >= <?php echo $block['time_elapsed']; ?>) {
							// block it
							block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
						}
					// custom start time
					<?php } elseif ($block['time_start'] == 'custom' && !empty($custom_stop)) { ?>
						if(s >= <?php echo $custom_stop ?>) {
							block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
						}
					<?php } elseif($block['time_start'] == 'custom' && empty($custom_stop)) {?>

					<?php } elseif ($block['time_start'] > 0) { ?>
						if(s >= <?php echo $block['time_start']; ?>) {
							block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
						}
					// block immediately
					<?php } else { ?>
						block("<?php echo $block['msg']; ?>", <?php echo ($block['closable'] > 0) ? 'true' : 'false';?>);
					<?php } 

				} }?>
				// ==========================
				// END GENERATED JAVASCRIPT
				// ==========================
				
				// chapter control
				/*$('.vtime-item').removeClass('done').removeClass('current');
				$('.vtime-item').each(function(index) {
					if($(this).attr('data-time') <= s) {
						$(this).addClass('done');
					} else {
						$('span.vtime-item:nth-child('+index+')').addClass('current');
						return false;
					}
				});*/

				// highlight chapters

				// helper var to check if nothing was highlighted
				var noneHighlighted = true;

				// loop thru all chapters
				for(var i = 0; i < chapters.length; i++) {
					// grab meta
					var chapter = chapters[i];
					var time = chapter.attr('data-time');

					// if the chapter is at or before the time of the video
					if(time <= s) {
						// something will be highlighted
						noneHighlighted = false;

						// if not already set
						if(!chapter.hasClass('current')) {
							// make current
							chapter.addClass('current');
							// remove current status of previous chapter
							if(i > 0) {
								chapters[i-1].removeClass('current');
								chapters[i-1].addClass('done');
							}
						// if already set
						} else {
							if(i > 0) {
								// remove current status of previous chapter
								chapters[i-1].removeClass('current');
								chapters[i-1].addClass('done');
							}
						}
					} else {
						// clear statuses
						if(chapter.hasClass('done')) chapter.removeClass('done');
						if(chapter.hasClass('current')) chapter.removeClass('current');
					}
				}

				// highlight last chapter if nothing else is
				if(noneHighlighted) {
					chapters[chapters.length-1].addClass('current');
				} 

				//highlight parent chapters if one exists
				var cur_chapter = $('.vtime-item.current');
				if(cur_chapter != null) {
					// grab parent chapter
					var parent_chapter = cur_chapter.attr('data-parent-chapter');
					if(parent_chapter != null) {
						// highlight parent chapter
						parent_chapter = $('.panel-heading#' + parent_chapter + 'heading span.vtime-item');
						//parent_chapter.addClass('current');
						parent_chapter.removeClass('done');
					}
				}

			});

			/**
			 * use ajax to show blocks when prompted
			 * @param  {[type]} msg      [description]
			 * @param  {[type]} closable [description]
			 * @return {[type]}          [description]
			 */
			function block(msg, closable) {

				if(blocked) return;

				if(closable) blocked = true;

				var function_name = msg;
				
				wistiaEmbed.pause();
				$('.access-block').show();
				wistiaEmbed.pause();

			 if (document.cancelFullScreen) {
					document.cancelFullScreen();
			 } else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
			 } else if (document.webkitCancelFullScreen) {
					document.webkitCancelFullScreen();
			 }
			 // hard block?
			 //$('.video-holder').empty();
			 //wistiaEmbed.remove();

			 console.log(function_name);

			 $.post(MyAjax.ajaxurl, {
				action: function_name,
				id: <?php echo get_the_ID(); ?>,
				msg: 'ACCESS RESTRICTED',
				redirectto: $('#url-share-box').attr('value')
			 }, 
			 function(response) {
				wistiaEmbed.pause();
				response = response.substring(0, response.length - 1);
				$('.access-block').find('#content').empty().html(response);
			 });
			}

			/**
			 * load time code from url and skip to it
			 * @return {[type]} [description]
			 */
			function loadTimeCode() {
				// grab GET variables
				var time_code = "<?php echo $get_time_code; ?>";

				// i dont know how to do regex.
				// tested it at regexr. it works for now
				var sec_regex = /(\d*)(?=s)/g;
				var min_regex = /(\d*)(?=m)/g;
				var hr_regex = /(\d*)(?=h)/g;

				// apply regex
				var seconds = sec_regex.exec(time_code);
				seconds = (seconds == null) ? 0 : parseInt(seconds[0]);

				var minutes = min_regex.exec(time_code);
				minutes = (minutes == null) ? 0 : parseInt(minutes[0]);

				var hours = hr_regex.exec(time_code);
				hours = (hours == null) ? 0 : parseInt(hours[0]);

				// add it all up
				var total = seconds + (minutes * 60) + (hours * 3600);

				// for some reason wistia likes to scrub 5 seconds ahead when using the time() function
				// edit: looks like wistia fixed it, leaving code in just in case
				//if (total > 5) total -= 5;

				// skip to that time
				if(total > 0) {
					wistiaEmbed.time(total);
					wistiaEmbed.play();
				}
			}

			/**
			 * load chapters from meta tags
			 * DEPRECATED
			 * @return {[type]} [description]
			 */
			function loadChapters() {
				// generate chapters from metadata
				$('#meta-chapters section').each(function(){
					$('#chapters ul').append('<li class="vtime-item" data-time="'+$(this).data('time')+'"><a href="#video" onclick="wistiaEmbed.time('+$(this).data('time')+').play();">'+$(this).data('title')+'</a></li>');
				});

				// if no valid metadata exists, and acf repeater field exists, grab from acf repeater fields
				// do this synchronously so the video doesnt stutter
				if($('#chapters ul').is(':empty') && <?php echo $chapter_count; ?> > 0) {
					$('#chapters ul').html("<?php echo $chapters_html; ?>");
				}

				$('#chapters').show();

				// dont display chapters if none are grabbed from meta or acf
				if($('#chapters ul').is(':empty')) {
					$('#chapters').hide();
					$('.video-holder').attr('class', 'col-sm-12').css('width', '100%').css('padding', '0 0');
				}
			}


			// scroll up function from the jquery plugin
			$.scrollUp({
				scrollName: 'scrollUp', // Element ID
				topDistance: '300', // Distance from top before showing element (px)
				topSpeed: 300, // Speed back to top (ms)
				animation: 'fade', // Fade, slide, none
				animationInSpeed: 200, // Animation in speed (ms)
				animationOutSpeed: 200, // Animation out speed (ms)
				//scrollText: 'Scroll to top', // Text for element
				activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
			});
		});
	</script>
<?php endwhile; ?>
