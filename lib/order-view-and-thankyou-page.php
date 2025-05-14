<?php
if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

/**
 * Output HTML to display payment/instruction url
 */

global $woocommerce;

//create the order object
$order = new WC_Order( $order_id );
$status = $order->get_status();
$flip_expired = $order->get_meta('_flip_expired_date');
$flip_type_payment = $order->get_meta('_flip_sender_bank_type');
$flip_name_payment = $order->get_meta('_flip_sender_bank_name');
$flip_url_link = $order->get_meta('_flip_link_url');
$flip_expired = $order->get_meta('_flip_expired_date');

// ## Print HTML
?>
<?php if( $order->meta_exists('_flip_link_url') ) : ?>
   <h3>Payment Info</h3>
   <div class="woocommerce-table flip_payment_info">
      <div class="flip_payment_content">
         <div>Payment Status:</div>
         <div><?php echo esc_html(wc_get_order_status_name($status)); ?></div>
      </div>
      <?php if(!empty($flip_type_payment)): ?>
         <div class="flip_payment_content">
            <div>Payment Type:</div>
            <div><?php echo esc_html(ucfirst(FlipForBusiness_Utils::flip_clean_string($flip_type_payment))); ?></div>
         </div>
      <?php endif; ?>
      <?php if(!empty($flip_name_payment)): ?>
         <div class="flip_payment_content">
            <div>Payment Name:</div>
            <div><?php echo esc_html(ucfirst(FlipForBusiness_Utils::flip_clean_string($flip_name_payment))); ?></div>
         </div>
      <?php endif; ?>
      <?php if(!empty($flip_url_link) && $status === "pending" && !empty($flip_expired) || !empty($flip_url_link) && $status === "on-hold" && !empty($flip_expired)): ?>
         <?php if (!FlipForBusiness_Utils::flip_is_expired($flip_expired)): ?>
            <div class="flip-payment-button">
               <?php echo '<a href="'.esc_url($order->get_meta('_flip_link_url')).'">Payment Complete</a>'?>
            </div>
         <?php endif; ?>
      <?php endif; ?>
   </div>
<?php endif; ?>
<?php
// ## End of print HTML