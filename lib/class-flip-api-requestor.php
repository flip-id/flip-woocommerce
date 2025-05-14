<?php
if (!defined('ABSPATH')) {
   exit;
}

/**
 * Send request to Flip Api
 */

class FlipForBusiness_Api_Requestor {
   /**
    * Send GET request
    * @param string  $url
    * @param string  $secret_key
    * @param mixed[] $data
    */
   public static function get($url, $secret_key) {
      $response = wp_remote_get($url, array(
         'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($secret_key)
         )
      ));

      return self::handleResponseAndErrorApi($response);
   }

   /**
    * Send Post request
    * @param string  $url
    * @param string  $secret_key
    * @param mixed[] $data
    */
   public static function post($url, $secret_key, $data) {
      $response = wp_remote_post($url, array(
         'body'    => $data,
         'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($secret_key)
         )
      ));

      return self::handleResponseAndErrorApi($response);
   }

   /**
    * Send Post request with Idempotency Key
    * @param string  $url
    * @param string  $secret_key
    * @param mixed[] $data
    * @param int $order_id
    */
   public static function moneyTransfer($url, $secret_key, $data, $order_id = 0) {
      $response = wp_remote_post($url, array(
         'body'    => $data,
         'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'idempotency-key' => '-flip-wc-' .$order_id,
            'X-TIMESTAMP'=> gmdate('c', current_time('timestamp')),
            'Authorization' => 'Basic ' . base64_encode($secret_key)
         )
      ));

      return self::handleResponseAndErrorApi($response);
   }

   /**
    * handle Response/Error Api
    * @param mixed $response
    * @throws \Exception
    * @return mixed
    */
   public static function handleResponseAndErrorApi($response) {
      if (is_wp_error($response)) {
         throw new \Exception(esc_html("WP Error: " . $response->get_error_message()));
      }

      $status_code = wp_remote_retrieve_response_code($response);

      if ($status_code !== 200) {
         // Retrieve the response body
         $result_string = wp_remote_retrieve_body($response);
         
         // Decode the JSON response
         $result = json_decode($result_string);

         if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(esc_html("JSON Decode Error: " . json_last_error_msg() . " | Response: " . $result_string));
         }

         // If there is a message in the response, use it
         if (isset($result->code)) {
            $messages = '';
            foreach ($result->errors as $index => $error) {
               $messages .= $error->message;
               if ($index < count($result->errors) - 1) {
                     $messages .= ', ';
               } else {
                     $messages .= '.';
               }
            }
            
            throw new \Exception(esc_html($messages));
         }else if (isset($result->status) && isset($result->message)) {
            throw new \Exception(esc_html($result->message));
         } else {
            throw new \Exception(esc_html("HTTP Error: " . $status_code . " | Response: " . $result_string));
         }
      }
      
      // Decode the response body
      $result_string = wp_remote_retrieve_body($response);
      $result = json_decode($result_string);

      return $result;
   }

}