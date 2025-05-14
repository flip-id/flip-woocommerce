<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FlipForBusiness_Notify_Handler class.
 * Handles responses from Flip Notification.
 */
class FlipForBusiness_Notify_Handler {
   /**
    * Summary of __construct
    */
   public function __construct() {
      // Register hook for handling HTTP notification (HTTP call to `http://[your web]/?wc-api=FlipForBusiness_Gateway`)
		add_action( 'woocommerce_api_flipforbusiness_gateway', array( $this, 'handleFlipNotificationRequest' ) );
      // Create action to be called when HTTP notification is valid
      // @TODO: rename this hook to use snake_case format
      add_action( 'flipforbusiness_handle_valid_notification', array( $this, 'handleFlipValidNotificationRequest' ) );
      // add_action( 'flipforbusiness_handle_valid_notification-money-transfer', array( $this, 'handleFlipValidNotificationMoneyTransferRequest' ) );
   }

   /**
    * Redirect transacting-user to the finish url set when they were checking out
    * if they were the authorized transacting-user, they will have the finish_url on cookie
    * @TAG: finish_url_user_cookies
   */
   public function checkAndRedirectUserToFinishUrl(){
      if(isset($_COOKIE['wc_flip_last_order_finish_url'])){
         // authorized transacting-user
         wp_redirect(esc_url_raw( wp_unslash($_COOKIE['wc_flip_last_order_finish_url'])));
      }else{
         // else, unauthorized user, redirect to shop homepage by default.
         wp_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
      }
   }
   
