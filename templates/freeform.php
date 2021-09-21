<?php
/*
Template Name: Freeform Pages
Template Post Type: TeamPages
*/
get_header();
$post_id = get_the_ID();
?>
					<h1 class="entry-title main_title"><?php the_title(); ?></h1>
<?php
				//		the_content();
?>
					<!-- Jitsi/Showtime Script -->
					<script src="https://showtime.theair.works/external_api.js"></script>
					<script src="https://showtime2.theair.works/external_api.js"></script>
						<script>
							var domain = "<?php echo get_post_meta($post->ID, 'showtimeServer', true); ?>";
							var options = {
								roomName: "<?php echo get_post_meta($post->ID, 'JitsiChannel', true); ?>",
								interfaceConfigOverwrite: { APP_NAME: 'TheAirWorks Showtime' },
								width: <?php echo get_post_meta($post->ID, 'showtimeWidth', true); ?>,
								height: <?php echo get_post_meta($post->ID, 'showtimeHeight', true); ?>,
								parentNode: jitsiContainer,
								configOverwrite: {
									startWithAudioMuted: <?php echo get_post_meta($post->ID, 'StartMuted', true); ?>
							},
								interfaceConfigOverwrite: {
								filmStripOnly: false
								}
							}
							var api = new JitsiMeetExternalAPI(domain, options);
							api.executeCommand('toggleTileView');

						</script>
<?php

get_footer();
