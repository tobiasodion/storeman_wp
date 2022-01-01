<?php
   //$json = '{"id":1585542623171,"session":"op-1585377232-5c346840b58849bd06d9eb9a7b5fbaa5","order_number":518,"order_number_format":"#518","title":"","items":[{"id":1585542660560,"name":"ACME","barcode":"00000002272","sub_name":"","dining":"","price":16.53,"price_incl_tax":20,"product_id":2272,"final_price":16.53,"final_price_incl_tax":20,"final_price_source":"","options":[],"bundles":[],"variations":[],"rule_discount":{},"discount_source":"","discount_amount":0,"discount_type":"fixed","final_discount_amount":0,"final_discount_amount_incl_tax":0,"qty":1,"refund_qty":0,"exchange_qty":0,"refund_total":0,"tax_amount":0,"total_tax":3.47,"total":16.53,"total_incl_tax":20,"product":{"name":"ACME","id":2272,"parent_id":2272,"sku":"","qty":null,"manage_stock":false,"stock_status":"instock","barcode":"00000002272","image":"http://localhost.com/dev/openpos/wordpress/wp-content/uploads/2019/03/product-1552879577-436784698.png","price":16.53,"price_incl_tax":20,"final_price":16.53,"special_price":"","regular_price":20,"sale_from":null,"sale_to":null,"status":"publish","categories":[],"tax":[{"code":"standard_1","rate":21,"shipping":"yes","compound":"no","rate_id":1,"label":"Tax","total":3.47}],"tax_amount":3.47,"price_included_tax":1,"group_items":[],"variations":[],"options":[],"bundles":[],"display_special_price":false,"allow_change_price":true,"price_display_html":"<span class=\"woocommerce-Price-amount amount\"><span class=\"woocommerce-Price-currencySymbol\">&#36;</span>20,00</span>","display":true,"str_key":"ACME"},"option_pass":true,"option_total":0,"bundle_total":0,"note":"","parent_id":0,"seller_id":0,"seller_name":"","item_type":"","has_custom_discount":false,"disable_qty_change":false,"read_only":false,"promotion_added":0,"tax_details":[{"code":"standard_1","compound":"no","label":"Tax","rate":21,"rate_id":1,"shipping":"yes","total":3.47}],"custom_fields":[],"is_exchange":false,"update_time":1585542660560}],"sub_total":16.53,"sub_total_incl_tax":20,"tax_amount":3.47,"customer":{"id":0,"group_id":0,"name":"","email":"","address":"","phone":"","point":0,"point_rate":0,"discount":0,"addition_data":{},"shipping_address":[]},"cart_rule_discount":{},"discount_source":"","discount_amount":0,"discount_final_amount":0,"discount_type":"","final_items_discount_amount":0,"final_discount_amount":0,"discount_tax_amount":0,"discount_excl_tax":0,"grand_total":20,"total_paid":0,"discount_code":"","discount_codes":[],"discount_code_amount":0,"discount_code_tax_amount":0,"discount_code_excl_tax":0,"payment_method":[{"name":"Cash","code":"cash","ref":"","description":"","paid":20,"return":0,"paid_point":0,"type":"offline","online_type":"","partial":false,"status_url":"","offline_transaction":"yes","offline_order":"yes"}],"shipping_information":{"shipping_method":"","shipping_title":"","address_id":0,"name":"","email":"","address":"","phone":"","note":"","shipping_method_details":{},"tax_details":[]},"shipping_cost":0,"shipping_tax":0,"shipping_tax_details":[],"sale_person":1,"sale_person_name":"admin","note":"","pickup_time":"","created_at":"3/30/2020, 11:31:11 AM","state":"new","order_state":"","online_payment":false,"print_invoice":false,"point_discount":[],"add_discount":false,"add_shipping":false,"add_tax":false,"custom_tax_rate":0,"custom_tax_rates":[],"tax_details":[{"code":"standard_1","rate":21,"shipping":"yes","compound":"no","rate_id":1,"label":"Tax","total":3.47}],"discount_tax_details":[],"source":{},"source_type":"","available_shipping_methods":[],"mode":"incl_tax","is_takeaway":true,"sync_status":0,"addition_information":{},"email_receipt":"no","checkout_guide":"","privacy_accept":"yes","created_at_time":1585542671673,"order_id":518,"refunds":[],"exchanges":[],"refund_total":0}';
    //$order_data = json_decode($json,true);
