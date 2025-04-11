<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Twitter Posting Class
 *
 * Handles all the functions to tweet on twitter
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

require_once __DIR__ . '/libraries/twitter/vendor/autoload.php';
use Noweh\TwitterApi\Client;

class Wpw_Auto_Poster_TW_Posting {

	public $twitter, $model, $logs;
	
	public function __construct() {
	
		global $wpw_auto_poster_model, $wpw_auto_poster_logs;
		
		$this->model = $wpw_auto_poster_model;
		$this->logs	 = $wpw_auto_poster_logs;
	}
	
	/**
	 * Include Twitter Class
	 * 
	 * Handles to load twitter class
	 * 
	 * @package Social Auto Poster
 	 * @since 1.0.0
	 */
	public function wpw_auto_poster_load_twitter(  $twitter_consumer_key, $twitter_consumer_secret, $twitter_oauth_token, $twitter_oauth_secret ) {
		
		global $wpw_auto_poster_options;
		
		//check twitter application id and application secret is not empty or not
		if( !empty( $twitter_consumer_key ) && !empty( $twitter_consumer_secret )
			&& !empty( $twitter_oauth_token ) && !empty( $twitter_oauth_secret ) ) {

			$settings = [
				'account_id' => '',
				'access_token' => $twitter_oauth_token,
				'access_token_secret' => $twitter_oauth_secret,
				'consumer_key' => $twitter_consumer_key,
				'consumer_secret' => $twitter_consumer_secret,
				'bearer_token' => '',
				//'free_mode' => true, // Optional
				//'api_base_uri' => 'https://api.twitter.com/2/', // Optional
			]; 

			
			
			$client = new Client($settings);
			
			return $client;

		} else {
			return false;
		}
	}

