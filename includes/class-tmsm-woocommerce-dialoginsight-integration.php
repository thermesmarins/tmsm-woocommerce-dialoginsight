<?php
	
if ( ! class_exists( 'Tmsm_WooCommerce_DialogInsight_Integration' ) ) :

class Tmsm_WooCommerce_DialogInsight_Integration extends WC_Integration {
	
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		
		global $woocommerce;
		
		$this->id                 = 'tmsm_woocommerce_dialoginsight';
		$this->method_title       = __( 'Tmsm WooCommerce DialogInsight', 'tmsm-woocommerce-dialoginsight' );
		$this->method_description = __( 'Allow buyers to optin for a Dialog Insight list', 'tmsm-woocommerce-dialoginsight' );
		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		
		// Define variables.
		$this->api_key          = $this->get_option( 'api_key' );
		$this->key_id  = $this->get_option( 'key_id' );
		$this->project_id  = $this->get_option( 'project_id' );
		$this->optin_field  = $this->get_option( 'optin_field' );
		$this->checkbox_label  = $this->get_option( 'checkbox_label' );
		$this->checkbox_action  = $this->get_option( 'checkbox_action' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
	}
	
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'api_key' => array(
				'title'             => __( 'API Key', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'description'       => __( 'Enter your API Key', 'tmsm-woocommerce-dialoginsight' ),
				'desc_tip'          => true,
				'default'           => ''
			),

			'key_id' => array(
				'title'             => __( 'Key ID', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'description'       => __( 'Enter your Key ID', 'tmsm-woocommerce-dialoginsight' ),
				'desc_tip'          => true,
				'default'           => ''
			),

			'project_id' => array(
				'title'             => __( 'Project ID', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'description'       => __( 'Enter your project ID', 'tmsm-woocommerce-dialoginsight' ),
				'desc_tip'          => true,
				'default'           => ''
			),

			'list_id' => array(
				'title'             => __( 'Optin Field', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'description'       => __( 'Enter your optin field', 'tmsm-woocommerce-dialoginsight' ),
				'desc_tip'          => true,
				'default'           => ''
			),

			'checkbox_label' => array(
				'title'             => __( 'Checkbox Label', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => __( 'Subscribe to our newsletter', 'tmsm-woocommerce-dialoginsight' )
			),

			'checkbox_action' => array(
				'title'             => __( 'Checkbox Action', 'tmsm-woocommerce-dialoginsight' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => 'woocommerce_after_checkout_billing_form'
			),


		);
	}
}

endif;