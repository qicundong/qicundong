<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

//Check Post status
$post_id = !empty($_GET['post']) ? stripslashes_deep($_GET['post']) : '';

// Facebook app version
$fb_app_version = ( !empty($wpw_auto_poster_options['fb_app_version']) ) ? $wpw_auto_poster_options['fb_app_version'] : '';

$facebook_auth_options = !empty($wpw_auto_poster_options['insta_fb_auth_options']) ? $wpw_auto_poster_options['insta_fb_auth_options'] : 'graph';

// Check Shedule general options
$schedule_option = !empty($wpw_auto_poster_options['schedule_wallpost_option']) ? $wpw_auto_poster_options['schedule_wallpost_option'] : '';

/**
 * Tab argument
 */
$instametatab = array(
    'class' => 'wpw_instagram', //unique class name of each tabs
    'title' => esc_html__('Instagram', 'wpwautoposter'), //  title of tab
    'active' => $defaulttabon //it will by default make tab active on page load
);

//when facebook is on then inactive other tab by default
$defaulttabon = false;

//initiate tabs in metabox
$poster_meta->addTabs($instametatab);

// Get stored fb app grant data
$wpw_auto_poster_fb_sess_data = get_option('wpw_auto_poster_insta_sess_data');

// Get all facebook account authenticated
$fb_users = wpw_auto_poster_get_insta_accounts('all_accounts');

//  code to remove user profile account for the facebook
if (!empty($fb_users)) {
    foreach ($fb_users as $fb_user_key => $fb_user) {
        $temp_check = explode('|', $fb_user_key);
        if (isset($temp_check[0]) && isset($temp_check[1]) && $temp_check[0] == $temp_check[1]) {
            unset($fb_users[$fb_user_key]);
        }
    }
}

