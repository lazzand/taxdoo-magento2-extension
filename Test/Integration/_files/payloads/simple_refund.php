<?php
/**
 * Taxdoo_VAT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Taxdoo
 * @package    Taxdoo_VAT
 * @copyright  Copyright (c) 2021 Andrea Lazzaretti.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

 use Taxdoo\VAT\Model\Configuration as TaxdooConfig;

$randomRefundNumber = 'TD-Integration-Test-Refund-' . rand(0, 100000);

return [
  'channel' => [
    'identifier' => TaxdooConfig::TAXDOO_MAGENTO_IDENTIFIER,
    'transactionNumber' => $randomTransactionNumber,
    'refundNumber' => $randomRefundNumber
  ],
  'paymentDate' => $now->format(\DateTime::RFC3339),
  'shipping' => -5.0,
  'transactionCurrency' => 'EUR',
  'items' => [[
    'quantity' => 1,
    'description' => 'Sprite Stasis Ball 65 cm',
    'itemPrice' => -27.0,
    'channelItemNumber' => "TD-Integration-Test-Object-001",
    'discount' => 0.0
    ],[
    'quantity' => 1,
    'description' => 'Sprite Foam Yoga Brick',
    'itemPrice' => -5.0,
    'channelItemNumber' => "TD-Integration-Test-Object-002",
    'discount' => 0.0
    ]],
  'paymentChannel' => "Paypal",
  'paymentNumber' => "TD-Integration-Test-Payment-0000001",
  'invoiceNumber' => "TD-Integration-Test-Invoice-0000001",
];
