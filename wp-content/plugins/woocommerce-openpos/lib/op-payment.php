<?php
function wc_openpos_offline_add_to_gateways( $gateways ) {
	if(class_exists('WC_Payment_Gateway'))
	{
		$gateways[] = 'OP_Gateway_Offline_Cash';
		$gateways[] = 'OP_Gateway_Offline_Multi';
	}
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'wc_openpos_offline_add_to_gateways' );
add_action( 'plugins_loaded', 'wc_openpos_offline_gateway_init', 11 );

function wc_openpos_offline_gateway_init() {
	if(class_exists('WC_Payment_Gateway'))
	{
		class OP_Gateway_Offline_Cash extends WC_Payment_Gateway {

			/**
			 * Constructor for the gateway.
			 */
			public function __construct() {
		  
				$this->id                 = 'cash';
				$this->icon               = '';
				$this->has_fields         = false;
				$this->method_title       = __( 'Cash', 'openpos' );
				$this->method_description = __( 'Cash method use for POS only.', 'openpos' );
			  
				// Load the settings.
				$this->init_form_fields();
				$this->init_settings();
			  
				// Define user set variables
				$this->title        = $this->method_title ;
				$this->description  = $this->method_description;
				$this->instructions = $this->method_description;
			  
			
			}
			public function is_available() {
				return false;
			}
		} 
		class OP_Gateway_Offline_Multi extends WC_Payment_Gateway {
	
			/**
			 * Constructor for the gateway.
			 */
			public function __construct() {
		  
				$this->id                 = 'pos_multi';
				$this->icon               = '';
				$this->has_fields         = false;
				$this->method_title       = __('Multi Methods','openpos');;
				$this->method_description = __( 'Multi payment method use for POS only.', 'openpos' );
			  
				// Load the settings.
				$this->init_form_fields();
				$this->init_settings();
			  
				// Define user set variables
				$this->title        = $this->method_title ;
				$this->description  = $this->method_description;
				$this->instructions = $this->method_description;
			}
			public function is_available() {
				return false;
			}
		}
	}
}