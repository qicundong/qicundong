<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Manage posts schedules
 *
 * The html markup for the post schedules list
 *
 * @package Social Auto Poster
 * @since 1.4.0
 */

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Wpw_Auto_Poster_Manage_schedules_List extends WP_List_Table {

	var $model, $render, $per_page;

	function __construct(){

		global $wpw_auto_poster_model, $wpw_auto_poster_render;

		$this->model = $wpw_auto_poster_model;
		$this->render = $wpw_auto_poster_render;

        //Set parent defaults
		parent::__construct( array(
			'singular'	=> 'schedule',	//singular name of the listed records
			'plural'	=> 'schedules',	//plural name of the listed records
			'ajax'		=> false		//does this table support ajax?
		) );

		$this->per_page	= apply_filters( 'wpw_auto_poster_manage_schedules_per_page', 10 ); // Per page
	}

    /**
	 * Displaying Scheduling posts
	 *
	 * Does prepare the data for displaying Scheduling posts in the table.
	 *
	 * @package Social Auto Poster
	 * @since 1.4.0
	 */
	function display_scheduling_posts() {

		$prefix = WPW_AUTO_POSTER_META_PREFIX;

		global $wpw_auto_poster_options;

		//if search is call then pass searching value to function for displaying searching values
		$args = array();

		//Get selected tab
		$selected_tab	= !empty( $_GET['tab'] ) ? stripslashes_deep($_GET['tab']) : 'facebook';		
		
		//Get social meta key
		$status_meta_key = $this->model->wpw_auto_poster_get_social_status_meta_key( $selected_tab );

		
		 
		// Taking parameter
		$orderby 	= isset( $_GET['orderby'] )	? urldecode( stripslashes_deep($_GET['orderby']) )		: 'ID';
		$order		= isset( $_GET['order'] )	? stripslashes_deep($_GET['order']) : 'DESC';
		$search 	= isset( $_GET['s'] ) 		? sanitize_text_field( trim($_GET['s']) )	: null;

		//Arguments
		$args = array(
			'posts_per_page'		=> $this->per_page,
			'post_status'			=> array( 'publish','future'),
			'page'					=> isset( $_GET['paged'] ) ? stripslashes_deep($_GET['paged']) : null,
			'orderby'				=> $orderby,
			'order'					=> $order,
			'offset'  				=> ( $this->get_pagenum() - 1 ) * $this->per_page,
			'wpw_auto_poster_list'	=> true,
			'meta_query'			=> array(
					'relation' => 'OR',
					array(
							'key' 	  => $status_meta_key,
							'compare' => 'NOT EXISTS',
						),
					array(
							'key' 	=> $status_meta_key,
							'value' => '',
						)
				)
		);

		//searched by search
		if( !empty( $search ) ) {
			$args['s']	= $search;
		}

		//Filter by post name
		if(isset($_REQUEST['wpw_auto_poster_post_type']) && !empty($_REQUEST['wpw_auto_poster_post_type'])) {
			$args['post_type']	= sanitize_text_field(esc_html($_REQUEST['wpw_auto_poster_post_type']));
		}

		// Filter based on post category
		if(isset($_REQUEST['wpw_auto_poster_cat_id']) && !empty($_REQUEST['wpw_auto_poster_cat_id'])) {
			$term_id = sanitize_text_field(esc_html($_REQUEST['wpw_auto_poster_cat_id']));
			$term = get_term($term_id);
			if(!empty($term)){

				$args['tax_query'] = array(
					array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						)
				);
			}
		}

		//Filter by status
		if(isset($_REQUEST['wpw_auto_poster_social_status']) && !empty($_REQUEST['wpw_auto_poster_social_status'])) {
			$args['meta_query']	= array(
				array(
						'key' 	=> $status_meta_key,
						'value' => sanitize_text_field(esc_html($_REQUEST['wpw_auto_poster_social_status'])),
					)
			);
		} elseif(!isset($_REQUEST['wpw_auto_poster_social_status'])) {
			$args['meta_query']	= array(
					array(
							'key' 	=> $status_meta_key,
							'value' => '2',
						)
				);
		}
		

		//Filter by date

		/** Get selected post status **/
		$selected_post_status = !empty( $_REQUEST['wpw_auto_poster_social_status'] ) ? sanitize_text_field(esc_html($_REQUEST['wpw_auto_poster_social_status'])) : '2';

		/** Check Start date **/
		if( !empty($_REQUEST['wpw_auto_start_date']) ) {
			
			$start_date = strtotime( sanitize_text_field(esc_html($_REQUEST['wpw_auto_start_date'])) );
			
		}

		/** Check End date **/
		if(!empty($_REQUEST['wpw_auto_end_date']) ) {
			
			$end_date = strtotime( sanitize_text_field($_REQUEST['wpw_auto_end_date']) );
		}

		// Check if post status selected is "Scheduled" and Hourly scheduling is set
		if ( $selected_post_status == '2' ) {
			
			if( !empty($wpw_auto_poster_options) && $wpw_auto_poster_options['schedule_wallpost_option'] == "hourly") { 

				/** If start date is set add to meta query **/
				if ( !empty( $start_date ) ) {
					
					$args['meta_query'][] = array(
			        		'key'			=> '_wpweb_select_hour',
			        		'compare'		=> '>=',
			        		'value'			=> $start_date,
			    		);
				}

				/** If end date is set add to meta query **/
				if ( !empty( $end_date ) ) {
					
					$args['meta_query'][] = array(
		        		'key'			=> '_wpweb_select_hour',
		        		'compare'		=> '<=',
		        		'value'			=> $end_date,
		    		);
				}
			}
		
		} elseif ( $selected_post_status == '1' ) { // Check if status selected as "Published"

			/** If start date is set add to meta query **/
			if ( !empty( $start_date ) ) {
				
				$args['meta_query'][] = array(
		        		'key'			=> '_wpweb_published_date',
		        		'compare'		=> '>=',
		        		'value'			=> $start_date,
		    		);
			}

			/** If end date is set add to meta query **/
			if ( !empty( $end_date ) ) {
				
				$args['meta_query'][] = array(
	        		'key'			=> '_wpweb_published_date',
	        		'compare'		=> '<=',
	        		'value'			=> $end_date,
	    		);
			}
		} 



		//Get social scheduling list data from database
		$results = $this->model->wpw_auto_poster_get_scheduling_data( $args );
		$data = isset( $results['data'] ) ? $results['data'] : '';
		$total	= isset( $results['total'] ) ? $results['total'] : 0;


		// Check if post status selected is "Scheduled"
		if ( $selected_post_status == '2' ) {

			// Filter result data if anything other than hourly posting is selected
			if ( $wpw_auto_poster_options['schedule_wallpost_option'] != "hourly" && ( !empty($start_date) || !empty($end_date) ) ) {

				
				$filtered_data = array();

				$cron_scheduled = wp_next_scheduled('wpw_auto_poster_scheduled_cron');

				foreach ( $results['data'] as $data_key => $data_value ){

					if ( ($cron_scheduled >= $start_date) && ($cron_scheduled <= $end_date)) {

						$filtered_data[$data_key] = $data_value;
					} 
				}

				$data	= isset( $filtered_data ) ? $filtered_data : '';
				$total	= ( isset( $data ) && !empty( $data ) ) ? $total : 0;		
			}		
		}

		if( !empty( $data ) ) {

			foreach ($data as $key => $value){
	
    
				// Declare variable
				$category_list = '';

				//Get Author name, Author profile url
				$author_name	=	get_the_author_meta( 'display_name', $value['post_author'] );
				$author_url		=	get_edit_user_link( $value['post_author'] );

				//Get post title
				$edit_link	= get_edit_post_link( $value[ 'ID' ] );

				//Get social status
				$status	= get_post_meta( $value['ID'], $status_meta_key, true );
				$social_status 	= esc_html__( 'Unpublished','wpwautoposter' );
				if( $status == 1 ) {
					$social_status 	= esc_html__( 'Published','wpwautoposter' );
				} elseif ( $status == 2 ) {
					$social_status 	= esc_html__( 'Scheduled','wpwautoposter' );
				}

				if( strlen( wp_strip_all_tags($value['post_title']) ) > 250 ){
					$listing_content = substr(esc_html($value['post_title']), 0, 250) . '...';
				}else{
					$listing_content = esc_html($value['post_title']);
				}

				$data[$key]['post_title'] 	= '<a target="_blank" href="'.esc_url($edit_link).'">' . $listing_content . '</a>';
				$data[$key]['post_type'] 	= $value['post_type'];
				$data[$key]['social_status']= $social_status;

				// Check if post status is published
				if( $status == 1 ) {
					
					$post_select_hour = get_post_meta($value['ID'], $prefix . 'published_date', true);
					if ( !empty( $post_select_hour ) ) {

						$post_select_hour = date( 'Y-m-d H:i', $post_select_hour);
					} else {
						$post_select_hour = '';
					}	
					
					$data[$key]['post_date'] = $post_select_hour;

				} elseif ( $status == 2 ) { // Check if post status is scheduled
					
					// If hourly scheduling option is set than show date as meta field value
					if(!empty($wpw_auto_poster_options) && $wpw_auto_poster_options['schedule_wallpost_option'] == "hourly" ){
						$post_select_hour = get_post_meta($value['ID'], $prefix . 'select_hour', true);
						if ( !empty( $post_select_hour ) ) {

							$post_select_hour = date( 'Y-m-d H:i', $post_select_hour);
						} else {
							$next_cron = wp_next_scheduled('wpw_auto_poster_scheduled_cron');
							$post_select_hour = get_date_from_gmt( date('Y-m-d H:i:s', $next_cron) );
						}	
						
						$data[$key]['post_date'] = $post_select_hour;

					} else { // Get the next cron scheduled if options other than hourly is set
						$next_cron = wp_next_scheduled('wpw_auto_poster_scheduled_cron');
						$post_select_hour = get_date_from_gmt( date('Y-m-d H:i:s', $next_cron) );
						$data[$key]['post_date'] = $post_select_hour;
					}
				}

				// Get all taxonomies defined for that post type
    			$all_taxonomies = get_object_taxonomies( $value['post_type'], 'objects' );

    			// Loop on all taxonomies
    			foreach ($all_taxonomies as $taxonomy){

    				/**
	    			 * If taxonomy is object and it is hierarchical, than it is our category
	    			 * NOTE: If taxonomy is not hierarchical than it is tag and we should not consider this
	    			 * And we will only consider first category found in our taxonomy list
	    			 */
	    			if(is_object($taxonomy) && !empty($taxonomy->hierarchical)){

	    				$categories = get_the_terms( $value['ID'], $taxonomy->name );
	    				if(!empty($categories)){

	    					for($i = 0; $i < count($categories); $i++){

	    						$category_list .= $categories[$i]->name;
	    						if($i < ( count($categories) - 1 ))
	    							$category_list .= ', ';
	    					}
	    				}
	    			}
    			}

				$data[$key]['post_category'] = $category_list;

				$data[$key]['author'] = sprintf('<a href="%s">%s</a>', esc_url($author_url), $author_name );
			}
		}

		$result_arr['data']		= !empty($data) ? $data : array();
		$result_arr['total'] 	= $total; // Total no of data

		return $result_arr;
	}

	/**
	 * Mange column data
	 *
	 * Default Column for listing table
	 *
	 * @package Social Auto Poster
	 * @since 1.4.0
	 */
	function column_default( $item, $column_name ){
		switch( $column_name ) {
			case 'post_title':
				$title = $item[ $column_name ];
		    	if( strlen( $title ) > 50 ) {
					$title = substr( $title, 0, 50 );
					$title = $title.'...';
				}
				return $title;
			case 'post_date':
				return !empty( $item[ $column_name ] ) ? $this->model->wpw_auto_poster_get_date_format( $item[ $column_name ], true ) : 'N/A';
            default:
				return $item[ $column_name ];
        }
    }

	/**
	 * Mange post type column data
	 *
	 * Handles to modify post type column for listing table
	 *
	 * @package Social Auto Poster
	 * @since 1.4.0
	 */
    function column_post_type($item) {

		// get all custom post types
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		$post_type_sort_link = '';
		if( !empty( $item[ 'post_type' ] ) && isset( $post_types[$item[ 'post_type' ]]->label ) ) {

			$post_type_sort_link = $post_types[$item[ 'post_type' ]]->label;
		}
		return $post_type_sort_link;
    }

    /**
     * Manage Post Title Column
     *
     * @package Social Auto Poster
     * @since 1.4.0
     */

    function column_post_title($item){

		//Get selected tab
		$selected_tab	= !empty( $_GET['tab'] ) ? stripslashes_deep($_GET['tab']) : 'facebook';

		//Get social meta key
		$status_meta_key = $this->model->wpw_auto_poster_get_social_status_meta_key( $selected_tab );

		//Get social status
		$status	= get_post_meta( $item['ID'], $status_meta_key, true );

		// Get admin page url
		$admin_page_url = add_query_arg( array( 'page' => 'wpw-auto-poster-manage-schedules', 'tab' => $selected_tab ), admin_url( 'admin.php' ) );

		if( empty( $status ) || $status == 1 ) {

			//Get schedule url
			$schedule_url = add_query_arg( array( 'action' => 'schedule', 'schedule[]' => $item['ID'] ), $admin_page_url );
			$actions['schedule'] 	= '<a href="'.esc_url($schedule_url).'">' . esc_html__( 'Schedule', 'wpwautoposter' ) . '</a>';
		} elseif (  $status == 2 ) {

			//Get Unschedule url
			$unschedule_url = add_query_arg( array( 'action' => 'unschedule', 'schedule[]' => $item['ID'] ), $admin_page_url );
			$actions['unschedule'] 	= '<a href="'.esc_url($unschedule_url).'">' . esc_html__( 'Unschedule', 'wpwautoposter' ) . '</a>';
		}

         //Return the title contents
        return sprintf('%1$s %2$s',
            $item['post_title'],
            $this->row_actions( $actions )
        );

    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" /><label for="%1$s[]" class="schedule-check"></label> ',
             $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
             $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    /**
     * Display Columns
     *
     * Handles which columns to show in table
     *
	 * @package Social Auto Poster
	 * @since 1.4.0
     */
	function get_columns(){

        $columns = array(
    						'cb'      			=>	'<input type="checkbox" />', //Render a checkbox instead of text
				            'post_title'		=>	esc_html__( 'Post Title', 'wpwautoposter' ),
				            'post_type'			=>	esc_html__(	'Post Type', 'wpwautoposter' ),
				            'social_status'		=>	esc_html__(	'Status', 'wpwautoposter' ),
				            'post_category'		=>	esc_html__(	'Category', 'wpwautoposter' ),
				            'post_date'			=>	esc_html__(	'Scheduled Date', 'wpwautoposter' ),
				        );
        
        // Unset date column if status filter selected in "Unpublished"
        if( isset( $_GET['wpw_auto_poster_social_status'] ) && empty( $_GET['wpw_auto_poster_social_status'] ) ) {

        	unset( $columns['post_date'] );

        }

        // Change date column header label if status filter selected in "Published"
        if ( isset( $_GET['wpw_auto_poster_social_status'] ) && $_GET['wpw_auto_poster_social_status'] == '1') {

        	$columns['post_date'] = esc_html__(	'Published Date', 'wpwautoposter' );
        }

        return $columns;
    }

    /**
     * Sortable Columns
     *
     * Handles soratable columns of the table
     *
	 * @package Social Auto Poster
	 * @since 1.4.0
     */
	function get_sortable_columns() {

		$sortable_columns	= array(
									'post_title'	=>	array( 'post_title', true ),    //true means its already sorted
									'post_type'		=>	array( 'post_type', true )
								);

		return $sortable_columns;
	}

	function no_items() {
		//message to show when no records in database table
		esc_html_e( 'No post found.', 'wpwautoposter' );
	}

	/**
     * Bulk actions field
     *
     * Handles Bulk Action combo box values
     *
	 * @package Social Auto Poster
	 * @since 1.4.0
     */
	function get_bulk_actions() {
		//bulk action combo box parameter
		//if you want to add some more value to bulk action parameter then push key value set in below array
		$actions = array(
							'schedule'    => esc_html__('Schedule','wpwautoposter'),
							'unschedule'  => esc_html__('Unschedule','wpwautoposter')
						);

		return $actions;
	}

	/**
     * Add filter for post types
     *
     * Handles to display records for particular post type
     *
	 * @package Social Auto Poster
	 * @since 1.4.0
     */
    function extra_tablenav( $which ) {

    	if( $which == 'top' ) {

			//Get all post type names
			$all_types = get_post_types( array( 'public' => true ), 'objects');

			//get all social status
			$social_status = array(
								'' 	=> esc_html__( 'Unpublished','wpwautoposter' ),
								1 	=> esc_html__( 'Published','wpwautoposter' ),
								2 	=> esc_html__( 'Scheduled','wpwautoposter' )
								);

			$post_parent_ids = array();

			$html = '';

    		$html .= '<div class="alignleft actions filteractions">';

			$html .= '<select name="wpw_auto_poster_post_type" id="wpw_auto_poster_post_type" data-placeholder="' . esc_html__( 'Show all post type', 'wpwautoposter' ) . '">';

			$html .= '<option value="" ' .  selected( isset( $_GET['wpw_auto_poster_post_type'] ) ? stripslashes_deep($_GET['wpw_auto_poster_post_type']) : '', '', false ) . '>'.esc_html__( 'Show all post type', 'wpwautoposter' ).'</option>';

			if ( !empty( $all_types ) ) {

				foreach ( $all_types as $key => $type ) {

					if( in_array( $key, array( 'attachment' ) ) ) continue;
					$html .= '<option value="' . esc_attr($key) . '" ' . selected( isset( $_GET['wpw_auto_poster_post_type'] ) ? stripslashes_deep($_GET['wpw_auto_poster_post_type']) : '', $key, false ) . '>' . esc_html($type->label) . '</option>';
				}

			}
			$html .= '</select>';

			// HTML for select category starts
			$html .= '<select name="wpw_auto_poster_cat_id" id="wpw_auto_poster_cat_id" data-placeholder="' . esc_html__( 'Select Category', 'wpwautoposter' ) . '">';
			$html .= '<option value="">' . esc_html__('Select Category', 'wpwautoposter') . '</option>';
			$html .= '</select>';
			// HTML for select category ends

			$html .= '<select name="wpw_auto_poster_social_status" id="wpw_auto_poster_social_status" data-placeholder="' . esc_html__( 'Show all status', 'wpwautoposter' ) . '">';

			foreach ( $social_status as $key => $name ) {

				$html .= '<option value="' . esc_attr($key) . '" ' . selected( isset( $_GET['wpw_auto_poster_social_status'] ) ? stripslashes_deep($_GET['wpw_auto_poster_social_status']) : '2', $key, false ) . '>' . esc_attr($name) . '</option>';
			}
			$html .= '</select>';

			// HTML for date filter starts
    		$style = "schedule_wallpost_option";

    		// if( !empty( $_GET['wpw_auto_poster_social_status'] ) ) {

    		// 	$style = ( $_GET['wpw_auto_poster_social_status'] == '2' ) ?  "schedule_wallpost_option" : "post_msg_style_hide" ;
    		// }
    		$html .= '<div class="wp-auto-date-filter ' . esc_attr($style) . '">
							
							<input type="text" name="wpw_auto_start_date" id="wpw_auto_start_date" class="wpw-auto-datepicker" placeholder="'.esc_html__( 'From Date', 'wpwautoposter') .'" value="'.(isset($_REQUEST['wpw_auto_start_date'])?  sanitize_text_field(esc_attr($_REQUEST['wpw_auto_start_date'])) : '').'">
							<input type="text" name="wpw_auto_end_date" id="wpw_auto_end_date" class="wpw-auto-datepicker" placeholder="'.esc_html__( 'To Date', 'wpwautoposter') .'" value="'.(isset($_REQUEST['wpw_auto_end_date'])?  sanitize_text_field(esc_attr($_REQUEST['wpw_auto_end_date'])) : '').'">
					</div>';
			// HTML for date filter ends

    		$html .= '<input type="submit" value="'.esc_html__( 'Filter', 'wpwautoposter' ).'" class="button" id="post-query-submit" name="">';
    		
    		// HTML for clear filter button starts
    		if( ! empty( $_REQUEST['wpw_auto_start_date'] ) || ! empty( $_REQUEST['wpw_auto_end_date'] ) ) {

            $html .= '<a href="'.admin_url( 'admin.php?page=wpw-auto-poster-manage-schedules' ).'" class="button wpw-clear-filter">'. esc_html__( 'Clear Filter', 'wpwautoposter' ).'</a>';
        	}
        	// HTML for clear filter button ends

        	$html .= '</div>';		
                	
			echo $html;
    	}
    }

    function prepare_items() {

        // Get how many records per page to show
        $per_page	= $this->per_page;

        // Get All, Hidden, Sortable columns
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

		// Get final column header
        $this->_column_headers = array($columns, $hidden, $sortable);

		// Get Data of particular page
		$data_res 	= $this->display_scheduling_posts();
		
		$data 		= $data_res['data'];

		// Get current page number
        $current_page = $this->get_pagenum();

		// Get total count
        $total_items  = $data_res['total'];

        // Get page items
        $this->items = $data;

		// We also have to register our pagination options & calculations.
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

	public function pagination($which = 'top') {
        $total_pages = $this->get_pagination_arg('total_pages');
        $current_page = $this->get_pagenum();
        
        $pagination_html = custom_pagination($total_pages, $current_page);
        
        echo '<ul class="pagination tablenav ' . esc_attr($which) . '">';
        echo $pagination_html;
        echo '</ul>';
    }

}

function custom_pagination($total_pages, $current_page) {
	$range = 2; // Show two pages before and after the current page
	$pagination = '';
	$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
	$post_type =  isset($_GET['wpw_auto_poster_post_type']) ? sanitize_text_field($_GET['wpw_auto_poster_post_type']) : '';
	$cat_id =  isset($_GET['wpw_auto_poster_cat_id']) ? sanitize_text_field($_GET['wpw_auto_poster_cat_id']) : '';
	$social_status =  isset($_GET['wpw_auto_poster_social_status']) ? sanitize_text_field($_GET['wpw_auto_poster_social_status']) : '';
	$start_date =  isset($_GET['wpw_auto_start_date']) ? sanitize_text_field($_GET['wpw_auto_start_date']) : '';
	$end_date =  isset($_GET['wpw_auto_end_date']) ? sanitize_text_field($_GET['wpw_auto_end_date']) : '';
	
	if ($total_pages > 1) {
		// "Previous" button
		$pagination .= $current_page > 1 ? '
		<li class="paginate_button previous"><a href="?page=wpw-auto-poster-manage-schedules&paged=' . ($current_page - 1) . '&wpw_auto_poster_cat_id='.$cat_id.'&wpw_auto_poster_post_type='.$post_type.'&wpw_auto_poster_social_status='.$social_status.'&wpw_auto_start_date='.$start_date.'&wpw_auto_end_date='.$end_date.'&s='.$s.'">Previous</a></li> ' : '<li class="paginate_button previous disabled"><a href="#">Previous</a></li> ';
		
		$start = max(1, $current_page - $range);
		$end = min($total_pages, $current_page + $range);

		// Add the first page and a "..." if needed
		if ($start > 1) {
			$pagination .= '<li class="paginate_button "><a href="?page=wpw-auto-poster-manage-schedules&paged=1&wpw_auto_poster_cat_id='.$cat_id.'&wpw_auto_poster_post_type='.$post_type.'&wpw_auto_poster_social_status='.$social_status.'&wpw_auto_start_date='.$start_date.'&wpw_auto_end_date='.$end_date.'&s='.$s.'">1</a></li> ... ';
		}

		for ($i = $start; $i <= $end; $i++) {
			$pagination .= $i === $current_page ? '<li class="paginate_button active"><a href="#">' . $i . '</a></li> ' : '<li class="paginate_button "><a class="paginate_button " href="?page=wpw-auto-poster-manage-schedules&paged=' . $i . '&wpw_auto_poster_cat_id='.$cat_id.'&wpw_auto_poster_post_type='.$post_type.'&wpw_auto_poster_social_status='.$social_status.'&wpw_auto_start_date='.$start_date.'&wpw_auto_end_date='.$end_date.'&s='.$s.'">' . $i . '</a></li> ';
		}

		// Add the last page and a "..." if needed
		if ($end < $total_pages) {
			$pagination .= '... <li class="paginate_button"><a " href="?page=wpw-auto-poster-manage-schedules&paged=' . $total_pages . '&wpw_auto_poster_cat_id='.$cat_id.'&wpw_auto_poster_post_type='.$post_type.'&wpw_auto_poster_social_status='.$social_status.'&wpw_auto_start_date='.$start_date.'&wpw_auto_end_date='.$end_date.'&s='.$s.'">' . $total_pages . '</a></li>';
		}

		// "Next" button
		$pagination .= $current_page < $total_pages ? ' <li class="paginate_button next"><a href="?page=wpw-auto-poster-manage-schedules&paged=' . ($current_page + 1) . '&wpw_auto_poster_cat_id='.$cat_id.'&wpw_auto_poster_post_type='.$post_type.'&wpw_auto_poster_social_status='.$social_status.'&wpw_auto_start_date='.$start_date.'&wpw_auto_end_date='.$end_date.'&s='.$s.'">Next</a></li>' : ' <li class="paginate_button next disabled"><a href="#">Next</a></li>';
	}

	return $pagination;
}

global $wpw_auto_poster_options;

//Create an instance of our package class...
$WpwAutoPosterManageSchedulesListTable = new Wpw_Auto_Poster_Manage_schedules_List();

//Fetch, prepare, sort, and filter our data...
$WpwAutoPosterManageSchedulesListTable->prepare_items();
?>
<div class="wrap wpw-scheduling-wrap manage-schedules-main">
<?php wpw_slg_header_menu(); ?>
	<div class="sub-header">
        <div class="woo-slg-top-header-wrap">
            <div class="logo-header-wrap">
				<!-- wpweb logo -->
				<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL) . '/wpw-auto-poster-logo.png'; ?>" class="wpw-auto-poster-logo" alt="<?php esc_html_e( 'Logo', 'wpwautoposter' );?>" />

				<div><?php esc_html_e( 'Manage Schedules', 'wpwautoposter' ); ?></div>
			</div>
		</div>
		<div class="woo-slg-top-error-wrap">
			<h2>Settings Error</h2>
		</div>
		<?php
		//showing sorting links on the top of the list
		$WpwAutoPosterManageSchedulesListTable->views();

		if( empty( $wpw_auto_poster_options['schedule_wallpost_option'] ) ) { //check message

			$settings_url = add_query_arg(array('page' => 'wpw-auto-poster-settings'), admin_url('admin.php'));
			
			echo '<div class="error fade" id="message">
					<p><strong>';
			echo sprintf(esc_html__('Please go to Social Auto Poster Settings -> General settings -> %s Schedule Wall Posts %s and select schedule option first.','wpwautoposter'), '<a href="'.esc_url($settings_url).'">', "</a>");
			echo '</strong></p></div>';
		} else {

			if(isset($_GET['message']) && !empty($_GET['message']) ) { //check message

				if( $_GET['message'] == '1' ) { //check message

					echo '<div class="updated fade" id="message">
							<p><strong>'.esc_html__("Post(s) Scheduled successfully.",'wpwautoposter').'</strong></p>
						</div>';

				} elseif ( $_GET['message'] == '2' ) { //check message

					echo '<div class="updated fade" id="message">
							<p><strong>'.esc_html__("Post(s) Unscheduled successfully.",'wpwautoposter').'</strong></p>
						</div>';

				}
			}

		//Get selected tab
		$selected_tab	= !empty( $_GET['tab'] ) ? stripslashes_deep($_GET['tab']) : '';

		//Get admin url
		$admin_url = admin_url('admin.php'); ?>
	</div>
	<div class="settings_page_url-form" bis_skin_checked="1">
		<div class="content woo-slg-content-section" bis_skin_checked="1">
			<div class="nav-tab-wrapper-inner" bis_skin_checked="1">
				<h2 class="nav-tab-wrapper wpw-auto-poster-h2 wpw-auto-poster-h2_common woo-slg-h2">
				<a class="nav-tab <?php echo empty( $selected_tab ) || $selected_tab == 'facebook' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  stripslashes_deep($_GET['page']), 'tab' =>  'facebook' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/tab-icon/facebook.svg" width="24" height="24" alt="fb" title="<?php esc_html_e( 'Facebook', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'twitter' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  stripslashes_deep($_GET['page']), 'tab' =>  'twitter' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/twitter_set.png" width="24" height="24" alt="tw" title="<?php esc_html_e( 'Twitter', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'linkedin' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  stripslashes_deep($_GET['page']), 'tab' =>  'linkedin' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/linkedin_set.png" width="24" height="24" alt="li" title="<?php esc_html_e( 'LinkedIn', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'tumblr' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  stripslashes_deep($_GET['page']), 'tab' =>  'tumblr' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/tumblr_set.png" width="24" height="24" alt="tb" title="<?php esc_html_e( 'Tumblr', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'youtube' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg(array('page' => stripslashes_deep($_GET['page']), 'tab' => 'youtube'), $admin_url); ?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_IMG_URL); ?>/youtube_set.png" width="24" height="24" alt="yt" title="<?php esc_html_e('Youtube', 'wpwautoposter'); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'pinterest' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  stripslashes_deep($_GET['page']), 'tab' =>  'pinterest' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/pinterest_set.png" width="24" height="24" alt="ins" title="<?php esc_html_e( 'Pinterest', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'googlemybusiness' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'googlemybusiness' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/googlemybusiness_set.png" width="24" height="24" alt="gmb" title="<?php esc_html_e( 'Google My Business', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'reddit' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'reddit' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/reddit_set.png" width="24" height="24" alt="reddit" title="<?php esc_html_e( 'Reddit', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'telegram' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'telegram' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/telegram_set.png" width="24" height="24" alt="tele" title="<?php esc_html_e( 'Telegram', 'wpwautoposter' ); ?>" />
				</a>
				<a class="nav-tab <?php echo $selected_tab == 'medium' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'medium' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/medium_set.png" width="24" height="24" alt="wp" title="<?php esc_html_e( 'Medium', 'wpwautoposter' ); ?>" />
				</a>
				
				<a class="nav-tab <?php echo $selected_tab == 'instagram' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'instagram' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/instagram_set.png" width="24" height="24" alt="insta" title="<?php esc_html_e( 'Instagram', 'wpwautoposter' ); ?>" />
				</a>
				
				<a class="nav-tab <?php echo $selected_tab == 'wordpress' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array( 'page' =>  $_GET['page'], 'tab' =>  'wordpress' ), $admin_url );?>">
					<img src="<?php echo esc_url(WPW_AUTO_POSTER_URL); ?>includes/images/wordpress_set.png" width="24" height="24" alt="wp" title="<?php esc_html_e( 'WordPress', 'wpwautoposter' ); ?>" />
				</a>

				<?php
				do_action( 'wpw_auto_poster_manage_schedules_after_list', $selected_tab, $admin_url );
				do_action( 'wpw_auto_poster_manage_schedules_list_after_ba', $selected_tab, $admin_url ); ?>
				</h2>
			</div>	
			<div class="woo-slg-content social-posting-logs" bis_skin_checked="1">
				<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
				<form id="product-filter" method="get" class="wpw-auto-poster-form">

					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
					<input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']) ?>" />
					<input type="hidden" name="tab" value="<?php echo isset( $_REQUEST['tab'] ) ? esc_attr($_REQUEST['tab']) : '';?>" />

					<!-- Search Title -->
					<?php $WpwAutoPosterManageSchedulesListTable->search_box( esc_html__( 'Search', 'wpwautoposter' ), 'wpwautoposter' ); ?>

					<!-- Now we can render the completed list table -->
					<?php $WpwAutoPosterManageSchedulesListTable->display(); ?>
				</form>
			</div>	
		</div>	
	</div>	

	
	<?php } ?>


