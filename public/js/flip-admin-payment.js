var $ = jQuery;

document.addEventListener('DOMContentLoaded', function() {
   composite.init();
});


var composite = (function(){
   function toggleApiKeyFields() {
      var environment = $('#woocommerce_flip_environment').val();
      if (environment === 'sandbox') {
         $('#woocommerce_flip_api_key_sandbox').closest('tr').show();
         $('#woocommerce_flip_api_key_production').closest('tr').hide();
         $('#woocommerce_flip_token_validation_sandbox').closest('tr').show();
         $('#woocommerce_flip_token_validation_production').closest('tr').hide();
      } else {
         $('#woocommerce_flip_api_key_sandbox').closest('tr').hide();
         $('#woocommerce_flip_api_key_production').closest('tr').show();
         $('#woocommerce_flip_token_validation_sandbox').closest('tr').hide();
         $('#woocommerce_flip_token_validation_production').closest('tr').show();
      }
   }

   function handle_account_customer_flip_refund() {
      $('#save_flip_refund_meta_box').on('click', function() {

         var flip_refund_bank = $('#flip_refund_bank').val();
         var flip_refund_number = $('#flip_refund_number').val();
         var order_id = $('#post_ID').val();
         var nonce = $('#flip_refund_meta_box_nonce').val();

         // Prepare data for the AJAX request
         var data = {
            action: 'save_flip_refund_meta_box_data',
            order_id: order_id,
            flip_refund_bank: flip_refund_bank,
            flip_refund_number: flip_refund_number,
            nonce: nonce
         };

         // Perform the AJAX request
         $.post(ajaxurl, data, function(response) {
            if (response.success) {
               $('#save_status').html('<p style="color:green;">' + response.data + '</p>');
            } else {
               $('#save_status').html('<p style="color:red;">' + response.data + '</p>');
            }
         });
      });
   }

   function init() {
      // flip admin setting
      toggleApiKeyFields()
      $('#woocommerce_flip_environment').change(function() {
         toggleApiKeyFields();
      });

      // Select2 Flip Global
      $(".flip-select2").select2();

      //Handle Customer Account Refund Flip
      handle_account_customer_flip_refund()
   }

   return {
      init : init
   }
})();