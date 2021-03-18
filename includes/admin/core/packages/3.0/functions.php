<?php if ( ! defined( 'ABSPATH' ) ) exit;


function um_upgrade_common30() {
	UM()->admin()->check_ajax_nonce();

	um_maybe_unset_time_limit();

	UM()->options()->remove( 'enable_blocks' );

	update_option( 'um_last_version_upgrade', '3.0' );

	wp_send_json_success( array( 'message' => __( 'Updated successfully', 'ultimate-member' ) ) );
}