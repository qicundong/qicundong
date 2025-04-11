<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Pinterest Settings
 *
 * The html markup for the Pinterest settings tab.
 *
 * @package Social Auto Poster
 * @since 2.6.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_pin_posting;

// model class
$model = $wpw_auto_poster_model;

$cat_posts_type = !empty( $wpw_auto_poster_options['pin_posting_cats'] ) ? $wpw_auto_poster_options['pin_posting_cats']: 'exclude';

// pinterest posting class
$pinposting = $wpw_auto_poster_pin_posting;

$pinterest_keys = isset( $wpw_auto_poster_options['pinterest_keys'] ) ? $wpw_auto_poster_options['pinterest_keys'] : array();

$wpw_auto_poster_pin_sess_data = get_option( 'wpw_auto_poster_pin_sess_data' ); // Getting pinterest app grant data

$pin_wp_pretty_url = ( !empty( $wpw_auto_poster_options['pin_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['pin_wp_pretty_url'] : '';

$pin_selected_shortner = isset( $wpw_auto_poster_options['pin_url_shortener'] ) ? $wpw_auto_poster_options['pin_url_shortener'] : '';

$pin_wp_pretty_url = !empty( $pin_wp_pretty_url ) ? ' checked="checked"' : '';
$pin_wp_pretty_url_css = ( $pin_selected_shortner == 'wordpress' ) ? ' ba_wp_pretty_url_css': ' post_msg_style_hide';

$error_msgs = $app_method_err = array();
$readonly = "";

// Check if site is ssl enabled, if not than set error message.
if( !is_ssl() ) {
   $app_method_err[] = sprintf( esc_html__( 'Pinterest APP Method requires %sSSL%s for posting to boards.', 'wpwautoposter' ), '<b>', '</b>' );
   $readonly = 'readonly';
}

$redirect_uri = admin_url('admin.php');

// get url shortner service list array 
$pin_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$pin_exclude_cats = array();

$pin_custom_msg_options = isset( $wpw_auto_poster_options['pin_custom_msg_options'] ) ? $wpw_auto_poster_options['pin_custom_msg_options'] : 'global_msg';

if( $pin_custom_msg_options == 'global_msg') {
	$post_msg_style = "post_msg_style_hide";
	$global_msg_style = "";
} else{
	$global_msg_style = "post_msg_style_hide";
	$post_msg_style = "";
} ?>

<!-- beginning of the pinterest general settings meta box -->
<div id="wpw-auto-poster-pinterest-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="pinterest_general" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/pinterest_set.png" alt="Pinterest">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'Pinterest General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
						<?php if(!empty($error_msgs)) { ?>
							<div class="wpw-auto-poster-error">
                                <ul>
                                    <?php foreach ( $error_msgs as $error_msg ) { ?>
                                        <li><?php print( $error_msg ); ?></li>
                                    <?php } ?>
                                </ul>
							</div>
						<?php } ?>
						<table class="form-table">											
							<tbody>				
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_pinterest]"><?php esc_html_e( 'Enable Autoposting : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<div class="d-flex-wrap fb-avatra">
											<label for="wpw_auto_poster_options[enable_pinterest]" class="toggle-switch">
												<input name="wpw_auto_poster_options[enable_pinterest]" id="wpw_auto_poster_options[enable_pinterest]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_pinterest'] ) ) { checked( '1', $wpw_auto_poster_options['enable_pinterest'] ); } ?> />
												<span class="slider"></span>
											</label>
											<p> <?php esc_html_e( 'Check this box, if you want to automatically post your new content to Pinterest.', 'wpwautoposter' ); ?> </p>
										</div>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_pinterest_for]"><?php esc_html_e( 'Enable Autoposting for : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<ul class="enable-autoposting">
										<?php 
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();
											
											if( !empty( $wpw_auto_poster_options['enable_pinterest_for'] ) ) {
												$prevent_meta = $wpw_auto_poster_options['enable_pinterest_for'];
											} else {
												$prevent_meta = array();
											}

											if( !empty( $wpw_auto_poster_options['pin_post_type_tags'] ) ) {
												$pin_post_type_tags = $wpw_auto_poster_options['pin_post_type_tags'];
											} else {
												$pin_post_type_tags = array();
											}

											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

											if( !empty( $wpw_auto_poster_options['pin_post_type_cats'] ) ) {
												$pin_post_type_cats = $wpw_auto_poster_options['pin_post_type_cats'];
											} else {
												$pin_post_type_cats = array();
											}

											// Get saved categories for pinterest to exclude from posting
											if( !empty( $wpw_auto_poster_options['pin_exclude_cats'] ) ) {
												$pin_exclude_cats = $wpw_auto_poster_options['pin_exclude_cats'];
											} 
											
											foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;										
													if( isset( $type->labels ) ) {
														$label = $type->labels->name ? $type->labels->name : $type->name;
										            } else {
										            	$label = $type->name;
										            }

													if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
													$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : ''; ?>
															
											<li class="wpw-auto-poster-prevent-types">
												<input type="checkbox" id="wpw_auto_posting_pinterest_prevent_<?php echo esc_attr($type->name); ?>" name="wpw_auto_poster_options[enable_pinterest_for][]" value="<?php echo $type->name; ?>" <?php echo esc_attr($selected); ?>/>
																						
												<label for="wpw_auto_posting_pinterest_prevent_<?php echo esc_attr($type->name); ?>"><?php echo esc_html($label); ?></label>
											</li>
											
											<?php	} ?>
										</ul>
										<p><small><?php esc_html_e( 'Check each of the post types that you want to post automatically to Pinterest when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>
									
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_post_type_tags][]"><?php esc_html_e( 'Select Tags for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[pin_post_type_tags][]" id="wpw_auto_poster_options[pin_post_type_tags]" class="pin_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
											<?php foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;	

													if(in_array( $type->name, $prevent_meta )) {

														if( isset( $type->labels ) ) {
															$label = $type->labels->name ? $type->labels->name : $type->name;
											            }
											            else {
											            	$label = $type->name;
											            }

														if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
	                							
	                									echo '<optgroup label="'.esc_attr($label).'">';
										                // Loop on all taxonomies
										                foreach ($all_taxonomies as $taxonomy){

										                	$selected = '';
										                	if( !empty( $static_post_type_arr[$type->name] ) && $static_post_type_arr[$type->name] != $taxonomy->name){
                             										continue;
                    										}
										                	if(isset($pin_post_type_tags[$type->name]) && !empty($pin_post_type_tags[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $pin_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

										                        echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											} ?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
										<p><small><?php esc_html_e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashtags}.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_post_type_cats][]"><?php esc_html_e( 'Select Categories for hashtags : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<select name="wpw_auto_poster_options[pin_post_type_cats][]" id="wpw_auto_poster_options[pin_post_type_cats]" class="pin_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
											<?php foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;	

													if(in_array( $type->name, $prevent_meta )) {
														
														if( isset( $type->labels ) ) {
															$label = $type->labels->name ? $type->labels->name : $type->name;
											            }
											            else {
											            	$label = $type->name;
											            }

														if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
	                							
	                									echo '<optgroup label="'.esc_attr($label).'">';
										                // Loop on all taxonomies
										                foreach ($all_taxonomies as $taxonomy){

										                	$selected = '';
										                	if(isset($pin_post_type_cats[$type->name]) && !empty($pin_post_type_cats[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $pin_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

										                        echo '<option value="' . esc_attr($type->name)."|".esc_attr($taxonomy->name) . '" '.esc_attr($selected).'>'.esc_html($taxonomy->label).'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . "/ajax-loader.gif";?>"/></div>
										<p><small><?php esc_html_e( 'Select the Categories for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php esc_html_e('{hashcats}.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_exclude_cats][]"><?php esc_html_e( 'Select Taxonomies : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td class="wpw-auto-poster-select">
										<div class="wpw-auto-poster-cats-option">
											<div class="radio-button-wrap">
												<input name="wpw_auto_poster_options[pin_posting_cats]" id="pin_cats_include" type="radio" value="include" <?php checked( 'include', $cat_posts_type ); ?> />
												<label for="pin_cats_include"><?php esc_html_e( 'Include ( Post only with )', 'wpwautoposter');?></label>
											</div>
											<div class="radio-button-wrap">
												<input name="wpw_auto_poster_options[pin_posting_cats]" id="pin_cats_exclude" type="radio" value="exclude" <?php checked( 'exclude', $cat_posts_type ); ?> />
												<label for="pin_cats_exclude"><?php esc_html_e( 'Exclude ( Do not post )', 'wpwautoposter');?></label>
											</div>
										</div>
										<select name="wpw_auto_poster_options[pin_exclude_cats][]" id="wpw_auto_poster_options[pin_exclude_cats]" class="pin_exclude_cats ajax-taxonomy-search wpw-auto-poster-cats-exclude-select" multiple="multiple">
											
										<?php

											$pt_exclude_cats_selected_values = !empty($wpw_auto_poster_options['pin_exclude_cats']) ? $wpw_auto_poster_options['pin_exclude_cats'] : array();
											$selected = 'selected="selected"';

											if(!empty($pt_exclude_cats_selected_values)) {

												foreach ($pt_exclude_cats_selected_values as $post_type => $post_data) {
													
													if( !empty( $post_data ) && is_array( $post_data ) ){
														
														foreach( $post_data as $key => $cat_data ) {
															
															$term              = get_term( $cat_data );
															$get_taxonomy_data = get_taxonomy( $term->taxonomy );
															$cat_name          = $get_taxonomy_data->label." : ".$term->name;
															echo '<option value="' . esc_attr($post_type) . "|" . esc_attr($cat_data) . '" ' . esc_attr($selected) . '>' . esc_html($cat_name) . '</option>';                                                    
															
															
														}

													}

												}    

											}
											?>

										</select>
										<p><small><?php esc_html_e( 'Select the Taxonomies for each post type that you want to include or exclude for posting.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_url_shortener]"><?php esc_html_e( 'URL Shortener : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[pin_url_shortener]" id="wpw_auto_poster_options[pin_url_shortener]" class="pin_url_shortener" data-content='pin'>
											<?php
											foreach( $pin_url_shortener as $key => $option ) { ?>
												<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $pin_selected_shortner, $key ); ?>>
													<?php echo esc_html($option); ?>
												</option>
											<?php
											} ?>
										</select>
										<p><small><?php esc_html_e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>

								<tr id="row-pin-wp-pretty-url" valign="top" class="<?php print esc_attr($pin_wp_pretty_url_css);?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_wp_pretty_url]"><?php esc_html_e( 'Pretty permalink URL : ', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<div class="d-flex-wrap fb-avatra">
											<label for="wpw_auto_poster_options[pin_wp_pretty_url]" class="toggle-switch">
												<input type="checkbox" name="wpw_auto_poster_options[pin_wp_pretty_url]" id="wpw_auto_poster_options[pin_wp_pretty_url]" class="pin_wp_pretty_url" data-content='pin' value="yes" <?php print esc_attr($pin_wp_pretty_url);?>>
												<span class="slider"></span>
											</label>
											<p><?php printf( esc_html( 'Check this box if you want to use pretty permalink. i.e. %s. (Not Recommnended).', 'wpwautoposter' ), esc_url("http://example.com/test-post/")); ?></p>
										</div>

									</td>
								</tr>
								
								<?php
								if( $pin_selected_shortner == 'bitly' ) {
									$class = '';
								} else {
									$class = 'post_msg_style_hide';
								}
								
								if( $pin_selected_shortner == 'shorte.st' ) {
									$shortest_class = '';
								} else {
									$shortest_class = 'post_msg_style_hide';
								} ?>
								
								<tr valign="top" class="pin_setting_input_bitly <?php echo esc_attr($class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_bitly_access_token]"><?php esc_html_e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<?php
										$pin_bitly_access_token = isset( $wpw_auto_poster_options['pin_bitly_access_token'] ) ? $wpw_auto_poster_options['pin_bitly_access_token'] : ''; ?>

										<input type="text" name="wpw_auto_poster_options[pin_bitly_access_token]" id="wpw_auto_poster_options[pin_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $pin_bitly_access_token ); ?>" class="large-text">
									</td>
								</tr>
								
								<tr valign="top" class="pin_setting_input_shortest <?php echo esc_attr($shortest_class); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_shortest_api_token]"><?php esc_html_e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[pin_shortest_api_token]" id="wpw_auto_poster_options[pin_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['pin_shortest_api_token'] ); ?>" class="large-text">
									</td>
								</tr>

								<?php
								echo apply_filters ( 
									 'wpweb_fb_settings_submit_button', 
									 '<tr valign="top">
											<td colspan="2">
												<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
											</td>
										</tr>'
								); ?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
							
			</div><!-- #pinterest_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-pinterest-general -->
<!-- end of the pinterest general settings meta box -->

<!-- beginning of the pinterest api settings meta box -->
<div id="wpw-auto-poster-pinterest-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="pinterest_api" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'Pinterest API Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
						<table id="pinterest-app-api" class="form-table wpw-auto-poster-pinterest-settings <?php print esc_attr($app_method_style); ?>">
							<tbody>
								<?php
									if( !empty($app_method_err) ) { ?>
								<tr valign="top">
									<td colspan="4">
										
											<div class="wpw-auto-poster-error"><ul>
			                                    <?php
			                                    foreach( $app_method_err as $error_msg ) { ?>
			                                        <li><?php echo $error_msg;?></li>
			                                    <?php } ?>
			                                </ul></div>
									</td>
								</tr>
								<?php } ?>
								<tr valign="top">
									<th scope="row"><label>
										<?php esc_html_e( 'Pinterest Application:', 'wpwautoposter' ); ?>
									</label></th>
									<td colspan="3">
										<p>
											<?php
											esc_html_e( 'Before you can start publishing your content to Pinterest you need to create a Pinterest Application.', 'wpwautoposter' ); ?>
										</p> 
										<p><?php printf( esc_html__('You can get a step by step tutorial on how to create a Pinterest Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="'.esc_url('https://docs.wpwebelite.com/social-network-integration/pinterest/').'" target="_blank">', '</a>' ); ?>
										</p> 
									</td>
								</tr>
								
								<tr>
									<th scope="row"><label>
										<?php esc_html_e( 'Allowing permissions:', 'wpwautoposter' ); ?>
									</label></th>
									<td colspan="3">
										<p><?php esc_html_e( 'Posting content to your chosen Pinterest boards requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.', 'wpwautoposter' ); ?></p>
									</td>
								</tr> 
								
								<tr>
									<td colspan="4">
										<p class="wpw-auto-poster-error-box warning-message"><?php echo sprintf(esc_html__( "%s Note : %sYou need to define redirect uri as mentioned below when you create Pinterest Application. Otherwise pinterest won't redirect you to the correct page after authorization. Replace {app_id} with your pinterest application key/id.%s", "wpwautoposter"), "<b>", "</b>", "</br><code class='wpw-auto-poster-url'>".esc_html($redirect_uri)."</code>"); ?></p>
									</td>
								</tr>

								<tr><td class="no-padding" colspan="4">
	                                <table class="wpw-auto-poster-form-table-resposive">
										<thead><tr valign="top">
											<td scope="row"><strong>
												<label for="wpw_auto_poster_options[pinterest_keys][0][app_id]"><?php esc_html_e( 'Pinterest App ID/API Key', 'wpwautoposter' ); ?></label>
											</strong></td>
											<td scope="row"><strong>
												<label for="wpw_auto_poster_options[pinterest_keys][0][app_secret]"><?php esc_html_e( 'Pinterest App Secret', 'wpwautoposter' ); ?></label>
											</strong></td>
											<td scope="row"><strong>
												<label><?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?></label><strong>
											<strong></td>
											<td></td>
										</tr></thead>

										<tbody>
										<?php
										if( !empty( $pinterest_keys ) ) {
											foreach($pinterest_keys as $pinterest_key => $pinterest_value) {
											
											// Don't disply delete link for first row
											$pinterest_delete_class = empty( $pinterest_key ) ? '' : ' wpw-auto-poster-display-inline '; ?>

											<tr valign="top" class="wpw-auto-poster-pinterest-account-details" data-row-id="<?php echo esc_attr($pinterest_key); ?>">
												<td scope="row" width="25%" data-label="<?php esc_html_e( 'Pinterest App ID/API Key', 'wpwautoposter' ); ?>">
													<input type="text" name="wpw_auto_poster_options[pinterest_keys][<?php echo esc_attr($pinterest_key); ?>][app_id]" value="<?php echo $model->wpw_auto_poster_escape_attr( $pinterest_value['app_id'] ); ?>" class="large-text wpw-auto-poster-pinterest-app-id" <?php echo esc_attr($readonly);?>/>
													<p><small><?php esc_html_e( 'Enter Pinterest App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td scope="row" width="30%" data-label="<?php esc_html_e( 'Pinterest App Secret', 'wpwautoposter' ); ?>">
													<input type="text" name="wpw_auto_poster_options[pinterest_keys][<?php echo esc_attr($pinterest_key); ?>][app_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr( $pinterest_value['app_secret'] ); ?>" class="large-text wpw-auto-poster-pinterest-app-secret" <?php echo esc_attr($readonly);?>/>
													<p><small><?php esc_html_e( 'Enter Pinterest App Secret.', 'wpwautoposter' ); ?></small></p>  
												</td>
												<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>">
													<?php
													if( !empty($pinterest_value['app_id']) && !empty($pinterest_value['app_secret']) && !empty($wpw_auto_poster_pin_sess_data[ $pinterest_value['app_id'] ]) )  {
														
														echo '<p>' . esc_html__( 'You already granted extended permissions.', 'wpwautoposter' ) . '</p>';	
														echo apply_filters ( 'wpweb_pin_settings_reset_session', sprintf( esc_html__( "%s Reset User Session %s", 'wpwautoposter' ), 
														"<a href='".add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'pin_reset_user' => '1', 'wpw_pin_app' => $pinterest_value['app_id'] ), admin_url( 'admin.php' ) )."'>",
														"</a>"
														 ) );
												
													} elseif( !empty($pinterest_value['app_id']) && !empty($pinterest_value['app_secret']) ) {
														echo '<p><a href="' . esc_url($pinposting->wpw_auto_poster_get_pinterest_login_url( $pinterest_value['app_id'] )) . '">' . esc_html__( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
													} ?>
												</td>
												<td>
													<a href="javascript:void(0);" class="wpw-auto-poster-delete-pin-account wpw-auto-poster-pinterest-remove <?php echo esc_attr($pinterest_delete_class); ?>" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL); ?>/close-icon.svg" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
												</td>
											</tr>
										<?php 
										}
									} else { ?>
										<tr valign="top" class="wpw-auto-poster-pinterest-account-details" data-row-id="<?php echo empty($pinterest_key) ? '': esc_attr($pinterest_key); ?>">
											<td scope="row" width="25%" data-label="<?php esc_html_e( 'Pinterest App ID/API Key', 'wpwautoposter' ); ?>">
												<input type="text" name="wpw_auto_poster_options[pinterest_keys][0][app_id]" value="" class="large-text wpw-auto-poster-pinterest-app-id" <?php echo esc_attr($readonly);?>/>
												<p><small><?php esc_html_e( 'Enter Pinterest App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
											</td>
											<td scope="row" width="30%" data-label="<?php esc_html_e( 'Pinterest App Secret', 'wpwautoposter' ); ?>">
												<input type="text" name="wpw_auto_poster_options[pinterest_keys][0][app_secret]" value="" class="large-text wpw-auto-poster-pinterest-app-secret" <?php echo esc_attr($readonly);?>/>
												<p><small><?php esc_html_e( 'Enter Pinterest App Secret.', 'wpwautoposter' ); ?></small></p>  
											</td>
											<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data" data-label="<?php esc_html_e( 'Allowing permissions', 'wpwautoposter' ); ?>"></td>
											<td>
												<a href="javascript:void(0);" class="wpw-auto-poster-delete-pin-account wpw-auto-poster-pinterest-remove" title="<?php esc_html_e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL); ?>/close-icon.svg" alt="<?php esc_html_e('Delete','wpwautoposter'); ?>"/></a>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</td></tr>

							<?php $pin_api_add_more = apply_filters( 'wpweb_pin_api_add_more_button', true ); ?>
            				<?php if( $pin_api_add_more ){ ?>
								<tr>
									<td colspan="4">
										<a class='wpw-auto-poster-add-more-pin-account wpw-auto-poster-add-more-fbs-account button-primary' href='javascript:void(0);'><?php esc_html_e( 'Add more', 'wpwautoposter' ); ?></a>
									</td>
								</tr>
							<?php } ?> 
								
							<?php
							echo apply_filters ( 
								'wpweb_fb_settings_submit_button', 
								'<tr valign="top">
									<td colspan="4">
										<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
									</td>
								</tr>'
							); ?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
			</div><!-- #pinterest_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-pinterest-api -->
<!-- end of the pinterest api settings meta box -->

<?php if( isset($wpw_auto_poster_options['app_id']) && !empty($wpw_auto_poster_options['app_id']) && isset($wpw_auto_poster_options['app_secret']) && !empty($wpw_auto_poster_options['app_secret'])  ) { ?>
<?php } ?>

<!-- beginning of the pinterest proxy settings meta box -->
<div id="wpw-auto-poster-pinterest-proxy" class="post-box-container">
    <div class="metabox-holder">	
        <div class="meta-box-sortables ui-sortable">
            <div id="pinterest_proxy" class="postbox">	
                <div class="handlediv" title="<?php esc_html_e('Click to toggle', 'wpwautoposter'); ?>"><br /></div>
                <h3 class="hndle">
                    <span class='wpw-sap-buffer-app-settings'><?php esc_html_e('Pinterest Proxy Settings', 'wpwautoposter'); ?></span>
                </h3>
                <div class="inside">                    	
                    <table class="form-table">											
                        <tbody>				
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wpw_auto_poster_options[pin_proxy_enable]"><?php esc_html_e('Enable Proxy : ', 'wpwautoposter'); ?></label>
                                </th>
                                <td>
                                	<div class="d-flex-wrap fb-avatra">
                                		<label for="wpw_auto_poster_options[pin_proxy_enable]" class="toggle-switch">
		                                    <input name="wpw_auto_poster_options[pin_proxy_enable]" id="wpw_auto_poster_options[pin_proxy_enable]" type="checkbox" value="1" <?php
		                                    if (isset($wpw_auto_poster_options['pin_proxy_enable'])) {
		                                        checked('1', $wpw_auto_poster_options['pin_proxy_enable']);
		                                    }
		                                    ?> />   
		                                    <span class="slider"></span>
		                                </label>
										<p><?php esc_html_e( 'Enable / Disable Proxy setting for Pinterest', 'wpwautoposter' ); ?></p>
									</div>                                 
                                </td>
                            </tr>
                        <tbody> 
                    </table>    
                    <?php
                    if(isset($wpw_auto_poster_options['pin_proxy_enable']) && $wpw_auto_poster_options['pin_proxy_enable']==1){
                        $pin_proxy_setting_hide = '';
                    }else{
                        $pin_proxy_setting_hide = 'none';
                    }
                    ?> 

                    <table id="pinterest-proxy-settings" class="form-table wpw-auto-poster-form-table-resposive gap-20" style="display:<?php echo $pin_proxy_setting_hide;?>">                            
                        <thead>
                            <tr valign="top">
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[pin_proxy_url]"><?php esc_html_e('Proxy URL', 'wpwautoposter'); ?></label></strong>
                                </td>
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[pin_proxy_username]"><?php esc_html_e('Proxy Username', 'wpwautoposter'); ?></label></strong>
                                </td>
                                <td scope="row">
                                    <strong><label for="wpw_auto_poster_options[pin_proxy_password]"><?php esc_html_e('Proxy Password', 'wpwautoposter'); ?></label></strong>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr valign="top">                               
                                <td>
                                    <input type="url" name="wpw_auto_poster_options[pin_proxy_url]" id="wpw_auto_poster_options[pin_proxy_url]" value="<?php if(isset($wpw_auto_poster_options['pin_proxy_url'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['pin_proxy_url']); }?>" class="large-text wpw-auto-poster-pinterest-proxy-url" />                                 
									<p><small><?php esc_html_e( 'Enter Proxy URL', 'wpwautoposter' ); ?></small></p>
								</td>                            
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[pin_proxy_username]" id="wpw_auto_poster_options[pin_proxy_username]" value="<?php if(isset($wpw_auto_poster_options['pin_proxy_username'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['pin_proxy_username']); }?>" class="large-text wpw-auto-poster-pinterest-proxy-username" />                                 
									<p><small><?php esc_html_e( 'Enter Proxy Username', 'wpwautoposter' ); ?></small></p>
								</td>                           
                                <td>
                                    <input type="text" name="wpw_auto_poster_options[pin_proxy_password]" id="wpw_auto_poster_options[pin_proxy_password]" value="<?php if(isset($wpw_auto_poster_options['pin_proxy_password'])){ echo $model->wpw_auto_poster_escape_attr($wpw_auto_poster_options['pin_proxy_password']); }?>" class="large-text wpw-auto-poster-pinterest-proxy-password" />                                 
									<p><small><?php esc_html_e( 'Enter Proxy Password', 'wpwautoposter' ); ?></small></p>
								</td>
                            </tr> 
                        </tbody>
                    </table>

                    <table class="form-table">											
                        <tbody>
                            <?php
                            echo apply_filters(
                                'wpweb_fb_settings_submit_button', '<tr valign="top">
                                    <td>
                                    <input type="submit" value="' . esc_html__('Save Changes', 'wpwautoposter') . '" name="wpw_auto_poster_set_submit" class="button-primary">
                                    </td>
                                </tr>'
                            );?>
                        </tbody>
                    </table>
                </div><!-- .inside -->
            </div><!-- #pinterest_proxy -->
        </div><!-- .meta-box-sortables ui-sortable -->
    </div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-pinterest-proxy -->
<!-- end of the pinterest proxy settings meta box -->


<!-- beginning of the autopost to pinterest meta box -->
<div id="wpw-auto-poster-autopost-pinterest" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="autopost_pinterest" class="postbox">	
				<div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span class='wpw_common_verticle_align'><?php esc_html_e( 'Autopost to Pinterest', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>
							
								<tr valign="top"> 
									<th scope="row">
										<label for="wpw_auto_poster_options[prevent_post_pin_metabox]"><?php esc_html_e( 'Do not allow individual posts : ', 'wpwautoposter' ); ?></label>
									</th>									
									<td>
										<div class="d-flex-wrap fb-avatra">
											<label for="wpw_auto_poster_options[prevent_post_pin_metabox]" class="toggle-switch">
												<input name="wpw_auto_poster_options[prevent_post_pin_metabox]" id="wpw_auto_poster_options[prevent_post_pin_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_pin_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_pin_metabox'] ); } ?> />
												<span class="slider"></span>
											</label>
											<p><?php esc_html_e( 'If you check this box, then it will hide meta settings for pinterest from individual posts.', 'wpwautoposter' ); ?></p>
										</div>

									</td>	
								</tr>
										
								<?php				
										
									$types = get_post_types( array( 'public'=>true ), 'objects' );
									$types = is_array( $types ) ? $types : array();
								?>
								<tr valign="top">
									<th scope="row">
										<label><?php esc_html_e( 'Map Autopost Location : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										
									<?php
									// Getting all pinterest account/boards
									$pin_accounts = wpw_auto_poster_get_pin_accounts( 'all_app_users_with_boards' );

									foreach( $types as $type ) {
										if( !is_object($type) ) continue;
										
										if( isset( $wpw_auto_poster_options['pin_type_' . $type->name . '_method'] ) ) {
											$wpw_auto_poster_pin_type_method = $wpw_auto_poster_options['pin_type_' . $type->name . '_method'];	
										} else {
											$wpw_auto_poster_pin_type_method = '';
										}

										if( isset( $type->labels ) ) {
											$label = $type->labels->name ? $type->labels->name : $type->name;
							            } else {
							            	$label = $type->name;
							            }
											
										if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media ?>

										<div class="wpw-auto-poster-fb-types-wrap">
											<div class="wpw-auto-poster-tw-types-label">
												<?php
												esc_html_e( 'Autopost', 'wpwautoposter' ); 
												echo ' '.esc_html($label); 
												esc_html_e( ' to Pinterest of this user(s)', 'wpwautoposter' ); ?>
											</div><!--.wpw-auto-poster-fb-types-label-->
											
											<div class="wpw-auto-poster-tw-users-acc">
												<?php
												if( isset( $wpw_auto_poster_options['pin_type_'.$type->name.'_user'] ) ) {
													$wpw_auto_poster_pin_type_user = $wpw_auto_poster_options['pin_type_'.$type->name.'_user'];	 
												} else {
													$wpw_auto_poster_pin_type_user = '';
												}
													
												$wpw_auto_poster_pin_type_user = ( array ) $wpw_auto_poster_pin_type_user; ?>

												<select name="wpw_auto_poster_options[pin_type_<?php echo esc_attr($type->name); ?>_user][]" multiple="multiple" class="wpw-auto-poster-users-acc-select">
													<?php
													if( !empty($pin_accounts) && is_array($pin_accounts) ) {
														foreach( $pin_accounts as $aid => $aval ) {
															if( is_array( $aval ) ) {

																$pin_app_data 	= isset( $wpw_auto_poster_pin_sess_data[$aid] ) ? $wpw_auto_poster_pin_sess_data[$aid] : array();

																$pin_opt_label	= !empty( $pin_app_data['wpw_auto_poster_pin_user_name'] ) ? $pin_app_data['wpw_auto_poster_pin_user_name'] .' - ' : '';
																$pin_opt_label	= $pin_opt_label . $aid; ?>
																
																<optgroup label="<?php echo esc_attr($pin_opt_label); ?>">
																			
																	<?php
																	foreach( $aval as $aval_key => $aval_data ) { ?>
																		<option value="<?php echo esc_attr($aval_key); ?>" <?php selected( in_array( $aval_key, $wpw_auto_poster_pin_type_user ), true, true ); ?> ><?php echo esc_html($aval_data); ?></option>
																	<?php } ?>
																</optgroup>
																			
															<?php
															} else { ?>
																<option value="<?php echo esc_attr($aid); ?>" <?php selected( in_array( $aid, $wpw_auto_poster_pin_type_user ), true, true ); ?> ><?php echo esc_html($aval); ?></option>
															<?php }
														} // End of foreach
													} // End of main if ?>

												</select>
											</div><!--.wpw-auto-poster-fb-users-acc-->
										</div><!--.wpw-auto-poster-fb-types-wrap-->
										<?php
										} ?>
										
									</td>
								</tr> 

								<tr valign="top">
									<th scope="row">
										<label><?php esc_html_e( 'Posting Format Options : ', 'wpwautoposter' ); ?></label>
									</th>
									<td class="wpw-auto-poster-cats-option">
										<div class="wpw-auto-poster-cats-option">
											<div class="radio-button-wrap">
												<input id="pin_custom_global_msg" type="radio" name="wpw_auto_poster_options[pin_custom_msg_options]" value="global_msg" <?php checked($pin_custom_msg_options, 'global_msg', true);?> class="custom_msg_options">
												<label for="pin_custom_global_msg" class="wpw-auto-poster-label-check"><?php esc_html_e( 'Global', 'wpwautoposter' ); ?></label>
											</div>
											<div class="radio-button-wrap">
												<input id="pin_custom_post_msg" type="radio" name="wpw_auto_poster_options[pin_custom_msg_options]" value="post_msg" <?php checked($pin_custom_msg_options, 'post_msg', true);?> class="custom_msg_options">
												<label for="pin_custom_post_msg" class="wpw-auto-poster-label-check"><?php esc_html_e( 'Individual Post Type Message', 'wpwautoposter' ); ?></label>
											</div>
	                                    </div>
									</td>	
								</tr>

								<tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options_pin_custom_img"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<?php
										$pin_custom_img = isset( $wpw_auto_poster_options['pin_custom_img'] ) ? $wpw_auto_poster_options['pin_custom_img'] : ''; ?>

										<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $pin_custom_img ); ?>" name="wpw_auto_poster_options[pin_custom_img]" id="wpw_auto_poster_options_pin_custom_img" class="large-text wpw-auto-poster-img-field">
										<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
										<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the Pinterest board.', 'wpwautoposter' ); ?></small></p><br>
										<p><small><strong><?php esc_html_e('Note : ', 'wpwautoposter'); ?> </strong><?php esc_html_e( 'You need to select atleast one image, otherwise pinterest posting will not work.', 'wpwautoposter' );?></small></p>
									</td>	
								</tr>

								<tr valign="top" class="global_msg_tr <?php echo esc_attr($global_msg_style); ?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[pin_custom_template]"><?php esc_html_e( 'Custom Message : ', 'wpwautoposter' ); ?></label>
									</th>
									<td class="form-table-td">
										<?php
										$pin_custom_template = isset( $wpw_auto_poster_options['pin_custom_template'] ) ? $wpw_auto_poster_options['pin_custom_template'] : ''; ?>
										<textarea name="wpw_auto_poster_options[pin_custom_template]" id="wpw_auto_poster_options[pin_custom_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $pin_custom_template ); ?></textarea>
									</td>
								</tr>

								
								<tr id="custom_post_type_templates_pin" class="post_msg_tr <?php echo esc_attr($post_msg_style); ?>">
									<th colspan="2" class="form-table-td">
									  	<ul>
									  		<?php
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();

											foreach( $all_types as $type ) {	
											
												if( !is_object( $type ) ) continue;

												if( isset( $type->labels ) ) {
													$label = $type->labels->name ? $type->labels->name : $type->name;
									            }
									            else {
									            	$label = $type->name;
									            }

												if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
												
											?>
										    <li><a href="#tabs-<?php echo esc_attr($type->name); ?>"><?php echo esc_html($label); ?></a></li>
									  		<?php } ?>

									  	</ul>
									  	<?php 
									  	foreach( $all_types as $type ) {	
										
											if( !is_object( $type ) ) continue;	

											if( isset( $type->labels ) ) {
												$label = $type->labels->name ? $type->labels->name : $type->name;
								            }
								            else {
								            	$label = $type->name;
								            }

											if( $label == 'Media' || $label == 'media' || $type->name == 'elementor_library' ) continue; // skip media
												
											$pin_custom_template = ( isset( $wpw_auto_poster_options['pin_custom_template_'.$type->name] ) ) ? $wpw_auto_poster_options['pin_custom_template_'.$type->name] : '';
												
											$pin_custom_img = ( isset( $wpw_auto_poster_options['pin_custom_img_'.$type->name] ) ) ? $wpw_auto_poster_options['pin_custom_img_'.$type->name] : ''; ?>
											
										  	<table id="tabs-<?php echo esc_attr($type->name); ?>">
												<tr valign="top">
													<th scope="row">
														<label for="wpw_auto_poster_options_pin_custom_img_<?php echo esc_attr($type->name); ?>"><?php esc_html_e( 'Post Image : ', 'wpwautoposter' ); ?></label>
													</th>
													<td>
														<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $pin_custom_img ); ?>" name="wpw_auto_poster_options[pin_custom_img_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_poster_options_pin_custom_img_<?php echo esc_attr($type->name); ?>" class="large-text wpw-auto-poster-img-field">
														<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php esc_html_e( 'Add Image','wpwautoposter' );?>" />
														<p><small><?php esc_html_e( 'Here you can upload a default image which will be used for the Pinterest board.', 'wpwautoposter' ); ?></small></p><br>
														<p><small><strong><?php esc_html_e('Note : ', 'wpwautoposter'); ?> </strong><?php esc_html_e( 'You need to select atleast one image, otherwise pinterest posting will not work.', 'wpwautoposter' );?></small></p>
													</td>	
												</tr>

												<tr valign="top">

													<th scope="row">
														<label for="wpw_auto_posting_pin_custom_msg_<?php echo esc_attr($type->name); ?>"><?php echo esc_html__('Custom Message ', 'wpwautoposter'); ?>: </label>
													</th>

													<td class="form-table-td">
														<textarea type="text" name="wpw_auto_poster_options[pin_custom_template_<?php echo esc_attr($type->name); ?>]" id="wpw_auto_posting_pin_custom_msg_<?php echo esc_attr($type->name); ?>" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $pin_custom_template ); ?></textarea>
													</td>	
												</tr>
											</table>
									<?php } ?>
									</th>
								</tr>	
								<tr valign="top">									
									<th scope="row"></th>
									<td class="global_msg_td">
										<p><?php esc_html_e( 'Here you can enter default notes which will be used for the pins. Leave it empty to use the post level notes. You can use following template tags within the notes template:', 'wpwautoposter' ); ?>
										<?php 
										$ins_template_str = '<div class="short-code-list">
										<div class="short-code"> 
											<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
											<code>{first_name}</code><span class="description">' . esc_html__('displays the first name.', 'wpwautoposter') .
										'</span></div>
										<div class="short-code">
											<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
											</div>
										<code>{last_name}</code><span class="description">' . esc_html__('displays the last name,', 'wpwautoposter') .
										'</span></div>
										<div class="short-code">
										<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
										</div>
										<code>{title}</code><span class="description">' . esc_html__('displays the default post title.', 'wpwautoposter') .
										'</span></div>
										<div class="short-code">
										<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
											</div>
										<code>{link}</code><span class="description">' . esc_html__('displays the default post link.', 'wpwautoposter') .
											'</span></div>
										<div class="short-code">
										<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
											</div>
										<code>{full_author}</code><span class="description">' . esc_html__('displays the full author name.', 'wpwautoposter') .
										'</span></div>
										<div class="short-code">
										<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
											</div>
										<code>{nickname_author}</code><span class="description">' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
										'</span></div>
										<div class="short-code">
										<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
											</div>
										<code>{post_type}</code><span class="description">' . esc_html__(' displays the post type.', 'wpwautoposter') .
										'</span></div>
										
										<div class="short-code">
											<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
										<code>{sitename}</code><span class="description">' . esc_html__('displays the name of your site.', 'wpwautoposter') .
											'</span></div>
										<div class="short-code">
											<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
											<code>{excerpt}</code><span class="description">' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
											'</span></div>
										<div class="short-code">
										<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
										<code>{hashtags}</code><span class="description">' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
											'</span></div>
										<div class="short-code">
										<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
										<code>{hashcats}</code><span class="description">' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
												'</span></div>
										<div class="short-code">
											<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
										<code>{content}</code><span class="description">' . esc_html__('displays the post content.', 'wpwautoposter') .
												'</span></div>
										<div class="short-code">
											<div class="link-icon">
												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
												<div class="wooslg-custom-tip">
													<span>Copy Tag</span>
												</div>
											</div>
											<code>{content-digits}</code><span class="description">' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
												) .
												'</span></div>
											<div class="short-code">
												<div class="link-icon">
													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
													<div class="wooslg-custom-tip">
														<span>Copy Tag</span>
													</div>
												</div>
											<code>{CF-CustomFieldName}</code></b><span class="description">' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "</span><b>", 
													"</div> 
													</div>"
									);
							            print $ins_template_str;
							            ?></p>
									</td>
								</tr>
								<?php
								echo apply_filters ( 
								 'wpweb_fb_settings_submit_button', 
								 '<tr valign="top">
										<td colspan="2">
											<input type="submit" value="' . esc_html__( 'Save Changes', 'wpwautoposter' ) . '" name="wpw_auto_poster_set_submit" class="button-primary">
										</td>
									</tr>'
								); ?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
							
			</div><!-- #autopost_pinterest -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #ps-poster-autopost-pinterest -->
<!-- end of the autopost to pinterest meta box -->