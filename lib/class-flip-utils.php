<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FlipForBusiness_Utils {

   /**
    * Helper for backward compatibility WC v3 & v2 on getting Order Property
    * @param  [String] $order Order Object
    * @param  [String] $property Target property
   */
   public static function getOrderProperty($order, $property){
      $functionName = "get_".$property;
      if (method_exists($order, $functionName)){ // WC v3
         return (string)$order->{$functionName}();
      } else { // WC v2
         return (string)$order->{$property};
      }
   }

   /**
    * Helper Calc time
    * @param mixed $duration
    * @return string
    * */
   public static function calculateTime($time = '15 minutes') {
      $currentTime = new DateTime(current_time('Y-m-d H:i'));
      $currentTime->modify($time);

      return $currentTime->format('Y-m-d H:i');
   }

   /**
    * Add prefix to order ID for Flip API on Flip bill title
    * @param string $order_id
    * @return string
    */
   public static function addFlipOrderPrefix($order_id) {
      $options = get_option('woocommerce_flip_settings');
      $prefix = !empty($options['order_id_prefix']) ? $options['order_id_prefix'] : 'FWOrder-';
      
      // Sanitize prefix to only allow alphanumeric characters, hyphens, and underscores
      $prefix = preg_replace('/[^a-zA-Z0-9_\-]/', '', $prefix);
      
      // Ensure we have a valid prefix
      if (empty($prefix)) {
         $prefix = 'FWOrder-';
      }
      
      return $prefix . $order_id;
   }

   /**
    * Remove prefix from bill title returned by Flip API
    * @param string $bill_title
    * @return string
    */
   public static function removeFlipOrderPrefix($bill_title) {
      $options = get_option('woocommerce_flip_settings');
      $prefix = !empty($options['order_id_prefix']) ? $options['order_id_prefix'] : 'FWOrder-';
      
      // Sanitize prefix to only allow alphanumeric characters, hyphens, and underscores
      $prefix = preg_replace('/[^a-zA-Z0-9_\-]/', '', $prefix);
      
      // Ensure we have a valid prefix
      if (empty($prefix)) {
         $prefix = 'FWOrder-';
      }
      
      if (strpos($bill_title, $prefix) === 0) {
         return substr($bill_title, strlen($prefix));
      }
      return $bill_title;
   }

   /**
    * Data Destination Bank
    */
   public static function flip_destination_bank_lists() {
      $banks = array(
         "harda" => "Allo Bank/Bank Harda Internasional",
         "anz" => "ANZ Indonesia",
         "aceh" => "Bank Aceh Syariah",
         "aladin" => "Bank Aladin Syariah",
         "amar" => "Bank Amar Indonesia",
         "antardaerah" => "Bank Antardaerah",
         "artha" => "Bank Artha Graha Internasional",
         "bengkulu" => "Bank Bengkulu",
         "daerah_istimewa" => "Bank BPD DIY",
         "daerah_istimewa_syr" => "Bank BPD DIY Syariah",
         "btpn_syr" => "Bank BTPN Syariah",
         "bukopin_syr" => "Bank Bukopin Syariah",
         "bumi_arta" => "Bank Bumi Arta",
         "capital" => "Bank Capital Indonesia",
         "bca" => "Bank Central Asia",
         "ccb" => "Bank China Construction Bank Indonesia",
         "cnb" => "Bank CNB (Centratama Nasional Bank)",
         "danamon" => "Bank Danamon & Danamon Syariah",
         "dinar" => "Bank Dinar Indonesia",
         "dki" => "Bank DKI",
         "dki_syr" => "Bank DKI Syariah",
         "ganesha" => "Bank Ganesha",
         "agris" => "Bank IBK Indonesia",
         "ina_perdana" => "Bank Ina Perdana",
         "index_selindo" => "Bank Index Selindo",
         "artos_syr" => "Bank Jago Syariah",
         "jambi" => "Bank Jambi",
         "jambi_syr" => "Bank Jambi Syariah",
         "jasa_jakarta" => "Bank Jasa Jakarta",
         "jawa_tengah" => "Bank Jateng",
         "jawa_tengah_syr" => "Bank Jateng Syariah",
         "jawa_timur" => "Bank Jatim",
         "jawa_timur_syr" => "Bank Jatim Syariah",
         "kalimantan_barat" => "Bank Kalbar",
         "kalimantan_barat_syr" => "Bank Kalbar Syariah",
         "kalimantan_selatan" => "Bank Kalsel",
         "kalimantan_selatan_syr" => "Bank Kalsel Syariah",
         "kalimantan_tengah" => "Bank Kalteng",
         "kalimantan_timur_syr" => "Bank Kaltim Syariah",
         "kalimantan_timur" => "Bank Kaltimtara",
         "lampung" => "Bank Lampung",
         "maluku" => "Bank Maluku",
         "mandiri" => "Bank Mandiri",
         "mantap" => "Bank MANTAP (Mandiri Taspen)",
         "maspion" => "Bank Maspion Indonesia",
         "mayapada" => "Bank Mayapada",
         "mayora" => "Bank Mayora Indonesia",
         "mega" => "Bank Mega",
         "mega_syr" => "Bank Mega Syariah",
         "mestika_dharma" => "Bank Mestika Dharma",
         "mizuho" => "Bank Mizuho Indonesia",
         "mas" => "Bank Multi Arta Sentosa (Bank MAS)",
         "mutiara" => "Bank Mutiara",
         "sumatera_barat" => "Bank Nagari",
         "sumatera_barat_syr" => "Bank Nagari Syariah",
         "nusa_tenggara_barat" => "Bank NTB Syariah",
         "nusa_tenggara_timur" => "Bank NTT",
         "nusantara_parahyangan" => "Bank Nusantara Parahyangan",
         "ocbc" => "Bank OCBC NISP",
         "ocbc_syr" => "Bank OCBC NISP Syariah",
         "america_na" => "Bank of America NA",
         "boc" => "Bank of China (Hong Kong) Limited",
         "india" => "Bank of India Indonesia",
         "tokyo" => "Bank of Tokyo Mitsubishi UFJ",
         "papua" => "Bank Papua",
         "prima" => "Bank Prima Master",
         "bri" => "Bank Rakyat Indonesia",
         "riau_dan_kepri" => "Bank Riau Kepri",
         "sahabat_sampoerna" => "Bank Sahabat Sampoerna",
         "shinhan" => "Bank Shinhan Indonesia",
         "sinarmas" => "Bank Sinarmas",
         "sinarmas_syr" => "Bank Sinarmas Syariah",
         "sulselbar" => "Bank Sulselbar",
         "sulselbar_syr" => "Bank Sulselbar Syariah",
         "sulawesi" => "Bank Sulteng",
         "sulawesi_tenggara" => "Bank Sultra",
         "sulut" => "Bank SulutGo",
         "sumsel_dan_babel" => "Bank Sumsel Babel",
         "sumsel_dan_babel_syr" => "Bank Sumsel Babel Syariah",
         "sumut" => "Bank Sumut",
         "sumut_syr" => "Bank Sumut Syariah",
         "resona_perdania" => "Bank Resona Perdania",
         "victoria_internasional" => "Bank Victoria International",
         "victoria_syr" => "Bank Victoria Syariah",
         "woori" => "Bank Woori Saudara",
         "bca_syr" => "BCA (Bank Central Asia) Syariah",
         "bjb" => "BJB",
         "bjb_syr" => "BJB Syariah",
         "royal" => "Blu/BCA Digital",
         "bni" => "BNI (Bank Negara Indonesia)",
         "bnp_paribas" => "BNP Paribas Indonesia",
         "bali" => "BPD Bali",
         "banten" => "BPD Banten",
         "eka" => "BPR EKA (Bank Eka)",
         "agroniaga" => "BRI Agroniaga",
         "bsm" => "BSI (Bank Syariah Indonesia)",
         "btn" => "BTN",
         "btn_syr" => "BTN Syariah",
         "tabungan_pensiunan_nasional" => "BTPN",
         "cimb" => "CIMB Niaga & CIMB Niaga Syariah",
         "citibank" => "Citibank",
         "commonwealth" => "Commonwealth Bank",
         "chinatrust" => "CTBC (Chinatrust) Indonesia",
         "dbs" => "DBS Indonesia",
         "hsbc" => "HSBC Indonesia",
         "icbc" => "ICBC Indonesia",
         "artos" => "Jago/Artos",
         "hana" => "LINE Bank/KEB Hana",
         "bii" => "Maybank Indonesia",
         "bii_syr" => "Maybank Syariah",
         "mnc_internasional" => "Motion/MNC Bank",
         "muamalat" => "Muamalat",
         "yudha_bakti" => "Neo Commerce/Yudha Bhakti",
         "nationalnobu" => "Nobu (Nationalnobu) Bank",
         "panin" => "Panin Bank",
         "panin_syr" => "Panin Dubai Syariah",
         "permata" => "Permata",
         "permata_syr" => "Permata Syariah",
         "qnb_kesawan" => "QNB Indonesia",
         "rabobank" => "Rabobank International Indonesia",
         "sbi_indonesia" => "SBI Indonesia",
         "kesejahteraan_ekonomi" => "Seabank/Bank BKE",
         "standard_chartered" => "Standard Chartered Bank",
         "super_bank" => "Superbank",
         "uob" => "TMRW/UOB",
         "bukopin" => "Wokee/Bukopin",
         "dana" => "Dana",
         "gopay" => "GoPay",
         "linkaja" => "LinkAja",
         "ovo" => "OVO",
         "shopeepay" => "ShopeePay"
      );

      return $banks;
   }

   /**
    * Helper to delete all characters that are not letters, numbers, or spaces.
    */
   public static function flip_clean_string($string) {
      // Removes all non-alphabetic and non-numeric characters, except spaces.
      $cleaned_string = preg_replace('/[^A-Za-z0-9 ]/', ' ', $string);
      // Replacing underscores (_) with spaces
      $cleaned_string = str_replace('_', ' ', $cleaned_string);
      // Remove excess spaces (if any)
      $cleaned_string = trim(preg_replace('/\s+/', ' ', $cleaned_string));

      return $cleaned_string;
   }

   /**
    * Handle check if order is expired
    * @param DateTime|string $flip_expired
    * @return bool
    */
   public static function flip_is_expired($flip_expired) {
      $status = false;
      $date_now = current_time('Y-m-d H:i');

      // Ubah waktu menjadi timestamp
      $timestamp_flip_expired = strtotime($flip_expired);
      $timestamp_date_now = strtotime($date_now);
      
      if ($timestamp_flip_expired < $timestamp_date_now) {
         $status = true;
      }

      return $status;
   }

   /**
    * Centralized helper function to get Flip link URL from order metadata
    * Enhanced to check Flip API for payment status and update URL if transaction found
    * @param WC_Order $order WooCommerce order object
    * @param bool $paymentCheck Whether to check Flip API for payment status (default: false)
    * @return string|null Flip link URL or null if not exists
    */
   public static function get_flip_link_url($order, $paymentCheck = false) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      $link_url = $order->get_meta('_flip_link_url');
      

      
      // If paymentCheck is false, return the original link_url without API call
      if (!$paymentCheck) {
         return $link_url;
      }
      
      $link_id = $order->get_meta('_flip_link_id');
      
      // If no link_id exists, return the original link_url
      if (empty($link_id)) {
         return $link_url;
      }
      
      try {
         // Check Flip API for payment status
         $payment_response = FlipForBusiness_Api::getPayment($link_id);
         
         // Debug: Log API response
         // if (defined('WP_DEBUG') && WP_DEBUG) {
         //    error_log('Flip API Response for Order ' . $order->get_id() . ': ' . json_encode($payment_response));
         // }
         
         // If payment data exists and has payment_url, update the order metadata
         if (!empty($payment_response->data) && is_array($payment_response->data) && count($payment_response->data) > 0) {
            $payment_data = $payment_response->data[0];
            
            // Debug: Log payment data
            if (defined('WP_DEBUG') && WP_DEBUG) {
               error_log('Flip Payment Data for Order ' . $order->get_id() . ': ' . json_encode($payment_data));
            }
            
            // Update order status if payment is successful and order is pending
            self::update_order_status_if_successful($order, $payment_data);
            
            if (!empty($payment_data->payment_url)) {
               
               // Process and preserve the payment URL properly
               $processed_payment_url = self::process_flip_payment_url($payment_data->payment_url);
               
               // Debug: Log URL processing
               if (defined('WP_DEBUG') && WP_DEBUG) {
                  error_log('Flip URL Processing for Order ' . $order->get_id() . ' - API URL: ' . $payment_data->payment_url);
                  error_log('Flip URL Processing for Order ' . $order->get_id() . ' - Processed URL: ' . $processed_payment_url);
               }
               
               // Update the order metadata with the processed payment URL
               $order->update_meta_data('_flip_link_url', $processed_payment_url);
               $order->save();
               
               return $processed_payment_url;
            }
         }
         
         // If no payment data found, return the original link_url
         if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Flip No Payment Data for Order ' . $order->get_id() . ' - Returning original URL: ' . $link_url);
         }
         
         return $link_url;
         
      } catch (\Exception $e) {
         // If API returns 422 (VALIDATION_ERROR) or any other error, return the original link_url
         // This means customer hasn't chosen payment method yet
         return $link_url;
      }
   }

   /**
    * Centralized helper function to get Flip link ID from order metadata
    * @param WC_Order $order WooCommerce order object
    * @return string|null Flip link ID or null if not exists
    */
   public static function get_flip_link_id($order) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      return $order->get_meta('_flip_link_id');
   }

   /**
    * Centralized helper function to get Flip expired date from order metadata
    * @param WC_Order $order WooCommerce order object
    * @return string|null Flip expired date or null if not exists
    */
   public static function get_flip_expired_date($order) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      return $order->get_meta('_flip_expired_date');
   }

   /**
    * Centralized helper function to check if Flip link URL exists in order metadata
    * @param WC_Order $order WooCommerce order object
    * @return bool True if exists, false otherwise
    */
   public static function has_flip_link_url($order) {
      if (!$order || !is_object($order)) {
         return false;
      }
      
      return $order->meta_exists('_flip_link_url');
   }

   /**
    * Centralized helper function to check if Flip link is valid (exists and not expired)
    * @param WC_Order $order WooCommerce order object
    * @return bool True if valid, false otherwise
    */
   public static function is_flip_link_valid($order) {
      if (!$order || !is_object($order)) {
         return false;
      }
      
      $link_id = self::get_flip_link_id($order);
      $expired_date = self::get_flip_expired_date($order);
      $link_url = self::get_flip_link_url($order);
      
      return !empty($link_id) && 
             !empty($expired_date) && 
             !empty($link_url) && 
             !self::flip_is_expired($expired_date);
   }

   /**
    * Centralized helper function to set Flip metadata on order
    * @param WC_Order $order WooCommerce order object
    * @param string $link_id Flip link ID
    * @param string $link_url Flip link URL
    * @param string $expired_date Flip expired date
    * @return void
    */
   public static function set_flip_metadata($order, $link_id, $link_url, $expired_date) {
      if (!$order || !is_object($order)) {
         return;
      }
      
      $order->update_meta_data('_flip_link_id', $link_id);
      $order->update_meta_data('_flip_link_url', $link_url);
      $order->update_meta_data('_flip_expired_date', $expired_date);
      $order->save();
   }

   /**
    * Check if payment transaction exists on Flip API without updating metadata
    * @param WC_Order $order WooCommerce order object
    * @return object|null Payment data object or null if not found
    */
   public static function check_payment_transaction($order) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      $link_id = $order->get_meta('_flip_link_id');
      
      if (empty($link_id)) {
         return null;
      }
      
      try {
         $payment_response = FlipForBusiness_Api::getPayment($link_id);
         
         if (!empty($payment_response->data) && is_array($payment_response->data) && count($payment_response->data) > 0) {
            return $payment_response->data[0];
         }
         
         return null;
         
      } catch (\Exception $e) {
         return null;
      }
   }

   /**
    * Get payment URL from Flip API if transaction exists
    * @param WC_Order $order WooCommerce order object
    * @return string|null Payment URL or null if not found
    */
   public static function get_payment_url_from_api($order) {
      $payment_data = self::check_payment_transaction($order);
      
      if ($payment_data && !empty($payment_data->payment_url)) {
         return $payment_data->payment_url;
      }
      
      return null;
   }

   /**
    * Update order status to processing if payment is successful and order is pending
    * Idempotent function to prevent duplicate status updates
    * @param WC_Order $order WooCommerce order object
    * @param object $payment_data Payment data from Flip API
    * @return bool True if status was updated, false otherwise
    */
   public static function update_order_status_if_successful($order, $payment_data) {
      if (!$order || !is_object($order) || !$payment_data) {
         return false;
      }
      
      // Check if payment status is SUCCESSFUL
      if (empty($payment_data->status) || strtoupper($payment_data->status) !== 'SUCCESSFUL') {
         return false;
      }
      
      // Check if order is currently pending payment
      if ($order->get_status() !== 'pending') {
         return false;
      }
      
      // Check if this transaction has already been processed (idempotent check)
      $processed_transactions = $order->get_meta('_flip_processed_transactions');
      if (empty($processed_transactions)) {
         $processed_transactions = array();
      } else {
         $processed_transactions = maybe_unserialize($processed_transactions);
      }
      
      $transaction_id = $payment_data->id;
      if (in_array($transaction_id, $processed_transactions)) {
         // Transaction already processed, skip
         return false;
      }
      
      // Update order status to processing
      $order->update_status('processing', sprintf(
         __('Payment completed via Flip. Transaction ID: %s. Bank: %s', 'flip-for-business'),
         $transaction_id,
         FlipForBusiness_Utils::flip_clean_string($payment_data->sender_bank ?? 'Unknown')
      ));
      
      // Mark transaction as processed
      $processed_transactions[] = $transaction_id;
      $order->update_meta_data('_flip_processed_transactions', serialize($processed_transactions));
      
      // Set transaction ID using the new helper function
      self::set_payment_transaction_id($order, $transaction_id);
      
      return true;
   }

   /**
    * Check if transaction has already been processed (idempotent check)
    * @param WC_Order $order WooCommerce order object
    * @param string $transaction_id Transaction ID to check
    * @return bool True if already processed, false otherwise
    */
   public static function is_transaction_processed($order, $transaction_id) {
      if (!$order || !is_object($order) || empty($transaction_id)) {
         return false;
      }
      
      $processed_transactions = $order->get_meta('_flip_processed_transactions');
      if (empty($processed_transactions)) {
         return false;
      }
      
      $processed_transactions = maybe_unserialize($processed_transactions);
      return in_array($transaction_id, $processed_transactions);
   }

   /**
    * Process Flip payment URLs for safe storage
    * Trims whitespace and ensures URL has proper protocol
    * @param string $url The URL to process
    * @return string The processed URL
    */
   public static function process_flip_payment_url($url) {
      if (empty($url)) {
         return $url;
      }
      
      // Ensure URL is properly encoded to preserve special characters
      $url = trim($url);
      
      // If URL doesn't start with http/https, add https://
      if (strpos($url, 'http') !== 0) {
         $url = 'https://' . $url;
      }
      
      return $url;
   }

   /**
    * Get the raw Flip link URL from order metadata
    * Retrieves the URL exactly as stored in the database
    * @param WC_Order $order WooCommerce order object
    * @return string|null The raw URL from database or null if not found
    */
   public static function get_exact_flip_link_url($order) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      // Get the raw URL from the database without any processing
      return $order->get_meta('_flip_link_url');
   }

   /**
    * Set payment transaction ID using multiple WooCommerce methods
    * Uses payment_complete(), custom meta, and set_transaction_id() for compatibility
    * @param WC_Order $order WooCommerce order object
    * @param string $transaction_id Transaction ID to set
    * @return bool True if successful, false otherwise
    */
   public static function set_payment_transaction_id($order, $transaction_id) {
      if (!$order || !is_object($order) || empty($transaction_id)) {
         return false;
      }
      
      try {
         // Method 1: Using payment_complete() (recommended)
         $order->payment_complete($transaction_id);
         
         // Method 2: Also set as meta data for custom tracking
         $order->update_meta_data('_flip_transaction_id', $transaction_id);
         
         // Method 3: Set transaction ID directly (WooCommerce 3.0+)
         if (method_exists($order, 'set_transaction_id')) {
            $order->set_transaction_id($transaction_id);
         }
         
         $order->save();
         return true;
         
      } catch (\Exception $e) {
         return false;
      }
   }

   /**
    * Get payment transaction ID from multiple sources
    * Checks WooCommerce transaction ID first, then custom meta data
    * @param WC_Order $order WooCommerce order object
    * @return string|null Transaction ID or null if not found
    */
   public static function get_payment_transaction_id($order) {
      if (!$order || !is_object($order)) {
         return null;
      }
      
      // Method 1: Get from WooCommerce transaction ID
      $transaction_id = $order->get_transaction_id();
      
      // Method 2: Get from custom meta data
      if (empty($transaction_id)) {
         $transaction_id = $order->get_meta('_flip_transaction_id');
      }
      
      return $transaction_id;
   }
}