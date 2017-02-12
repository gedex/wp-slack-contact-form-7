<?php
/**
 * Plugin Name: Slack Contact Form 7
 * Plugin URI: https://github.com/gedex/wp-contact-form-7
 * Description: This plugin allows you to send notifications to Slack channels whenever someone sent message through Contact Form 7.
 * Version: 0.2.0
 * Author: Akeda Bagus
 * Author URI: http://gedex.web.id
 * Text Domain: slack
 * Domain Path: /languages
 * License: GPL v2 or later
 * Requires at least: 3.6
 * Tested up to: 3.8
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package Slack_WPCF7
 */

/**
 * Adds new event that send notification to Slack channel
 * when someone sent message through Contact Form 7.
 *
 * @param  array $events List of events.
 * @return array
 *
 * @filter slack_get_events
 */
function wp_slack_wpcf7_submit( $events ) {
	$events['wpcf7_submit'] = array(
		// Action in Gravity Forms to hook in to get the message.
		'action' => 'wpcf7_submit',

		// Description appears in integration setting.
		'description' => __( 'When someone sent message through Contact Form 7', 'slack' ),

		// Message to deliver to channel. Returns false will prevent
		// notification delivery.
		'message' => function( $form, $result ) {

			// @todo: Once attachment is supported in Slack
			// we can send payload with nicely formatted message
			// without relying on mail_sent result.
			$sent = (
				! empty( $result['mail_sent'] )
				||
				( ! empty( $result['status'] ) && 'mail_sent' === $result['status'] )
			);

			if ( $sent ) {
				return apply_filters( 'slack_wpcf7_submit_message',
					sprintf(
						__( 'Someone just sent a message through *%s* _Contact Form 7_. Check your email!', 'slack' ),
						is_callable( array( $form, 'title' ) ) ? $form->title() : $form->title
					),
					$form,
					$result
				);
			}

			return false;
		}
	);

	return $events;
}
add_filter( 'slack_get_events', 'wp_slack_wpcf7_submit' );
