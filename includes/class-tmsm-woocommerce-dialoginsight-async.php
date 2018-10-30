<?php

if ( class_exists( 'WP_Async_Request' ) ) {

	class Tmsm_WooCommerce_DialogInsight_Async extends WP_Async_Request {

		/**
		 * @var string
		 */
		protected $action = 'tmsm_woocommerce_dialoginsight_async';


		/**
		 * Contains an instance of the DialogInsight API library, if available.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    object $api If available, contains an instance of the DialogInsight API library.
		 */
		private $api = null;

		public $options = null;

		/**
		 * Initializes DialogInsight API if credentials are valid.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @uses   GFAddOn::get_plugin_setting()
		 * @uses   GFAddOn::log_debug()
		 * @uses   GFAddOn::log_error()
		 * @uses   GF_DialogInsight_API::account_details()
		 *
		 * @return bool|null
		 */
		public function initialize_api( ) {

			include_once 'class-tmsm-woocommerce-dialoginsight-api.php';

			error_log('initialize_api');
			// If API is alredy initialized, return true.
			if ( ! is_null( $this->api ) ) {
				return true;
			}

			$options = get_option( 'woocommerce_tmsm_woocommerce_dialoginsight_settings', array() );

			$api_key = $this->options->get_option('api_key');
			$key_id = $this->options->get_option('key_id');

			error_log('$api_key: '.$api_key);
			error_log('$key_id: '.$key_id);

			// If the API key is blank, do not run a validation check.
			if ( rgblank( $api_key ) || rgblank( $key_id ) ) {
				return null;
			}

			// Setup a new DialogInsight object with the API credentials.
			$dialoginsight = new Tmsm_WooCommerce_DialogInsight_API( $api_key, $key_id );

			try {

				// Assign API library to class.
				$this->api = $dialoginsight;

				// Log that authentication test passed.
				error_log( __METHOD__ . '(): DialogInsight successfully authenticated.' );
				error_log( __METHOD__ . '(): Return body 1:'. var_export($dialoginsight, true) );
				return true;

			} catch ( Exception $e ) {

				// Log that authentication test failed.
				error_log( __METHOD__ . '(): Unable to authenticate with DialogInsight; ' . $e->getMessage() );

				return false;

			}

		}

		/**
		 * Handle
		 *
		 * Override this method to perform any actions required
		 * during the async request.
		 */
		public function handle() {
			error_log( 'handle' );
			error_log(print_r($_POST, true) );

			$email = sanitize_email($_POST['billing_email']);
			$subscribe = false;
			$subscribe = isset( $_POST['tmsm_woocommerce_dialoginsight_optin'] ) ? (int) $_POST['tmsm_woocommerce_dialoginsight_optin'] : 0;

			if(empty($email) || $subscribe == false){
				error_log('email empty or not check subscribe');
				return;
			}

			if($this->initialize_api()){
				error_log('api initialized');

				$member        = false;
				$member_found  = false;
				$member_status = null;

				// If member status is not defined, set to subscribed.
				$member_status = isset( $member_status ) ? $member_status : 'subscribed';

				$list_id    = $this->options->get_option('list_id');
				$project_id    = $this->options->get_option('project_id');

				if ( empty( $project_id ) || empty( $list_id ) ) {
					return;
				}

				// Prepare transaction type for filter.
				$transaction = $member_found ? 'Update' : 'Subscribe';

				$action = $member_found ? 'updated' : 'added';

				$merge_vars[ 'optin_' . $list_id ] = true;
				$merge_vars[ 'f_EMail' ] = $email;
				if ( ! empty( $_POST['billing_last_name'] ) ) {
					$merge_vars[ 'f_FirstName' ] = sanitize_text_field($_POST['billing_first_name']);
				}
				if ( ! empty( $_POST['billing_last_name'] ) ) {
					$merge_vars[ 'f_LastName' ] = sanitize_text_field($_POST['billing_last_name']);
				}

				if ( class_exists( 'Tmsm_Woocommerce_Billing_Fields_Public' ) && ! empty( $_POST['billing_title'] ) ) {
					$title_options = Tmsm_Woocommerce_Billing_Fields_Public::billing_title_options();

					$title = $title_options[ sanitize_text_field( $_POST['billing_title'] ) ];
					if ( ! empty( $title ) ) {
						$merge_vars['f_civilite'] = $title;
					}
				}

				if ( class_exists( 'Tmsm_Woocommerce_Billing_Fields_Public' ) && ! empty( $_POST['billing_birthday'] ) ) {
					$birthday_input = sanitize_text_field( $_POST['billing_birthday'] );

					$objdate = DateTime::createFromFormat( _x( 'm/d/Y', 'birthday date format conversion', 'tmsm-woocommerce-billing-fields' ),
						$birthday_input );

					error_log(print_r($objdate, true));
					if ( $objdate instanceof DateTime ) {
						$merge_vars['f_dateNaissance'] = $objdate->format( 'Y-m-d' ); // Fixed format by DialogInsight
					}
				}


				// Prepare request parameters.
				$params = array(
					'idProject'    => $project_id,
					'Records'      => array(
						array(
							'ID'   => array(
								'key_f_EMail' => strtolower($email),
							),
							'Data' => $merge_vars,
						),
					),
					'MergeOptions' => array(
						'AllowInsert'            => true,
						'AllowUpdate'            => true,
						'SkipDuplicateRecords'   => false,
						'SkipUnmatchedRecords'   => false,
						'ReturnRecordsOnSuccess' => false,
						'ReturnRecordsOnError'   => false,
						'FieldOptions'           => null,
					),
				);

				error_log(print_r($params, true));

				try {

					$response = $this->api->update_list_member( $params );

					error_log('Subscriber successful');

				} catch ( Exception $e ) {

					error_log('Unable to add/update subscriber');
					return;

				}

			}
		}
	}
}
