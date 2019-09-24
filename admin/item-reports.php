<?php
/**
 * Author: Alin Marcu
 * Author URI: https://deconf.com
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GADWP_Backend_Item_Reports' ) ) {

	final class GADWP_Backend_Item_Reports {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GADWP();

			if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_back'] ) && 1 == $this->gadwp->config->options['backend_item_reports'] ) {
				// Add custom column in Posts List
				add_filter( 'manage_posts_columns', array( $this, 'add_columns' ) );

				// Populate custom column in Posts List
				add_action( 'manage_posts_custom_column', array( $this, 'add_icons' ), 10, 2 );

				// Add custom column in Pages List
				add_filter( 'manage_pages_columns', array( $this, 'add_columns' ) );

				// Populate custom column in Pages List
				add_action( 'manage_pages_custom_column', array( $this, 'add_icons' ), 10, 2 );
			}
		}

		private function post_type_is_blacklisted()
		{
			$post_type = get_post_type();

			if ( ! $post_type ) {
				return false;
			}

			// post types to NOT show the "Analytics" column on
			$blacklist_post_types = [];

			// allow plugins/themes to add post types to the blacklist
			$blacklist_post_types = apply_filters( 'gadwp_post_types_blacklist', $blacklist_post_types );

			return in_array( $post_type, $blacklist_post_types );
		}

		public function add_icons( $column, $id ) {
			global $wp_version;

			if ( 'gadwp_stats' != $column || $this->post_type_is_blacklisted() ) {
				return;
			}

			if ( version_compare( $wp_version, '3.8.0', '>=' ) ) {
				echo '<a id="gadwp-' . $id . '" title="' . get_the_title( $id ) . '" href="#' . $id . '" class="gadwp-icon dashicons-before dashicons-chart-area">&nbsp;</a>';
			} else {
				echo '<a id="gadwp-' . $id . '" title="' . get_the_title( $id ) . '" href="#' . $id . '"><img class="gadwp-icon-oldwp" src="' . GADWP_URL . 'admin/images/gadwp-icon.png"</a>';
			}
		}

		public function add_columns( $columns ) {
			if ( $this->post_type_is_blacklisted() ) {
				return $columns;
			}

			return array_merge( $columns, array( 'gadwp_stats' => __( 'Analytics', 'google-analytics-dashboard-for-wp' ) ) );
		}
	}
}
