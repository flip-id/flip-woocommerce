<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

/**
 * FlipForBusiness_Api class.
 * 
 * Communicates with Flip API.
 */

class FlipForBusiness_Api {
   /**
	 * Api Url.
	 * @var string
	 */
	private static $api_url = '';

   /**
	 * Api Key.
	 * @var string
	 */
	private static $api_key = '';

   /**
	 * Token.
	 * @var string
	 */
	private static $token = '';

	/**
	 * Flip Environment.
	 * @var string
	 */
   private static $environment = '';

   /**
	 * Plugin Options.
	 * @var array
	 */
	private static $plugin_options;

   /**
	 * Payment method id.
	 *
	 * @var string
	 */
	private static $name = 'flip';
	
   const SANDBOX_BASE_URL = 'https://bigflip.id/big_sandbox_api';
	
   const PRODUCTION_BASE_URL = 'https://bigflip.id/api';
   
   /**
	 * Set Api Url.
	 * @param string $Url
	 */
	public static function set_api_url( $api_url ) {
		self::$api_url = $api_url;
   }

   /**
	 * Set Api Key.
	 * @param string $key
	 */
	public static function set_api_key( $api_key ) {
		self::$api_key = $api_key;
   }

   /**
	 * Set Api Key.
	 * @param string $key
	 */
	public static function set_token( $token ) {
		self::$token = $token;
   }

	/**
	 * Set Flip Environment.
	 * @param string $key
	 */
	public static function set_environment( $environment ) {
		self::$environment = $environment;
   }

   /**
	 * Get Api Url.
	 * @return string
	 */
	public static function get_api_url() {
		if ( ! self::$api_url ) {
         self::set_api_url( self::get_environment() == 'production' ? self::PRODUCTION_BASE_URL : self::SANDBOX_BASE_URL );
		}

		return self::$api_url;
	}

   /**
	 * Get Api Key.
	 * @return string
	 */
	public static function get_api_key() {
		if ( ! self::$api_key ) {
			$plugin_options = self::$plugin_options;
			if ( isset( $plugin_options['api_key_production'], $plugin_options['api_key_sandbox'] ) ) {
				self::set_api_key( self::get_environment() == 'production' ? $plugin_options['api_key_production']. ':' : $plugin_options['api_key_sandbox']. ':' );
			}
		}
		return self::$api_key;
	}

   /**
	 * Get Token.
	 * @return string
	 */
	public static function get_token() {
		if ( ! self::$token ) {
			$plugin_options = self::$plugin_options;
			if ( isset( $plugin_options['token_validation_production'], $plugin_options['token_validation_sandbox'] ) ) {
				self::set_token( self::get_environment() == 'production' ? $plugin_options['token_validation_production'] : $plugin_options['token_validation_sandbox'] );
			}
		}
		return self::$token;
	}

   /**
	 * Get Flip Environment.
	 * @return string
	 */
	public static function get_environment() {
		if ( ! self::$environment ) {
			$plugin_options = self::$plugin_options;
			if ( isset( $plugin_options['environment'] ) ) {
				self::set_environment( $plugin_options['environment'] );
			}
		}
		return self::$environment;
	}

	/**
	 * Fetch Set as self/private variable
	 */
	public static function fetchAndSetCurrentPluginOptions () {
		return self::$plugin_options = get_option( 'woocommerce_'. self::$name .'_settings' );
	}
	
   /**
    * Create Accept Payment, auto handle exception
    * @param  object $order the WC Order instance.
    * @param  object $params Payment options.
    * @return object show response.
    * @throws Exception curl error or flip error.
    * */
   public static function createBillTransaction( $order, $params) {
      self::fetchAndSetCurrentPluginOptions();
		$args = array(
			'title'                    => FlipForBusiness_Utils::addFlipOrderPrefix($params['transaction_details']['order_id']),
			'type'                     => "SINGLE",
			'amount'                   => (float)FlipForBusiness_Utils::getOrderProperty($order, 'total'),
			'expired_date'             => $params['expiry'],
			'redirect_url'             => $params['redirect_thankyou_url'],
			'is_address_required'      => !empty($params['customer_details']['address']) ? 1 : 0,
			'is_phone_number_required' => !empty($params['customer_details']['phone']) ? 1 : 0,
			'step'                     => 2,
			'sender_name'              => $params['customer_details']['first_name'].' '.$params['customer_details']['last_name'],
			'sender_email'             => $params['customer_details']['email'],
			'sender_phone_number'      => $params['customer_details']['phone'],
			'sender_address'           => $params['customer_details']['address'],
			'charge_fee'			   => self::$plugin_options['charge_fee'] === 'yes' ? 1 : 0
		);

		try {
			// var_dump(self::get_api_url().'/v2/pwf/bill', self::get_api_key(), $args);

			$response = FlipForBusiness_Api_Requestor::post(self::get_api_url().'/v2/pwf/bill', self::get_api_key(), $args );
		} catch (\Exception $e) {
			throw $e;
		}

		// $response = (object)[
		// 	"link_id" => $params['transaction_details']['order_id'],
		// 	// "link_url" => $params['redirect_url'],
		// 	"link_url" => 'https://flip.id/pwf-sandbox/$mineral/#test-7348',
		// 	"title" => $params['transaction_details']['order_id'],
		// 	"type" => "SINGLE",
		// 	"amount" => 1000,
		// 	"redirect_url" => $params['redirect_thankyou_url'],
		// 	"expired_date" => "2024-09-19 18:05",
		// 	"created_from" => "API",
		// 	"status" => "ACTIVE",
		// 	"is_address_required" => 0,
		// 	"is_phone_number_required" => 0,
		// 	"step" => 2,
		// 	"customer" => (object)[
		// 		"name" => "FLIP TEST",
		// 		"email" => "hello@mineral.co.id",
		// 		"address" => NULL,
		// 		"phone" => NULL
		// 	]
		// ];
		
		$data = array(
			'args' => $args,
			'response' => $response
		);

		FlipForBusiness_Logger::log(wp_json_encode($data), 'flip-accept-payment', 'flip', current_time('timestamp'));
		
		return $response;
	}

   /**
    * Create Money Transfer, auto handle exception
    * @param  mixed $data instance.
    * @return int $order_id for idempotency key.
    * @return object show response.
    * @throws Exception curl error or flip error.
    * */
   public static function createRefund( $data, $order_id ) {
      self::fetchAndSetCurrentPluginOptions();

		try {
			$response = FlipForBusiness_Api_Requestor::moneyTransfer(self::get_api_url().'/v3/disbursement', self::get_api_key(), $data, $order_id );
		} catch (\Exception $e) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Get Data Payment Flip, auto handle exception
	 * @param int $bill_id
	 * @return mixed
	 * @throws Exception curl error or flip error.
	 */
   public static function getPayment( $bill_id ) {
      self::fetchAndSetCurrentPluginOptions();

		try {
			$response = FlipForBusiness_Api_Requestor::get(
				self::get_api_url().'/v2/pwf/'.$bill_id.'/payment',
				self::get_api_key()
			);
		} catch (\Exception $e) {
			throw $e;
		}

		$data = array(
			'args' => $bill_id,
			'response' => $response
		);

		FlipForBusiness_Logger::log(wp_json_encode($data), 'flip-accept-payment', 'flip', current_time('timestamp'));

		return $response;
	}
}