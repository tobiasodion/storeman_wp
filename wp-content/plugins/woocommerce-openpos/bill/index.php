<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/21/18
 * Time: 12:05
 */
require_once 'protect.php';
global $op_in_bill_screen;
$op_in_bill_screen = true;
$base_dir = dirname(dirname(dirname(dirname(__DIR__))));
require_once ($base_dir.'/wp-load.php');

global $op_register;
$id = esc_attr($_GET['id']);
$register = $op_register->get((int)$id);

#Protect\with('form.php', 'my_password'); //uncomment if you want protect your kitchen screen
?>
<?php if(!empty($register)):  ?>
<html lang="en" style="height: calc(100% - 0px);">
<head>
    <meta charset="utf-8">
    <title>Bill Screen - <?php echo $register['name']; ?></title>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script>
        var data_url = '<?php echo $op_register->bill_screen_file_url($register['id']); ?>';
        var data_template= <?php echo json_encode(array('template' => $op_register->bill_template()));?>;
        
        var lang_obj = {
            'label_cashier': '<?php echo __('Cashier','openpos'); ?>',
            'label_products': '<?php echo __('Products','openpos'); ?>',
            'label_product': '<?php echo __('Product','openpos'); ?>',
            'label_price': '<?php echo __('Price','openpos'); ?>',
            'label_qty': '<?php echo __('Qty','openpos'); ?>',
            'label_total': '<?php echo __('Total','openpos'); ?>',
            'label_grand_total': '<?php echo __('Grand Total','openpos'); ?>'
        };
    </script>
    <?php
    $handes = array(
        'openpos.bill.style'
    );
    wp_print_styles($handes);
    ?>

</head>
<body>
<div  id="bill-content"></div>

<?php
$handes = array(
    'openpos.bill.script'
);
wp_print_scripts($handes);
?>

</body>
</html>
<?php else: ?>
    <h1> <?php echo __('Opppos !!!!','openpos'); ?></h1>
<?php endif; ?>

