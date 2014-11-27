<?php
add_action( 'widgets_init', 'bkap_widgets_init');

/****************************
 * This function initialize the wideget , and register the same.
 *****************************/
function bkap_widgets_init() {
	include_once("widget-product-search.php");
	register_widget( 'Custom_WooCommerce_Widget_Product_Search' );
}

/***********************************
*Modify current search by adding where clause to cquery fetching posts
************************************/
function bkap_get_custom_posts($where, $query){
	global $wpdb;
	$booking_table = $wpdb->prefix . "booking_history";
	$meta_table = $wpdb->prefix . "postmeta";
	$post_table = $wpdb->prefix . "posts";
	if(!empty($_GET["w_check_in"])  && $query->is_main_query()){
		$chkin  = $_GET["w_checkin"];  
		$chkout = $_GET["w_checkout"];
	
		$start_date = $chkin;
		$end_date =  $chkout;
	
		$language_selected = '';
		if (defined('ICL_LANGUAGE_CODE'))
		{
			if(constant('ICL_LANGUAGE_CODE') != '')
			{
				$wpml_current_language = constant('ICL_LANGUAGE_CODE');
				if (!empty($wpml_current_language)) {
					$language_selected = $wpml_current_language;
				}
			}
		}
		if($language_selected != '')
		{
			$where = " AND($wpdb->posts.post_type = 'product'and $wpdb->posts.post_status = 'publish') AND $wpdb->posts.ID NOT IN
			(SELECT b.post_id FROM $booking_table AS b
			WHERE ('$start_date' between b.start_date and date_sub(b.end_date,INTERVAL 1 DAY))
			or
			('$end_date' between b.start_date and date_sub(b.end_date,INTERVAL 1 DAY))
			or
			(b.start_date between '$start_date' and '$end_date')
			or
			b.start_date = '$start_date'
			)and $wpdb->posts.ID NOT IN(SELECT post_id from $meta_table
			where meta_key =  'woocommerce_booking_settings' and meta_value LIKE  '%booking_enable_date\";s:0%') and $wpdb->posts.ID NOT IN(SELECT a.id
			FROM $post_table AS a
			LEFT JOIN $meta_table AS b ON a.id = b.post_id
			AND (
			b.meta_key =  'woocommerce_booking_settings'
			)
			WHERE b.post_id IS NULL) AND $wpdb->posts.ID IN
			(SELECT element_id FROM $icl_table
			WHERE language_code = '".ICL_LANGUAGE_CODE."')";
		
		
		}
		else
		{
			$where = " AND($wpdb->posts.post_type = 'product'and $wpdb->posts.post_status = 'publish') AND $wpdb->posts.ID NOT IN
					(SELECT b.post_id FROM $booking_table AS b
					WHERE ('$start_date' between b.start_date and date_sub(b.end_date,INTERVAL 1 DAY))
					  or 
					  ('$end_date' between b.start_date and date_sub(b.end_date,INTERVAL 1 DAY))
					  or 
					  (b.start_date between '$start_date' and '$end_date')
					  or
					  b.start_date = '$start_date'
					)and $wpdb->posts.ID NOT IN(SELECT post_id from $meta_table
					where meta_key =  'woocommerce_booking_settings' and meta_value LIKE  '%booking_enable_date\";s:0%') and $wpdb->posts.ID NOT IN(SELECT a.id
					FROM $post_table AS a
					LEFT JOIN $meta_table AS b ON a.id = b.post_id
					AND (
					b.meta_key =  'woocommerce_booking_settings'
					)
					WHERE b.post_id IS NULL)";
		}
		
	}
	return $where;

}
add_filter( 'posts_where','bkap_get_custom_posts', 10, 2 );
