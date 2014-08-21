<?php
/*********************************
 * This function returns the function name to display the timeslots on frontend if type of timeslot is Multiple for multiple time slots addon.
 ********************************/
function bkap_ajax_on_select_date() {
	global $post;
	$booking_settings = get_post_meta($post->ID, 'woocommerce_booking_settings', true);
	if(isset($booking_settings['booking_enable_multiple_time']) && $booking_settings['booking_enable_multiple_time'] == "multiple" && is_plugin_active('bkap-multiple-time-slot/multiple-time-slot.php')) {
		return 'multiple_time';
	}
	/*else
	{
		return 'check_for_time_slot';
	}*/
}

?>