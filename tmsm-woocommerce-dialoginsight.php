<?php

/**
 * @link              https://github.com/nicomollet
 * @since             1.0.0
 * @package           Tmsm_Woocommerce_DialogInsight
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM WooCommerce DialogInsight
 * Plugin URI:        https://github.com/thermesmarins/tmsm-woocommerce-dialoginsight
 * Description:       DialogInsight integration in WooCommerce
 * Version:           1.0.0
 * Author:            Nicolas Mollet
 * Author URI:        https://github.com/nicomollet
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tmsm-woocommerce-dialoginsight
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-woocommerce-dialoginsight
 * Github Branch:     master
 * Requires PHP:      5.6
 */

/**
 * Tmsm_WooCommerce_DialogInsight class
 */
if ( ! class_exists( 'Tmsm_WooCommerce_DialogInsight' ) ) {

	class Tmsm_WooCommerce_DialogInsight {

		/**
		 * Instance of Tmsm_WooCommerce_DialogInsight_Actions.
		 *
		 * @var Tmsm_WooCommerce_DialogInsight_Actions
		 */
		public $actions;

		/**
		 * Tmsm_WooCommerce_DialogInsight_Async.
		 *
		 * @var Tmsm_WooCommerce_DialogInsight_Async
		 */
		protected $async;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) );

			// include required files
			$this->includes();

			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'plugins_loaded', array( $this->actions, 'load_plugin_textdomain' ) );

		}

		/**
		 * Initiate Plugin
		 *
		 * @since 1.0
		 */
		public function init() {



			if ( class_exists( 'WC_Integration' ) ) {
				include_once 'includes/class-tmsm-woocommerce-dialoginsight-integration.php';
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

				$integration = new Tmsm_WooCommerce_DialogInsight_Integration();
				$checkbox_label = esc_html($integration->get_option( 'checkbox_label', __( 'Subscribe to our newsletter', 'tmsm-woocommerce-dialoginsight' ) ));
				$checkbox_action = esc_html($integration->get_option( 'checkbox_action', 'woocommerce_after_checkout_billing_form' ));

				add_action( $checkbox_action, array( $this->actions, 'apply_checkbox' ) );
				add_action( 'woocommerce_checkout_order_processed', array( $this->actions, 'process_checkbox' ), 3, 100 );

				add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_handler' ), 3, 200 );

			}
			
		}

		/**
		 * Process handler (order)
		 *
		 * @param int      $order_id
		 * @param array    $posted_data
		 * @param WC_Order $order
		 */
		public function process_handler( $order_id, $posted_data, $order ) {

			error_log( 'process_handler' );

			include_once 'includes/class-tmsm-woocommerce-dialoginsight-async.php';
			if ( class_exists( 'Tmsm_WooCommerce_DialogInsight_Async' ) ) {
				error_log('Tmsm_WooCommerce_DialogInsight_Async');
				$this->async = new Tmsm_WooCommerce_DialogInsight_Async();
				$integration = new Tmsm_WooCommerce_DialogInsight_Integration();
				$this->async->options = $integration;
				$this->async->initialize_api();

				$rand = wp_rand( 0, 999 );

				$this->async->handle();
			}


		}

		/**
		 * Include required files
		 *
		 * @since 1.0
		 */
		private function includes() {

			// load main class
			require( 'includes/class-tmsm-woocommerce-dialoginsight.php' );
			$this->actions = Tmsm_WooCommerce_DialogInsight_Actions::get_instance();

		}

		/**
		* Gets the absolute plugin path without a trailing slash, e.g.
		* /path/to/wp-content/plugins/plugin-directory
		*
		* @return string plugin path
		*/
		public function get_plugin_path() {
			if ( isset( $this->plugin_path ) ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		
		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               Tmsm WooCommerce DialogInsight.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'Tmsm_WooCommerce_DialogInsight_Integration';
			return $integrations;
		}
		
		/**
		 * Remove terms and scheduled events on plugin deactivation
		 *
		 * @since 1.0
		 */
		public function deactivate() {

		}
		
	}

}

/**
 * Register this class globally
 */
$GLOBALS['Tmsm_WooCommerce_DialogInsight'] = new Tmsm_WooCommerce_DialogInsight();