// Check facebook application id and secret must entered in settings page or not
if ((WPW_AUTO_POSTER_FB_APP_ID == '' || WPW_AUTO_POSTER_FB_APP_SECRET == '') &&
    $facebook_auth_options == 'graph') {
    $poster_meta->addGrantPermission($prefix . 'fb_warning', array('desc' => esc_html__('Enter your Facebook APP ID / Secret within the Settings Page, otherwise the Facebook posting won\'t work.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_instagram'));
} elseif (empty($wpw_auto_poster_fb_sess_data)) { // Check facebook user id is set or not
    $poster_meta->addGrantPermission($prefix . 'fb_grant', array('desc' => esc_html__('Your App doesn\'t have enough permissions to publish on Facebook.', 'wpwautoposter'), 'url' => add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php')), 'urltext' => esc_html__('Go to the Settings Page', 'wpwautoposter'), 'tab' => 'wpw_instagram'));
}

//add label to show status
$poster_meta->addTweetStatus($prefix . 'insta_published_on_insta', array('name' => esc_html__('Status : ', 'wpwautoposter'), 'desc' => esc_html__('Status of Instagram wall post like published/unpublished/scheduled.', 'wpwautoposter'), 'tab' => 'wpw_instagram'));

$post_status = get_post_meta($post_id, $prefix . 'insta_published_on_insta', true);
$post_label = esc_html__('Publish Post On Instagram : ', 'wpwautoposter');
$post_desc = esc_html__('Publish this Post to Instagram Userwall.', 'wpwautoposter');

if ($post_status == 1 && empty($schedule_option)) {
    $post_label = esc_html__('Re-publish Post On Instagram : ', 'wpwautoposter');
    $post_desc = esc_html__('Re-publish this Post to Instagram Userwall.', 'wpwautoposter');
} elseif (($post_status == 2) || ($post_status == 1 && !empty($schedule_option))) {
    $post_label = esc_html__('Re-schedule Post On Instagram : ', 'wpwautoposter');
    $post_desc = esc_html__('Re-schedule this Post to Instagram Userwall.', 'wpwautoposter');
} elseif (empty($post_status) && !empty($schedule_option)) {
    $post_label = esc_html__('Schedule Post On Instagram : ', 'wpwautoposter');
    $post_desc = esc_html__('Schedule this Post to Instagram Userwall.', 'wpwautoposter');
}

$post_desc .= '<br>' . sprintf(esc_html__('If you have enabled %sEnable auto posting to Instagram%s in global settings then you do not need to check this box to publish/schedule the post. This setting is only for republishing Or rescheduling post to Instagram.', 'wpwautoposter'), '<strong>', '</strong>');

$post_desc .= '<br><div class="wpw-auto-poster-error"><strong>' . esc_html__('Note : ', 'wpwautoposter') . '</strong> ' . sprintf(esc_html__('This setting is just an event to republish/reschedule the content, It will not save any value to %sdatabase%s.', 'wpwautoposter'), '<strong>', '</strong>') . '</div>';

//post to Instagram
$poster_meta->addPublishBox($prefix . 'post_to_instagram', array('name' => $post_label, 'desc' => $post_desc, 'tab' => 'wpw_instagram'));

//Immediate post to facebook
if (!empty($schedule_option)) {
    $poster_meta->addPublishBox($prefix . 'immediate_post_to_instagram', array('name' => esc_html__('Immediate Posting On Instagram:', 'wpwautoposter'), 'desc' => 'Immediately publish this post to Instagram Userwall.', 'tab' => 'wpw_instagram'));
}

// $fb_template_str = '<div class="short-code-list">
// 										<div class="short-code"> 
// 											<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 											<code>{first_name}</code><span class="description">' . esc_html__('displays the first name.', 'wpwautoposter') .
// 										'</span></div>
// 										<div class="short-code">
// 											<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 											</div>
// 										<code>{last_name}</code><span class="description">' . esc_html__('displays the last name,', 'wpwautoposter') .
// 										'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 										</div>
// 										<code>{title}</code><span class="description">' . esc_html__('displays the default post title.', 'wpwautoposter') .
// 										'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 											</div>
// 										<code>{link}</code><span class="description">' . esc_html__('displays the default post link.', 'wpwautoposter') .
// 											'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 											</div>
// 										<code>{full_author}</code><span class="description">' . esc_html__('displays the full author name.', 'wpwautoposter') .
// 										'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 											</div>
// 										<code>{nickname_author}</code><span class="description">' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
// 										'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 											</div>
// 										<code>{post_type}</code><span class="description">' . esc_html__(' displays the post type.', 'wpwautoposter') .
// 										'</span></div>
										
// 										<div class="short-code">
// 											<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 										<code>{sitename}</code><span class="description">' . esc_html__('displays the name of your site.', 'wpwautoposter') .
// 											'</span></div>
// 										<div class="short-code">
// 											<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 											<code>{excerpt}</code><span class="description">' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
// 											'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 										<code>{hashtags}</code><span class="description">' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
// 											'</span></div>
// 										<div class="short-code">
// 										<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 										<code>{hashcats}</code><span class="description">' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
// 												'</span></div>
// 										<div class="short-code">
// 											<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 										<code>{content}</code><span class="description">' . esc_html__('displays the post content.', 'wpwautoposter') .
// 												'</span></div>
// 										<div class="short-code">
// 											<div class="link-icon">
// 												<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 												<div class="wooslg-custom-tip">
// 													<span>Copy Tag</span>
// 												</div>
// 											</div>
// 											<code>{content-digits}</code><span class="description">' . sprintf(esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'), "<b>", "</b>"
// 												) .
// 												'</span></div>
// 											<div class="short-code">
// 												<div class="link-icon">
// 													<img src="'. WPW_AUTO_POSTER_IMG_URL .'/link-icon.svg" alt="" />
// 													<div class="wooslg-custom-tip">
// 														<span>Copy Tag</span>
// 													</div>
// 												</div>
// 											<code>{CF-CustomFieldName}</code></b><span class="description">' . sprintf(esc_html__('inserts the contents of the custom field with the specified name. %s E.g. If your price is stored in the custom field "PRDPRICE" you will need to use {CF-PRDPRICE} tag.%s', 'wpwautoposter'), "</span><b>", 
// 													"</div> 
// 													</div>"
// 									);

$fb_template_str = '<br /><b><code>{first_name}</code></b> - ' . esc_html__('displays the first name.', 'wpwautoposter') .
    '<br /><b><code>{last_name}</code></b> - ' . esc_html__('displays the last name.', 'wpwautoposter') .
    '<br /><b><code>{title}</code></b> - ' . esc_html__('displays the default post title.', 'wpwautoposter') .
    '<br /><b><code>{link}</code></b> - ' . esc_html__('displays the default post link.', 'wpwautoposter') .
    '<br /><b><code>{full_author}</code></b> - ' . esc_html__('displays the full author name.', 'wpwautoposter') .
    '<br /><b><code>{nickname_author}</code></b> - ' . esc_html__('displays the nickname of author.', 'wpwautoposter') .
    '<br /><b><code>{post_type}</code></b> - ' . esc_html__('displays the post type.', 'wpwautoposter') .
    '<br /><b><code>{sitename}</code></b> - ' . esc_html__('displays the name of your site.', 'wpwautoposter') .
    '<br /><b><code>{excerpt}</code></b> - ' . esc_html__('displays the post excerpt.', 'wpwautoposter') .
    '<br /><b><code>{hashtags}</code></b> - ' . esc_html__('displays the post tags as hashtags.', 'wpwautoposter') .
    '<br /><b><code>{hashcats}</code></b> - ' . esc_html__('displays the post categories as hashtags.', 'wpwautoposter') .
    '<br /><b><code>{content}</code></b> - ' . esc_html__('displays the post content.', 'wpwautoposter') .
    '<br /><b><code>{content-digits}</code></b> - ' . sprintf(
        esc_html__('displays the post content with define number of digits in template tag. %s E.g. If you add template like {content-100} then it will display first 100 characters from post content. %s', 'wpwautoposter'),
        "<b>",
        "</b>"
	);

//publish with diffrent post title
$poster_meta->addTextarea($prefix . 'insta_custom_title', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Message : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom message which will be used for the wall post. Leave it empty to use the global custom message. If the global custom message will be blank then it will use the post title. You can use following template tags within the message:', 'wpwautoposter') .
$fb_template_str, 'tab' => 'wpw_instagram', 'rows' => 3 ));

do_action('wpw_auto_poster_after_custom_message_field_instagram', $poster_meta, $post_id);

//post to this account
$poster_meta->addSelect($prefix . 'insta_user_id', $fb_users, array('name' => esc_html__('Post To This Instagram Account', 'wpwautoposter') . '(' . esc_html__('s', 'wpwautoposter') . ') : ', 'std' => array(''), 'desc' => esc_html__('Select an account to which you want to post. This setting overrides the global and category settings. Leave it  empty to use the global/category defaults.', 'wpwautoposter'), 'multiple' => true, 'placeholder' => esc_html__('Default', 'wpwautoposter'), 'tab' => 'wpw_instagram'));

$wall_post_methods = array(
    '' => esc_html__('Default', 'wpwautoposter'),
    'image_posting' => esc_html__('As a Image Post', 'wpwautoposter'),           
    'reel_posting' => esc_html__('As a Reel Post', 'wpwautoposter'),         
);

//post on wall as a type
$poster_meta->addSelect( $prefix . 'insta_posting_method', $wall_post_methods, array('name' => esc_html__('Post As : ', 'wpwautoposter'), 'std' => array(''), 'desc' => esc_html__('Select a Instagram post type. Leave it empty to use the default one from the settings page.', 'wpwautoposter'), 'tab' => 'wpw_instagram') );

//Images
$poster_meta->addGallery( $prefix . 'instagram_post_gallery', array('name' => esc_html__('Image(s) to use : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload multiple images which will be used for the Instagram image posting. Leave it empty to use the featured image.', 'wpwautoposter') , 'tab' => 'wpw_instagram', 'show_path' => true) );

$sharepost_desc = '<br><div class="wpw-auto-poster-error"><strong>' . esc_html__('Note : ', 'wpwautoposter') . '</strong> ' . sprintf(esc_html__('If you are posting a reel, please ensure that the video format is either MOV or MP4 (MPEG-4 Part 14). The video duration must be between 3 seconds and 15 minutes.', 'wpwautoposter'), '<strong>', '</strong>') . '</div>';

//$sharepost_desc = '';

$poster_meta->addVideo( $prefix . 'insta_post_reel', array('name' => esc_html__('Post Reel : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a video which will be used for the instagram reel. ', 'wpwautoposter').$sharepost_desc, 'tab' => 'wpw_instagram', 'show_path' => true, 'class' => $prefix . 'validate-video') );

// Display custom image if fb version below 2.9
if ($fb_app_version < 209) {
    //post image url
    $poster_meta->addImage($prefix . 'insta_post_image', array('name' => esc_html__('Post Image : ', 'wpwautoposter'), 'desc' => esc_html__('Here you can upload a default image which will be used for the Instagram wall post. Leave it empty to use the featured image. if featured image is also blank, then it will take default image from the settings page.', 'wpwautoposter') . '<br><br><strong>' . esc_html__('Note : ', 'wpwautoposter') . ' </strong>' . esc_html__('This option only work if your facebook app version is below 2.9. If you\'re using latest facebook app, it wont work.', 'wpwautoposter') . ' <a href="' . esc_url('https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/') . '" target="_blank">' . esc_html__('Learn More.', 'wpwautoposter') . '</a>', 'tab' => 'wpw_instagram', 'show_path' => true));
}



// Display custom post link and description if fb version below 2.11
if ($fb_app_version < 209) {
    //custom link to post to facebook
    $poster_meta->addText($prefix . 'insta_custom_post_link', array('validate_func' => 'escape_html', 'name' => esc_html__('Custom Link:', 'wpwautoposter'), 'desc' => esc_html__('Here you can enter a custom link which will be used for  the wall post. Leave it empty to use the link of the current post. The link must start with', 'wpwautoposter') . ' http://', 'tab' => 'wpw_instagram'));
}
