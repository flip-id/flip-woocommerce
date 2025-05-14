<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FlipForBusiness_Refund_Meta_Box {
   /**
    * Summary of __construct
    */
   public function __construct() {
      $flip_payment_gateway = new FlipForBusiness_Gateway();

      // Check if support refund active
      if (in_array('refunds', $flip_payment_gateway->get_supports())) {
         // Hook Add meta box to the order detail page
         add_action('add_meta_boxes', array( $this, 'flip_add_custom_order_meta_box'));
      }
      
      add_action('wp_ajax_save_flip_refund_meta_box_data', array( $this, 'save_flip_refund_meta_box_ajax'));
   }

   /**
    * Add meta box to the order detail page
    * @return void
    */
   public function flip_add_custom_order_meta_box() {
      add_meta_box(
         'flip_refund_meta_box',
         'Flip Refund: Set Account Payment Customer',
         array( $this, 'flip_refund_order_meta_box'),
         'shop_order',
         'side',
         'default'
      );
   }

   public function flip_refund_order_meta_box($post) {
      $order = wc_get_order($post->ID);
      if ($order->get_payment_method() === "flip") {
         // Get existing values
         $selected_bank = get_post_meta($post->ID, '_flip_account_refund_bank', true);
         $account_number = get_post_meta($post->ID, '_flip_account_refund_number', true);

         // Nonce field for security
         wp_nonce_field('save_flip_refund_meta_box_data', 'flip_refund_meta_box_nonce');

         ?>
            <p>
               <label for="flip_refund_bank"><?php esc_attr_e('Bank/e-Wallet', 'flip-for-business'); ?></label>
               <select name="flip_refund_bank" id="flip_refund_bank" class="widefat flip-select2">
                  <?php
                     foreach (FlipForBusiness_Utils::flip_destination_bank_lists() as $key => $value) {
                        echo '<option value="' . esc_attr($key) . '" ' . selected($selected_bank, $key, false) . '>' . esc_html($value) . '</option>';
                     }
                  ?>
               </select>
            </p>
            <p>
               <label for="flip_refund_number"><?php esc_attr_e('Account Number', 'flip-for-business'); ?></label>
               <input type="text" name="flip_refund_number" id="flip_refund_number" value="<?php echo esc_attr( $account_number ); ?>" class="widefat" />
            </p>
            <p>
               <button type="button" id="save_flip_refund_meta_box" class="button button-primary"><?php esc_attr_e('Save', 'flip-for-business'); ?></button>
            </p>
            <div id="save_status"></div>
         <?php
      }else{
         echo '<p>' . esc_attr_e('Flip refund not available for the selected payment method.', 'flip-for-business') . '</p>';
      }
   }

   /**
    * Hook to handle AJAX request for saving flip meta box data
    */
   public function save_flip_refund_meta_box_ajax() {
      // Check if nonce is set
      if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])) , 'save_flip_refund_meta_box_data')) {
         wp_send_json_error(__('Invalid nonce', 'flip-for-business'));
      }

      // Check if order ID is set
      if (empty($_POST['order_id'])) {
         wp_send_json_error(__('Order ID missing', 'flip-for-business'));
      }

      // Check if order ID is set
      if (empty($_POST['flip_refund_bank'])) {
         wp_send_json_error(__('Bank/e-Wallet missing', 'flip-for-business'));
      }

      // Check if order Account Number Flip
      if (empty($_POST['flip_refund_number'])) {
         wp_send_json_error(__('Account Number missing', 'flip-for-business'));
      }

      $order_id = absint( wp_unslash( $_POST['order_id'] ) );

      // Sanitize and save the bank field
      if (isset($_POST['flip_refund_bank'])) {
         update_post_meta($order_id, '_flip_account_refund_bank', sanitize_text_field(wp_unslash($_POST['flip_refund_bank'])));
      }

      // Sanitize and save the no rek field
      if (isset($_POST['flip_refund_number'])) {
         update_post_meta($order_id, '_flip_account_refund_number', sanitize_text_field(wp_unslash($_POST['flip_refund_number'])));
      }

      // Return success message
      wp_send_json_success(__('Flip Refund: Customer account details saved successfully!', 'flip-for-business'));
   }
}