?>
<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Email Receipt</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
  /**
   * Google webfonts. Recommended to include the .woff version for cross-client compatibility.
   */
  @media screen {
    @font-face {
      font-family: 'Source Sans Pro';
      font-style: normal;
      font-weight: 400;
      src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
    }

    @font-face {
      font-family: 'Source Sans Pro';
      font-style: normal;
      font-weight: 700;
      src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
    }
  }

  /**
   * Avoid browser level font resizing.
   * 1. Windows Mobile
   * 2. iOS / OSX
   */
  body,
  table,
  td,
  a {
    -ms-text-size-adjust: 100%; /* 1 */
    -webkit-text-size-adjust: 100%; /* 2 */
  }

  /**
   * Remove extra space added to tables and cells in Outlook.
   */
  table,
  td {
    mso-table-rspace: 0pt;
    mso-table-lspace: 0pt;
  }

  /**
   * Better fluid images in Internet Explorer.
   */
  img {
    -ms-interpolation-mode: bicubic;
  }

  /**
   * Remove blue links for iOS devices.
   */
  a[x-apple-data-detectors] {
    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
    color: inherit !important;
    text-decoration: none !important;
  }

  /**
   * Fix centering issues in Android 4.4.
   */
  div[style*="margin: 16px 0;"] {
    margin: 0 !important;
  }

  body {
    width: 100% !important;
    height: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /**
   * Collapse table borders to avoid space between cells.
   */
  table {
    border-collapse: collapse !important;
  }

  a {
    color: #1a82e2;
  }

  img {
    height: auto;
    line-height: 100%;
    text-decoration: none;
    border: 0;
    outline: none;
  }
  </style>

</head>
<body style="background-color: #D2C7BA;">

  <!-- start preheader -->
  <div class="preheader" style="display: none; max-width: 0; max-height: 0; overflow: hidden; font-size: 1px; line-height: 1px; color: #fff; opacity: 0;">
    <?php echo __('You have new order from POS.','openpos');?>
  </div>
  <!-- end preheader -->

  <!-- start body -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%">

    <!-- start logo -->
    <tr>
      <td align="center" bgcolor="#D2C7BA">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
          <tr>
            <td align="center" valign="top" style="padding: 36px 24px;">
              <!--  logo image here  -->
            </td>
          </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
      </td>
    </tr>
    <!-- end logo -->

    <!-- start hero -->
    <tr>
      <td align="center" bgcolor="#D2C7BA">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 36px 24px 0; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;">
              <h1 style="margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;"><?php echo __('Thank you for your order!','openpos');?></h1>
            </td>
          </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
      </td>
    </tr>
    <!-- end hero -->
    <?php if(!$order_data['receipt_url'] ): ?>
    <!-- start copy block -->
    <tr>
      <td align="center" bgcolor="#D2C7BA">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

          <!-- start copy -->
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;"><?php echo __('Here is a summary of your recent order. If you have any questions or concerns about your order.','openpos');?></p>
            </td>
          </tr>
          <!-- end copy -->

          <!-- start receipt table -->
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="left" bgcolor="#D2C7BA" width="75%" colspan="2" style="padding: 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><strong><?php echo __('Order #','openpos');?></strong></td>
                  <td align="left" bgcolor="#D2C7BA" width="25%" style="padding: 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><strong><?php echo $order_data['order_number_format']; ?></strong></td>
                </tr>
                <?php foreach($order_data['items'] as $item):?>
                    <tr>
                        <td align="left" width="50%" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                           <?php echo $item['name']; ?>
                           <?php if($item['sub_name']): ?>
                               <p><?php echo $item['sub_name'];?></p>
                           <?php endif; ?>
                        </td>
                        <td align="left" width="25%" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">x<?php echo $item['qty']; ?></td>
                        <td align="left" width="25%" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo wc_price($item['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if($order_data['add_shipping']): ?>
                <tr>
                  <td align="left" width="75%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo __('Shipping','openpos');?></td>
                  <td align="left" width="25%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo wc_price($order_data['shipping_cost']);?></td>
                </tr>
                <?php endif; ?>
                <tr>
                  <td align="left" width="75%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo __('Sales Tax','openpos');?></td>
                  <td align="left" width="25%" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo wc_price($order_data['tax_amount']);?></td>
                </tr>
                <tr>
                  <td align="left" width="75%" colspan="2" style="padding: 12px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; border-top: 2px dashed #D2C7BA; "><strong><?php echo __('Total','openpos');?></strong></td>
                  <td align="left" width="25%" style="padding: 12px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; border-top: 2px dashed #D2C7BA; "><strong><?php echo wc_price($order_data['grand_total']); ?></strong></td>
                </tr>
                <tr>
                  <td align="left" bgcolor="#D2C7BA" width="75%" colspan="3" style="padding: 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><strong><?php echo __('Payment','openpos');?></strong></td>
                 
                </tr>
                <?php $return_amount = 0; foreach($order_data['payment_method'] as $payment):  if($payment['return'] > 0 ){ $return_amoun += $payment['return']; }  ?>
                <tr>
                  <td align="left" width="75%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo $payment['name'];?></td>
                  <td align="left" width="25%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo wc_price($payment['paid']);?></td>
                </tr>
                <?php endforeach; ?>
                <?php if($return_amount > 0): ?>
                <tr>
                  <td align="left" width="75%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo __('Return','openpos');?></td>
                  <td align="left" width="25%" colspan="2" style="padding: 6px 12px;font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo wc_price($return_amount);?></td>
                </tr>
                <?php endif; ?>
              </table>
            </td>
          </tr>
          <!-- end reeipt table -->

        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
      </td>
    </tr>
    <!-- end copy block -->

    <!-- start receipt address block -->
    <tr>
      <td align="center" bgcolor="#D2C7BA" valign="top" width="100%">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
        <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
          <tr>
            <td align="center" valign="top" style="font-size: 0; border-bottom: 3px solid #d4dadf">
              <!--[if (gte mso 9)|(IE)]>
              <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
              <tr>
              <td align="left" valign="top" width="300">
              <![endif]-->
              <?php if($order_data['shipping_information']): ?>
                    <div style="display: inline-block; width: 100%; max-width: 50%; min-width: 240px; vertical-align: top;">
                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 300px;">
                        <tr>
                            <td align="left" valign="top" style="padding-bottom: 36px; padding-left: 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                            <p><strong><?php echo __('Shipping Information','openpos');?></strong></p>
                            <p><?php echo $order_data['shipping_information']['shipping_title'];?><br>
                            <?php echo $order_data['shipping_information']['name'];?><br>
                            <?php echo $order_data['shipping_information']['email'];?><br>
                            <?php echo $order_data['shipping_information']['phone'];?><br>
                            <?php echo $order_data['shipping_information']['address'];?><br>
                            <?php echo $order_data['shipping_information']['note'];?><br>
                            
                            </p>
                            </td>
                        </tr>
                        </table>
                    </div>
              <?php endif; ?>
              <!--[if (gte mso 9)|(IE)]>
              </td>
              <td align="left" valign="top" width="300">
              <![endif]-->
              <?php if($order_data['customer']): ?>
              <div style="display: inline-block; width: 100%; max-width: 50%; min-width: 240px; vertical-align: top;">
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 300px;">
                  <tr>
                    <td align="left" valign="top" style="padding-bottom: 36px; padding-left: 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                      <p><strong><?php echo __('Billing Address','openpos');?></strong></p>
                      <p>
                        <?php echo $order_data['customer']['name'];?><br>
                        <?php echo $order_data['customer']['email'];?><br>
                        <?php echo $order_data['customer']['phone'];?><br>
                        <?php echo $order_data['customer']['address'];?><br>
                      </p>
                    </td>
                  </tr>
                </table>
              </div>
              <?php endif; ?>
              <!--[if (gte mso 9)|(IE)]>
              </td>
              </tr>
              </table>
              <![endif]-->
            </td>
          </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
      </td>
    </tr>
    <!-- end receipt address block -->
<?php else: ?>
<!-- start receipt receipt url -->

<tr>
      <td align="center" bgcolor="#D2C7BA">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">

          <!-- start copy -->
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;"><?php echo __('Please click link below to view / print your receipt.','openpos');?></p>
            </td>
          </tr>
          <tr>
            <td align="left" bgcolor="#ffffff" style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
              <p style="margin: 0;"><a href="<?php echo esc_url($order_data['receipt_url']); ?>"><?php echo $order_data['receipt_url']; ?></a></p>
            </td>
          </tr>
        </table>
  </tr>

<!-- end receipt receipt url -->
<?php endif; ?>

  </table>
  <!-- end body -->

</body>
</html>