	/**
	 * Post To Twitter
	 * 
	 * Handles to Post on Twitter account
	 * 
	 * @package Social Auto Poster
	 * @since 1.0.0
	 */
	public function wpw_auto_poster_post_to_twitter( $post, $auto_posting_type ) {
		
		global $wpw_auto_poster_options, $wpw_auto_poster_reposter_options;
		
		//posting logs data
		$posting_logs_data = array();
		
		//metabox field prefix
		$prefix = WPW_AUTO_POSTER_META_PREFIX;
		
		$post_type 	= $post->post_type; //post type
		
		//get tweet template from post meta
		$tw_user_ids = get_post_meta( $post->ID, $prefix . 'tw_user_id' );

		if( $post_type == 'wpwsapquickshare'){
            $tw_user_ids = get_post_meta($post->ID, $prefix . 'tw_user_id',true);
        }

		/******* Code to posting to selected category Twitter account ******/

		// get all categories for custom post type
        $categories = wpw_auto_poster_get_post_categories_by_ID( $post_type, $post->ID );

		// Get all selected account list from category
		$category_selected_social_acct = get_option( 'wpw_auto_poster_category_posting_acct');
		// IF category selected and category social account data found
		if( !empty( $categories ) && !empty( $category_selected_social_acct ) && empty( $tw_user_ids ) ) {
			$tw_clear_cnt = true;

			// GET FB user account ids from post selected categories
			foreach ( $categories as $key => $term_id ) {
				
				$cat_id = $term_id;
				// Get TW user account ids form selected category  
				if( isset( $category_selected_social_acct[$cat_id]['tw'] ) && !empty( $category_selected_social_acct[$cat_id]['tw'] ) ) {
					// clear TW user data once
					if( $tw_clear_cnt)
						$tw_user_ids = array();
					$tw_user_ids = array_merge($tw_user_ids, $category_selected_social_acct[$cat_id]['tw'] );
					$tw_clear_cnt = false;
				}
			}
			if( !empty( $tw_user_ids ) ) {
				$tw_user_ids = array_unique($tw_user_ids);
			}
		}

		//check twitter user ids are empty in metabox and set in settings page
		if( empty( $tw_user_ids ) 
			&& isset( $wpw_auto_poster_options[ 'tw_type_'.$post_type.'_user' ] ) 
			&& !empty( $wpw_auto_poster_options[ 'tw_type_'.$post_type.'_user' ] ) ) {
			//users from settings
			$tw_user_ids = $wpw_auto_poster_options[ 'tw_type_'.$post_type.'_user' ];
		} //end if
		
		//check twitter user ids are empty selected for posting
		if( empty( $tw_user_ids ) ) {
			
			//record logs for twitter users are not selected
			$this->logs->wpw_auto_poster_add( 'Twitter error: user not selected for posting.' );
			if( $post_type == 'wpwsapquickshare'){
	            update_post_meta($post->ID, $prefix . 'tw_post_status','error');
	            update_post_meta($post->ID, $prefix . 'tw_error', esc_html__('User not selected for posting.', 'wpwautoposter' ) );
	        }
			sap_add_notice( esc_html__('Twitter: You have not selected any user for the posting.', 'wpwautoposter' ), 'error');
			//return false
			return false;
			
		} //end if to check user ids are empty
		
		//convert user ids to single array
		$post_to_users 	= ( array ) $tw_user_ids;
		
		//Twitter Consumer Key and Secret
		$twitter_keys = isset( $wpw_auto_poster_options['twitter_keys'] ) ? $wpw_auto_poster_options['twitter_keys'] : array();
		$disable_image_tweet = !empty( $wpw_auto_poster_options['tw_disable_image_tweet'] ) ? $wpw_auto_poster_options['tw_disable_image_tweet'] : '';
		
		//initial value of posting flag
		$postflg = false;
		
		if( !empty( $post_to_users ) ) { // Check all user ids
			foreach ( $post_to_users as $tw_user_key => $tw_user_value ) {

				// array start from zero while users stored as 1,2,3 so did -1 logic here
				$tw_key = $tw_user_value - 1;

				$tw_consumer_key 		= isset( $twitter_keys[$tw_key]['consumer_key'] ) ? $twitter_keys[$tw_key]['consumer_key'] : '';
				$tw_consumer_secret 	= isset( $twitter_keys[$tw_key]['consumer_secret'] ) ? $twitter_keys[$tw_key]['consumer_secret'] : '';
				$tw_auth_token 			= isset( $twitter_keys[$tw_key]['oauth_token'] ) ? $twitter_keys[$tw_key]['oauth_token'] : '';
				$tw_auth_token_secret 	= isset( $twitter_keys[$tw_key]['oauth_secret'] ) ? $twitter_keys[$tw_key]['oauth_secret'] : '';
				
				//load twitter class
				$twitter = $this->wpw_auto_poster_load_twitter( $tw_consumer_key, $tw_consumer_secret, $tw_auth_token, $tw_auth_token_secret );
				
				//check twitter class is loaded or not
				if( !$twitter ) return false;
				
				//record logs for twitter posting
				$this->logs->wpw_auto_poster_add( 'Twitter posting to user account begins.' );
				
				//get tweet template from post meta
				$status = get_post_meta( $post->ID, $prefix . 'tw_template', true );
				
				$status = apply_filters( 'wpw_post_meta_tw_template', $status, $post->ID );
				
				//check tweet template is empty in post meta
				if( empty( $status ) || ( !empty( $status ) && $auto_posting_type == 'reposter' ) ) {

					if( !empty( $auto_posting_type ) && $auto_posting_type == 'reposter' ) {

			            $wpw_auto_poster_reposter_options["repost_tw_global_message_template_".$post_type] = ( isset( $wpw_auto_poster_reposter_options["repost_tw_global_message_template_".$post_type] ) ) ? $wpw_auto_poster_reposter_options["repost_tw_global_message_template_".$post_type] : '';

			            $repost_tw_global_message_template = ( isset( $wpw_auto_poster_reposter_options["repost_tw_global_message_template"] ) ) ? $wpw_auto_poster_reposter_options["repost_tw_global_message_template"] : '';

			            $repost_tw_custom_msg_options = isset( $wpw_auto_poster_reposter_options['repost_tw_custom_msg_options'] ) ? $wpw_auto_poster_reposter_options['repost_tw_custom_msg_options'] : '';

			            if( $repost_tw_custom_msg_options == 'post_msg' && !empty( $wpw_auto_poster_reposter_options["repost_tw_global_message_template_".$post_type] ) ) {

			                $status = $wpw_auto_poster_reposter_options["repost_tw_global_message_template_".$post_type];
			            }
			            elseif( !empty( $repost_tw_global_message_template ) ) {

							$status = $repost_tw_global_message_template;
						}
			            else {

							$status = '[title] - [link]';
						}
					}
					else {

						$status = $this->model->wpw_auto_poster_get_tweet_template( $wpw_auto_poster_options['tw_tweet_template'], $post_type );
					}

				} //end if 


				//replace tweet status with template
				$status = $this->model->wpw_auto_poster_tweet_status ( $post, $status );
				
				//use content with short description
				$tweetdesc = $this->model->wpw_auto_poster_excerpt( $status );
				
				/**************
				 * Image Priority
				 * If metabox image set then take from metabox
				 * If metabox image is not set then take from featured image
				 * If featured image is not set then take from settings page
				 **************/
				
				//get custom image from post / page / custom post type
				$wpw_auto_poster_custom_img = get_post_meta( $post->ID, $prefix . 'tw_image', true );
				
				//get featured image from post / page / custom post type
				$post_featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				
				//check custom image is set in meta and not empty
				if( isset( $wpw_auto_poster_custom_img['src'] ) && !empty( $wpw_auto_poster_custom_img['src'] ) ) {
					$post_img = $wpw_auto_poster_custom_img['src'];
				} elseif ( isset( $post_featured_img[0] ) && !empty( $post_featured_img[0] ) ) {
					//check post featrued image is set the use that image
					$post_img = $post_featured_img[0];
				} else {
					//else get post image from settings page

		            $tw_custom_msg_options = isset( $wpw_auto_poster_options['tw_custom_msg_options'] ) ? $wpw_auto_poster_options['tw_custom_msg_options'] : '';
		            
		            // get individual post type post image from settings page
		            $tw_custom_post_img = ( isset( $wpw_auto_poster_options["tw_tweet_img_".$post_type] ) ) ? $wpw_auto_poster_options["tw_tweet_img_".$post_type] : '';

		            $post_img = ( $tw_custom_msg_options == 'post_msg' && !empty( $tw_custom_post_img ) ) ? $tw_custom_post_img : $wpw_auto_poster_options['tw_tweet_img'];
				}

				$post_img = apply_filters('wpw_auto_poster_social_media_posting_image', $post_img );
				
				//record logs for twitter data
				$this->logs->wpw_auto_poster_add( 'Twitter post data : ' . $tweetdesc );
				
				//posting logs data
				if( !empty( $post_img ) ) {
					$posting_logs_data = array(	
												'status' => $tweetdesc,
												'image'  => $post_img
											);
				} else {
					$posting_logs_data = array(	
												'status' => $tweetdesc
											);
				}

				try {
					
					//do posting to twitter
					if( !empty( $post_img ) && ! $disable_image_tweet ) {
						
						$file_data = base64_encode(file_get_contents($post_img));
						$media_info = $twitter->uploadMedia()->upload($file_data);
					    
					    // check if media upload function successfully run
					    //if( $upload->httpstatus == 200 ){
					    if( isset($media_info) && isset($media_info['media_id_string']) && !empty($media_info['media_id_string']) ){

					    	//upload the file to your twitter account
					    	$media_ids = $media_info['media_id_string'];

					    	$params = array(
							  'text' => $tweetdesc,
							  'media' => array( // modifiy code to fix issue with new API since 2023
							  	'media_ids' => (array)$media_ids
							  )
							);
					    } else {
					    	$params = array(
							  'text' => $tweetdesc
							);
					    }
					
					} else {
						$params = array(
						  'text' => $tweetdesc
						);
					}
					
					$result = $twitter->tweet()->create()
							->performRequest($params);	

					//check id is set in result data and not empty
					if( ( isset( $result->id ) && !empty( $result->id ) ) || isset( $result->data->id ) && !empty( $result->data->id ) ) { // modifiy code to fix issue with new API since 2023
						
						//User details
						$posting_logs_user_details = array(
							'account_id' 				=> isset( $result->user->id ) ? $result->user->id : '',
							'display_name'				=> isset( $result->user->name ) ? $result->user->name : '',
							'user_name'					=> isset( $result->user->screen_name ) ? $result->user->screen_name : '',
							'twitter_consumer_key' 		=> $tw_consumer_key,
							'twitter_consumer_secret'	=> $tw_consumer_secret,
							'twitter_oauth_token'		=> $tw_auth_token,
							'twitter_oauth_secret'		=> $tw_auth_token_secret,
						);
						
						//posting logs store into database
						$this->model->wpw_auto_poster_insert_posting_log( $post->ID, 'tw', $posting_logs_data, $posting_logs_user_details );
						
						if( $post_type == 'wpwsapquickshare'){
				            update_post_meta($post->ID, $prefix . 'tw_post_status','success');
				        }

						//record logs for post posted to twitter
						$this->logs->wpw_auto_poster_add( 'Twitter posted to user account : Response ID ' . $result->id );
						
						//posting flag that posting successfully
						$postflg = true;
						
					}
					
					//check error is set
					if( isset( $result->errors ) && !empty( $result->errors ) ) {
						//record logs for twitter posting exception
						$this->logs->wpw_auto_poster_add( 'Twitter error: ' . $result->errors[0]->code . ' | ' .$result->errors[0]->message );
						if( $post_type == 'wpwsapquickshare'){
				            update_post_meta($post->ID, $prefix . 'tw_post_status','error');
				            update_post_meta($post->ID, $prefix . 'tw_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $result->errors[0]->message ) );
				        }
						sap_add_notice( sprintf( esc_html__('Twitter: Error while posting %s', 'wpwautoposter' ), $result->errors[0]->message ), 'error');
					} else if( isset( $result->error ) && !empty( $result->error ) ) {
						//record logs for twitter posting exception
						$this->logs->wpw_auto_poster_add( 'Twitter error: ' . $result->httpstatus . ' | ' .$result->error );
						if( $post_type == 'wpwsapquickshare'){
							update_post_meta($post->ID, $prefix . 'tw_post_status','error');
							update_post_meta($post->ID, $prefix . 'tw_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $result->error ) );
						}
						sap_add_notice( sprintf( esc_html__('Twitter: Error while posting %s', 'wpwautoposter' ), $result->error ), 'error');
					} else if( isset( $result->httpstatus ) &&  $result->httpstatus == 403  ) {
						//record logs for twitter posting exception
						$this->logs->wpw_auto_poster_add( 'Twitter error: ' . $result->httpstatus . ' | ' .$result->detail );
						if( $post_type == 'wpwsapquickshare'){
							update_post_meta($post->ID, $prefix . 'tw_post_status','error');
							update_post_meta($post->ID, $prefix . 'tw_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $result->detail ) );
						}
						sap_add_notice( sprintf( esc_html__('Twitter: Error while posting %s', 'wpwautoposter' ), $result->detail ), 'error');
					}
					
					//return $result;
					
				} catch ( Exception $e ) {
					//record logs exception generated
					$this->logs->wpw_auto_poster_add( 'Twitter error: ' . $e->__toString() );
					if( $post_type == 'wpwsapquickshare'){
			            update_post_meta($post->ID, $prefix . 'tw_post_status','error');
			            update_post_meta($post->ID, $prefix . 'tw_error', sprintf( esc_html__('Error while posting %s', 'wpwautoposter' ), $e->__toString() ) );
			        }
					sap_add_notice( sprintf( esc_html__('Twitter: Something was wrong while posting %s', 'wpwautoposter' ), $e->__toString() ), 'error');
					$postflg = false;
					//return false;
				}
			}
		}
		//returning post flag
		return $postflg;
	}
	
	/**
	 * Get Twitter User Data
	 * 
	 * Handles to get twitter user data
	 * 
	 * @package Social Auto Poster
	 * @since 1.4.0
	 */
	public function wpw_auto_poster_get_user_data( $twitter_consumer_key, $twitter_consumer_secret, $twitter_oauth_token, $twitter_oauth_secret ) {
		
		//load twitter class
		$twitter = $this->wpw_auto_poster_load_twitter( $twitter_consumer_key, $twitter_consumer_secret, $twitter_oauth_token, $twitter_oauth_secret );
		
		//check twitter class is loaded or not
		if( !$twitter ) return false;
		
		try {
			$response = $twitter->userMeLookup()->performRequest();
			
			
		} catch (Exception $e) {
			// Log the error
			$errors = $e->getMessage();
			
			if( isset($errors) ){
				if( isset($errors->status) && isset($errors->detail)){
					sap_add_notice( "Twitter: ".$errors->detail, 'error');
				}
			}else{
				sap_add_notice( esc_html__('Twitter: Something was wrong while fatching data', 'wpwautoposter' ) , 'error');
			}
		
		}
		if($response && isset($response->data) && isset($response->data->id)){
			return $response->data;
		}
		
		return false;
	}
	
	/**
	 * Twitter Posting
	 * 
	 * Handles to twitter posting
	 * by post data
	 * 
	 * @package Social Auto Poster
 	 * @since 1.5.0
	 */
	public function wpw_auto_poster_tw_posting( $post, $auto_posting_type = '' ) {
		
		global $wpw_auto_poster_options;
		
		$prefix = WPW_AUTO_POSTER_META_PREFIX;
		
	 	
		$res = $this->wpw_auto_poster_post_to_twitter( $post, $auto_posting_type );
		
		if( !empty( $res ) ) { //check post has been posted on twitter or not
			
			//record logs for posting done on twitter
			$this->logs->wpw_auto_poster_add( 'Twitter posting completed successfully.' );
			
			update_post_meta( $post->ID, $prefix . 'tw_status', '1' );

			// get current timestamp and update meta as published date/time
            $current_timestamp = current_time( 'timestamp' );
            update_post_meta($post->ID, $prefix . 'published_date', $current_timestamp);
            
			return true;
		}
		return false;
	}
}