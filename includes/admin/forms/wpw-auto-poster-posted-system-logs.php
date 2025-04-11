<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Social Posted Logs List
 *
 * The html markup for the system logs
 * 
 * @package Social Auto Poster
 * @since 2.7.9
 */
global $wpw_auto_poster_logs,$wp_filesystem;
if (empty($wp_filesystem)) {
    require_once (ABSPATH . '/wp-admin/includes/file.php');
    WP_Filesystem();
}

$log_file = WPW_AUTO_POSTER_LOG_DIR.$wpw_auto_poster_logs->wpw_auto_poster_file_name( 'logs' );

if( isset( $_POST['wpw_auto_poster_log_action'] ) && $_POST['wpw_auto_poster_log_action'] == 'clear-log' && wp_verify_nonce($_POST['wpw_clear_debuglog'], 'wpw_auto_poster_clear_log') ){
	$wpw_auto_poster_logs->wpw_auto_poster_clear('logs');
}
?>
<div class="wrap posting-debug-logs">
<?php wpw_slg_header_menu(); ?>
<div class="sub-header">
	<div class="woo-slg-top-header-wrap">
		<div class="logo-header-wrap">
			<div class="posting-debug-title">
				<!-- wpweb logo -->
				<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="<?php esc_html_e( 'Logo', 'wpwautoposter' );?>" />
				<?php esc_html_e( 'Posting Debug Logs', 'wpwautoposter' ); ?> <small><?php esc_html_e( '(Debug Logs will be cleared automatically every week.)', 'wpwautoposter' ); ?></small></div>
			<?php $auto_poster_posted_system_log = apply_filters( 'wpweb_auto_poster_posted_system_log', true ); ?>
			<?php if ( $auto_poster_posted_system_log ) { ?>
				<form method="post" action="" class="clear-log woo-slg-reset-wrapper">
					<?php wp_nonce_field( 'wpw_auto_poster_clear_log', 'wpw_clear_debuglog' );?>
					<input type="hidden" name="wpw_auto_poster_log_action" value="clear-log">
					<input type="submit" class="button-primary wpw-auto-poster-reset-button" name="wpw_auto_poster_log_submit" value="<?php esc_html_e( 'Clear log', 'wpwautoposter' ); ?>">
				</form>
			<?php } ?>
		</div>
	</div>
</div>
<div class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
								
					<!-- general settings box title -->
					<h3 class="hndle">
						<span class="wpw_common_verticle_align"><?php esc_html_e( 'Debug Logs', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
						<div id="wpw-log-viewer" class="wpw-log-viewer">
							<?php if( file_exists( $log_file ) ){
								if( is_readable( $log_file ) ) { // if the file is readable
								?>
								<code>
									<?php echo esc_html( $wp_filesystem->get_contents(trim( $log_file ) ) ); ?>
								</code>
							<?php 
								} else{ // if file is not readable
									?>
									<div class="wpw-auto-poster-error"><p><?php esc_html_e( 'Log file does not have read permission. Please assign read permission for the file ', 'wpwautoposter' ); ?><code><?php print esc_html($log_file);?></code></p></div>	
							<?php }
							}
							else{ ?>
								<p><?php esc_html_e( 'Log file not found.', 'wpwautoposter' ); ?></p>
							<?php }?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>				
</div>