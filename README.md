# Flip for Business

Contributors: flipforbusiness
Tags: flip, flip checkout, online payment, flip woocommerce, flip id
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Flip for Business is a robust plugin that integrates Flip's secure and efficient payment processing directly into your WordPress e-commerce site.

## Description

Flip for Business is a robust plugin that integrates Flip's secure and efficient payment processing directly into your WordPress e-commerce site. With this plugin, you can offer your customers a smooth, reliable payment experience without ever leaving your store.

Key Features:
- Seamless integration with WooCommerce
- Support for various payment methods including bank transfers and e-wallets
- Real-time payment status updates
- Customizable payment interface
- Secure transaction processing
- Support for both Test Mode and Live Mode environments


## Installation

### Requirements

- WordPress 6.2 or higher
- WooCommerce 7.7 or higher
- PHP 7.4 or higher

### Quick Install

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Flip for Business"
4. Click "Install Now" and then "Activate"

### Manual Installation

1. Download the plugin zip file from the releases page
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded zip file and click "Install Now"
5. After installation, click "Activate Plugin"

## Configuration

1. Go to WooCommerce > Settings > Payments
2. Find "Flip for Business" and click "Manage"
3. Enable WooCommerce Flip for Business
4. Choose environment (sandbox or production)
4. Enter your Flip API credentials (available from your Flip dashboard)
5. Enter your Token Validation Key (available from your Flip dashboard)
6. Configure other settings as needed (title, description, expiry time, etc.)
6. Save changes
For detailed configuration instructions, please refer to our documentation.

## Flip Callback Configuration

1. Login to your Flip  Account, select your environment (Test Mode / Live Mode), go to menu Developer Menu > Manage API
2. Insert http://[your web]/?wc-api=FlipForBusiness_Gateway as your Accept Payment URL.
3. Click Save & Test Callback. You should see the result "Callback sent! Callback URL saved."

## Frequently Asked Questions

**Q: Is this plugin free to use?**

A: Yes, the plugin itself is free. However, you'll need a Flip account and may incur transaction fees for payments processed through Flip.

**Q: Can I test the plugin before going live?**
A: Absolutely! Use the sandbox environment to test the integration without real transactions.

**Q: Which payment methods are supported?**

A: Flip supports various payment methods including bank transfers, e-wallets, and more. Check the Flip website for the most up-to-date list.


## Support

For support, please:
1. Check our documentation
2. For critical issues, contact our support team at support@flip.id

## Third-Party Service Integration

This plugin relies on the [Flip](https://flip.id) service for payment processing and related functionalities. When you use this plugin, certain data may be sent to Flip, including but not limited to:

- Payment information
- Flip Api Secret Key for Flip API Authentication

### Data Transmission

The plugin communicates with the following endpoints:

- **Production API**: [https://bigflip.id/api](https://bigflip.id/api)
- **Sandbox API**: [https://bigflip.id/big_sandbox_api](https://bigflip.id/big_sandbox_api)

### Legal and Privacy Information

By using this plugin, you acknowledge that your data may be sent to Flip. We encourage you to review their terms of service and privacy policy:

- [Flip Terms of Service](https://flip.id/business/en/terms-and-conditions)
- [Flip Privacy Policy](https://flip.id/business/en/terms-and-conditions)

This documentation is intended to ensure transparency and provide users with the necessary information regarding data handling practices. If you have any concerns about data privacy or legal implications, please consult the links provided above.

## Contributing

We welcome contributions! Please read our contributing guidelines before submitting pull requests.

## License

This plugin is released under the GPL v2 or later license. See LICENSE for details.

## Changelog

#### 1.0 2024-11-18
Ready to service version 1.0

#### 1.0.0 2024-11-15
Ready to service

#### 0.6
Chore: rename all prefix class name from `WC_*` to `FlipForBusiness_*`

#### 0.5
Fix: domain text mismatch

#### 0.4
Chore: add function comments

#### 0.3
Docs: readme

#### 0.2
Chore: change plugin name from `flip-woocommerce` to `flip-for-business` as contains the restricted term "woocommerce".

#### 0.1 2024-08-06
Original version of the Flip-Woocommerce plugin, not a released version of the plugin, this changelog is here for historical purposes only.

---
For more information about Flip, visit https://flip.id.
Happy selling with Flip and WooCommerce!