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

   function validateOrderIdPrefix() {
      // Add input validation for order_id_prefix
      var prefixField = $('#woocommerce_flip_order_id_prefix');
      
      // Show info about current value when field is focused
      prefixField.on('focus', function() {
         var currentValue = $(this).val();
         if (!$(this).next('.prefix-info').length) {
            $(this).after('<span class="prefix-info" style="color:blue;display:block;margin-top:5px;">Current prefix: "' + currentValue + '". This will be added to all order IDs sent to Flip as Prefix on Bill Title.</span>');
         }
      });
      
      // Remove info when field loses focus
      prefixField.on('blur', function() {
         $('.prefix-info').fadeOut('slow', function() {
            $(this).remove();
         });
      });
      
      prefixField.on('input', function() {
         var input = $(this);
         var sanitized = input.val().replace(/[^a-zA-Z0-9_\-]/g, '');
         
         if (input.val() !== sanitized) {
            input.val(sanitized);
            // Show warning if characters were removed
            if (!input.next('.prefix-warning').length) {
               input.after('<span class="prefix-warning" style="color:orange;display:block;margin-top:5px;">Special characters removed. Only letters, numbers, hyphens (-) and underscores (_) are allowed.</span>');
               setTimeout(function() {
                  $('.prefix-warning').fadeOut('slow', function() {
                     $(this).remove();
                  });
               }, 3000);
            }
         }
      });
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

      // Validate order ID prefix
      validateOrderIdPrefix();

      // Select2 Flip Global
      $(".flip-select2").select2();

      //Handle Customer Account Refund Flip
      handle_account_customer_flip_refund()
   }

   return {
      init : init
   }
})();