<!-- Render popup content when hourly scheduling is set-->
<?php if( !empty($wpw_auto_poster_options) && $wpw_auto_poster_options['schedule_wallpost_option'] == "hourly") { 

	$next_cron = wp_next_scheduled( 'wpw_auto_poster_scheduled_cron' );
	$default_select_hour = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_cron ), 'Y-m-d H:i' );
	?>
	<!-- HTML for Schedule Post popup Starts-->
	<div class="wpw-auto-poster-popup-content wp-map-post-types-popup">
		<div class="wpw-popup-header">
			<div class="wpw-auto-poster-header-title"><?php
			esc_html_e( 'Schedule Post(s)', 'wpwautoposter' ) ?></div>
			<div class="wpw-auto-poster-popup-close"><a href="javascript:void(0);" class="wpw-auto-poster-close-button">&times;</a></div>
		</div>
									
		<div class="wpw-auto-poster-popup meta-box-sortables">
			<table class="form-table">
				<tbody>
					<tr>
						<td scope="col">Schedule Date/Time</td>
						<td>
							<input type="text" name="wpw_auto_select_hour" id="wpw_auto_select_hour" class="wpw-auto-datepicker" value="<?php echo esc_attr($default_select_hour);?>">
							<input type="hidden" name="schedule_url" id="schedule_url">
						</td>
					</tr>
					<tr>
						<td scope="col"></td>
						<td>
							<button class="button done button-primary" name="done" id="done" value="Done">Done</button>
						</td>
					</tr>
				</tbody>				
			</table>
		</div><!--.wpw-auto-popup-->
	</div><!--.wpw-auto-popup-content-->
	<div class="wpw-auto-poster-popup-overlay wpw-auto-schedule-overlay"></div>
	<!-- HTML for Schedule Post popup Ends-->
<?php } ?>
</div>