   /**
    * Called by hook function when HTTP notification / API call received
    * Handle Flip payment notification
    */
   public function handleFlipNotificationRequest() {
      @ob_clean();
      global $woocommerce;

      $raw_data = [];
      // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Data ini dari API eksternal (Flip) dan tidak memerlukan nonce verification.
      $raw_data['data'] = isset($_POST['data']) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ): null;
      // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Token ini dari API eksternal (Flip) dan tidak memerlukan nonce verification.
      $raw_data['token'] = isset($_POST['token']) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ): null;
      
      // Log request details
      $log_data = [
         'timestamp' => current_time('timestamp'),
         'headers' => getallheaders(),
         'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
         'raw_data' => [
            'data' => $raw_data['data']
         ]
      ];

      FlipForBusiness_Api::fetchAndSetCurrentPluginOptions();

      $response = [
         'success' => false,
         'message' => ''
      ];

      if (empty($raw_data['data']) || empty($raw_data['token'])) {
         $missing_params = [];
         if (empty($raw_data['data'])) {
            $missing_params[] = 'data';
         }
         if (empty($raw_data['token'])) {
            $missing_params[] = 'token';
         }

         $response['message'] = sprintf(
            'Missing required parameters: %s',
            implode(', ', $missing_params)
         );

         $log_data['callback_response'] = $response;
         FlipForBusiness_Logger::log(
            wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'flip-notification',
            'flip',
            current_time('timestamp')
         );
         
         $this->sendResponse(400, $response);
         $this->checkAndRedirectUserToFinishUrl();
         return;
      }

      $result = json_decode(stripslashes($raw_data['data']), true);
      $money_transfer_direction = array(
         'DOMESTIC_TRANSFER',
         'DOMESTIC_SPECIAL_TRANSFER',
         'FOREIGN_INBOUND_SPECIAL_TRANSFER'
      );
      
      if (FlipForBusiness_Api::get_token() != $raw_data['token']) {
         $check_error = array(
            'message' => 'Error: Validation token from settings does not match or is missing. Please check your configuration and try again.'
         );

         $response['message'] = 'Invalid token: Token from settings does not match the received token';
         $log_data['callback_response'] = $response;
         FlipForBusiness_Logger::log(
            wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'flip-notification',
            'flip',
            current_time('timestamp')
         );
         $this->sendResponse(401, $response);
         return;
      }

      // ACCEPT PAYMENT
      if (!empty($result['bill_link_id']) && !empty($result['bill_title']) && wc_get_order((int)$result['bill_title']) != false && FlipForBusiness_Api::get_token() === $raw_data['token']) {
         try {
            $notification_id = $result['id'];
            $get_payment = FlipForBusiness_Api::getPayment($result['bill_link_id']);
            
            if ($get_payment->total_data > 0) {
               $transaction_payment = null;
   
               foreach ($get_payment->data as $transaction) {
                  if (strpos($transaction->id, $notification_id) !== false) {
                     $transaction_payment = $transaction;
                     break;
                  }
               }
   
               if (!empty($transaction_payment)) {
                  do_action("flipforbusiness_handle_valid_notification", $transaction_payment);
                  $response['success'] = true;
                  $response['message'] = sprintf(
                     'Payment notification processed successfully for order #%s',
                     $result['bill_title']
                  );
                  
                  $log_data['callback_response'] = $response;
                  FlipForBusiness_Logger::log(
                     wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                     'flip-notification',
                     'flip',
                     current_time('timestamp')
                  );

                  $this->sendResponse(200, $response);
                  return;
               }
            } else {
               $response['message'] = sprintf(
                  'Payment data not found for bill link ID: %s',
                  $result['bill_link_id']
               );

               $log_data['callback_response'] = $response;
               FlipForBusiness_Logger::log(
                  wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                  'flip-notification',
                  'flip',
                  current_time('timestamp')
               );

               $this->sendResponse(404, $response);
               return;
            }
         } catch (\Throwable $th) {
            $response['message'] = 'Internal server error occurred while processing payment notification';
            $log_data['callback_response'] = $response;
               FlipForBusiness_Logger::log(
                  wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                  'flip-notification',
                  'flip',
                  current_time('timestamp')
               );
            $this->sendResponse(500, $response);
            return;
         }
      }

      $response['message'] = sprintf(
         'Invalid notification data: Missing required fields (bill_link_id, bill_title) or order #%s not found',
         $result['bill_title'] ?? 'unknown'
      );

      $log_data['callback_response'] = $response;
      FlipForBusiness_Logger::log(
         wp_json_encode($log_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
         'flip-notification',
         'flip',
         current_time('timestamp')
      );
      $this->sendResponse(400, $response);
   }

   /**
    * Send JSON response with proper headers
    * 
    * @param int $status_code HTTP status code
    * @param array $data Response data
    * @return void
    */
   private function sendResponse($status_code, $data) {
      status_header($status_code);
      wp_send_json($data, $status_code);
   }

   /**
    * Handle Flip Notification Object, after payment status changes on Flip
    * Will update WC payment status accordingly
    * @param  [Object] $flip_notification Object representation of Flip JSON
    * notification
    * @return void
    */
   public function handleFlipValidNotificationRequest( $flip_notification ) {
      $order_id = (int)$flip_notification->bill_title;
      $order = new WC_Order( $order_id );
      $status_notification = strtolower($flip_notification->status);
      $sender_bank_type = FlipForBusiness_Utils::flip_clean_string($flip_notification->sender_bank_type);
      $sender_bank_name = FlipForBusiness_Utils::flip_clean_string($flip_notification->sender_bank);
      
      /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
      $order->add_order_note(sprintf(__('Flip HTTP notification received: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
      
      update_post_meta($order_id, '_flip_sender_bank_type', sanitize_text_field($flip_notification->sender_bank_type));
      update_post_meta($order_id, '_flip_sender_bank_name', sanitize_text_field($flip_notification->sender_bank));

      switch ($flip_notification->status) {
         case 'SUCCESSFUL':
            $order->payment_complete($flip_notification->id);
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->add_order_note(sprintf(__('Flip payment completed: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            break;
         case 'PENDING':
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->update_status('on-hold',sprintf(__('Awaiting payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->add_order_note(sprintf(__('Awaiting payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            break;
         case 'CANCELLED':
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->update_status('cancelled',sprintf(__('Expired payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->add_order_note(sprintf(__('Expired payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            break;
         case 'FAILED':
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->update_status('failed',sprintf(__('Denied payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
            $order->add_order_note(sprintf(__('Denied payment: Transaction %1$s. Type: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $sender_bank_type, $sender_bank_name));
            break;

         default:
            # code...
            break;
      }
   }

   /**
    * Handle Flip Notification Money Transfer Object, after payment status changes on Flip
    * Will update WC payment status accordingly
    * @param  [array] $flip_notification Object/Array
    * notification
    * @return void
    */
   // public function handleFlipValidNotificationMoneyTransferRequest( $flip_notification ) {
   //    $query = new WC_Order_Query( array(
   //       'limit'        => 1, // -1 for no limit
   //       'meta_key'     => '_flip_refund_transaction_id',
   //       'meta_value'   => $flip_notification['id'],
   //       'meta_compare' => '=', // comparison operator (e.g., '=', '!=', etc.)
   //    ));
      
   //    // Get the results (WC_Order objects)
   //    $orders = $query->get_orders();

   //    if (!empty($orders)) {
   //       $order = $orders[0];
   //       $status_notification = strtolower($flip_notification['status']);
   //       $sender_bank_name = FlipForBusiness_Utils::flip_clean_string($flip_notification['sender_bank']);
         
   //       /* translators: 1: Status notification, 2: Bank type, 3: Band sender */
   //       $order->add_order_note(sprintf(__('Flip HTTP notification money transfer received: Transaction %1$s. Direction: %2$s, Bank: %3$s', 'flip-for-business'), $status_notification, $flip_notification['direction'], $sender_bank_name));
   //       /* translators: 1: Refund price, 2: Refund id, 3: Data Notification, 4: Reason */
   //       $order->add_order_note(sprintf(__('Refunded %1$s - Refund ID: %2$s - Status:  %3$s - Reason: %4$s', 'flip-for-business'), wc_price($flip_notification['amount']), $flip_notification['id'], $status_notification, $flip_notification['reason']));

   //       if ($flip_notification['status'] === "DONE") {
   //           /* translators: 1: Transaction id, 2: Bank type */
   //          $order->update_status('refunded', sprintf(__('Refund payment: Transaction %1$s. Bank: %2$s', 'flip-for-business'), $status_notification, $sender_bank_name));
   //       }
   //    }

   //    exit();
   // }
}