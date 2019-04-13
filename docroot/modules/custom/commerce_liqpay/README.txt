# Drupal module: Commerce Liqpay Gateway
This module provides a Drupal Commerce payment method to embed the payment
services provided by LiqPay.

It efficiently integrates payments from various sources such as:
- credit cards;
- cash via self-service terminals (offline payments);
- email receipts;
- privat24 banking or liqpay accounts.

This module use LiqPay API v3.0.

## Required dependencies

- [Token](http://drupal.org/project/token)
- Commerce Payment (from [Commerce](http://drupal.org/project/commerce) core)
- Commerce Order (from [Commerce](http://drupal.org/project/commerce) core)

## Installation / Configuration

1. Install the Commerce LiqPay Gateway module by copying the sources
   to a modules directory, such as `modules/contrib` or `modules`.
2. In your Drupal site, enable the module.
3. Add payment gateway at setting page and configure your API keys:
   Commerce -> Configuration -> Payment gateways -> Add payment gateway
