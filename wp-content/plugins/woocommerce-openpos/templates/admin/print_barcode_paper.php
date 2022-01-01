<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
    global $OPENPOS_SETTING;
    global $OPENPOS_CORE;
    global $barcode_generator;
    global $unit;
    global $mode;

    global $_barcode_width;
    global $_barcode_height;

    $setting_sheet_width = $OPENPOS_SETTING->get_option('sheet_width','openpos_label');
    $setting_sheet_height = $OPENPOS_SETTING->get_option('sheet_height','openpos_label');
    $setting_sheet_padding_top = $OPENPOS_SETTING->get_option('sheet_margin_top','openpos_label');
    $setting_sheet_padding_right = $OPENPOS_SETTING->get_option('sheet_margin_right','openpos_label');
    $setting_sheet_padding_bottom = $OPENPOS_SETTING->get_option('sheet_margin_bottom','openpos_label');
    $setting_sheet_padding_left = $OPENPOS_SETTING->get_option('sheet_margin_left','openpos_label');
    $setting_vertical_space = $OPENPOS_SETTING->get_option('sheet_vertical_space','openpos_label');
    $setting_horizontal_space = $OPENPOS_SETTING->get_option('sheet_horizontal_space','openpos_label');
    $setting_label_width = $OPENPOS_SETTING->get_option('barcode_label_width','openpos_label');
    $setting_label_height = $OPENPOS_SETTING->get_option('barcode_label_height','openpos_label');

    $setting_label_padding_top = $OPENPOS_SETTING->get_option('barcode_label_padding_top','openpos_label');
    $setting_label_padding_right = $OPENPOS_SETTING->get_option('barcode_label_padding_right','openpos_label');
    $setting_label_padding_bottom = $OPENPOS_SETTING->get_option('barcode_label_padding_bottom','openpos_label');
    $setting_label_padding_left = $OPENPOS_SETTING->get_option('barcode_label_padding_left','openpos_label');

    $setting_barcode_width = $OPENPOS_SETTING->get_option('barcode_width','openpos_label');
    $setting_barcode_height = $OPENPOS_SETTING->get_option('barcode_height','openpos_label');

    $setting_unit = $OPENPOS_SETTING->get_option('unit','openpos_label');



    $is_preview = isset($_REQUEST['is_preview']) && $_REQUEST['is_preview'] == 1 ? true : false;
    $product_id_str = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : '';
    $product_ids = explode(',',$product_id_str);
    //sheet
    $sheet_width = isset($_REQUEST['sheet_width']) ? floatval($_REQUEST['sheet_width']) : $setting_sheet_width;
    $sheet_height = isset($_REQUEST['sheet_height']) ? floatval($_REQUEST['sheet_height']) : $setting_sheet_height;
    $sheet_padding_top = isset($_REQUEST['sheet_margin_top']) ? floatval($_REQUEST['sheet_margin_top']) : $setting_sheet_padding_top;
    $sheet_padding_right = isset($_REQUEST['sheet_margin_right']) ? floatval($_REQUEST['sheet_margin_right']) : $setting_sheet_padding_right;
    $sheet_padding_bottom = isset($_REQUEST['sheet_margin_bottom']) ? floatval($_REQUEST['sheet_margin_bottom']) : $setting_sheet_padding_bottom;
    $sheet_padding_left = isset($_REQUEST['sheet_margin_left']) ? floatval($_REQUEST['sheet_margin_left']) : $setting_sheet_padding_left;
    $vertical_space = isset($_REQUEST['sheet_vertical_space']) ? floatval($_REQUEST['sheet_vertical_space']) : $setting_vertical_space;
    $horizontal_space = isset($_REQUEST['sheet_horizontal_space']) ? floatval($_REQUEST['sheet_horizontal_space']) : $setting_horizontal_space;
   
    //label
    $label_width = isset($_REQUEST['barcode_label_width']) ? floatval($_REQUEST['barcode_label_width']) : $setting_label_width;
    $label_height = isset($_REQUEST['barcode_label_height']) ? floatval($_REQUEST['barcode_label_height']) : $setting_label_height;
    $label_margin_top = isset($_REQUEST['barcode_label_padding_top']) ? floatval($_REQUEST['barcode_label_padding_top']) : $setting_label_padding_top;
    $label_margin_right = isset($_REQUEST['barcode_label_padding_right']) ? floatval($_REQUEST['barcode_label_padding_right']) : $setting_label_padding_right;
    $label_margin_bottom = isset($_REQUEST['barcode_label_padding_bottom']) ? floatval($_REQUEST['barcode_label_padding_bottom']) : $setting_label_padding_bottom;
    $label_margin_left = isset($_REQUEST['barcode_label_padding_left']) ? floatval($_REQUEST['barcode_label_padding_left']) : $setting_label_padding_left;

    $barcode_width = isset($_REQUEST['barcode_width']) ? floatval($_REQUEST['barcode_width']) : $setting_barcode_width;
    $barcode_height = isset($_REQUEST['barcode_height']) ? floatval($_REQUEST['barcode_height']) : $setting_barcode_height;
    //other
    $unit = isset($_REQUEST['unit']) ? sanitize_text_field($_REQUEST['unit']) : $setting_unit;
    $total = isset($_REQUEST['total']) ? intval($_REQUEST['total']) : -1;
    //calc

    $sheet_space_width = $sheet_width - $sheet_padding_left - $sheet_padding_right  ;
    $sheet_space_height = $sheet_height - $sheet_padding_top - $sheet_padding_bottom;

    $columns = floor($sheet_space_width / ($label_width + $vertical_space)) ;
    
    if($vertical_space > $label_width )
    {
        $columns += 1;
    }
   

    
    $rows = floor($sheet_space_height / ($label_height + $horizontal_space));
    if($horizontal_space > $label_height )
    {
        $rows += 1;
    }
   

    $truth_label_width = $label_width;
    $truth_label_height = $label_height;

    if($rows == 0){ $rows = 1; }
    if($columns == 0){$columns = 1;}
    $label_per_sheet = $rows * $columns;
    
    $count = 0;

    $_barcode_width = $barcode_width;
    $_barcode_height = $barcode_height;

    $templates = array();
    if($total == -1)
    {
        $total = 0;
        foreach($product_ids as $product_id)
        {
            if($product_id)
            {
                $_op_product = wc_get_product(intval($product_id));
                $qty = apply_filters('op_product_label_qty',1,$_op_product);
                $total += $qty;
                for($i = 0;$i < $qty ; $i++)
                {
                    $templates[] = balanceTags(do_shortcode($OPENPOS_SETTING->get_option('barcode_label_template','openpos_label')),true);
                }
            }
        }
       
    }else{
        foreach($product_ids as $product_id)
        {
            if($product_id)
            {
                $_op_product = wc_get_product(intval($product_id));
                $templates[] = balanceTags(do_shortcode($OPENPOS_SETTING->get_option('barcode_label_template','openpos_label')),true);
                //$template = balanceTags(do_shortcode($OPENPOS_SETTING->get_option('barcode_label_template','openpos_label')),true);
            }
        }
    }
    $page = ceil($total / $label_per_sheet);
    $template_count = 0;
    
?>
<?php ob_start(); ?>
<body style="background-color: transparent;padding:0;margin:0;">
    <?php for($k = 1;$k <= $page;$k++): ?>
    <div style="width: <?php echo $sheet_width.$unit;?>;height:<?php echo $sheet_height.$unit; ?>;  display: block; overflow: hidden; background-color: transparent;" class="sheet">
        <div style="display: block; overflow: hidden;background-color: transparent; margin-left:<?php echo $sheet_padding_left.$unit; ?>;margin-right:<?php echo $sheet_padding_right.$unit; ?>;margin-top:<?php echo $sheet_padding_top.$unit; ?>;margin-bottom:<?php echo $sheet_padding_bottom.$unit; ?>;">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="label-row" style="margin-bottom: <?php echo ($i != ($rows - 1)) ? $horizontal_space.$unit:0;?>; display: block;width: <?php echo $sheet_space_width.$unit;?>;">
                <?php for($j = 0; $j < $columns; $j++): $count++; ?>
                    <?php
                        $template = $templates[$template_count];
                        if($template_count == (count($templates) - 1 ))
                        {
                            $template_count = 0;
                        }else{
                            $template_count ++;

                        }
                    ?>
                    <div class="label  <?php echo $count; ?>"  style=" text-align: center;overflow: hidden; width: <?php echo $truth_label_width.$unit; ?>;height: <?php echo $truth_label_height.$unit; ?>; display: inline-block;overflow: hidden; " >
                        <div class="label-element-container">
                        <?php echo $template; ?>
                        </div>
                    </div>
                    <?php if( $j < ($columns - 1)): ?>
                    <div class="label-vertical-space" style="width: <?php echo $vertical_space.$unit; ?>;height: <?php echo $truth_label_height.$unit; ?>; display: inline-block;overflow: hidden;"></div>
                    <?php endif; ?>
                <?php if($count == $total){ break; }  endfor; ?>
            </div>
        <?php if($count == $total){ break; }  endfor; ?>
        </div>
    </div>

    <?php if($count == $total){ break; }  endfor; ?>
</body>
<?php
$out2 = ob_get_contents();

ob_end_clean();
$buffer = preg_replace('/\s+/', ' ', $out2);


$search = array(
    '/\>[^\S ]+/s',
    '/[^\S ]+\</s',
    '/(\s)+/s'
);
$replace = array(
    '>',
    '<',
    '\\1'
);
if (preg_match("/\<html/i",$buffer) == 1 && preg_match("/\<\/html\>/i",$buffer) == 1) {
    $buffer = preg_replace($search, $replace, $buffer);
}
$buffer = str_replace('> <', '><', $buffer);
?>
<html>
<head>
    <title>barcode</title>
    <?php if(!$is_preview): ?>
        <script type="application/javascript">
            window.print();
        </script>
        <style media="print">
            @page {
                size: <?php echo $sheet_width.$unit;?> <?php echo $sheet_height.$unit; ?>;
                padding:0;
                

            }
            .sheet{
                width: 100%;
            }
            .label{
                overflow: hidden;
            }
        </style>
    <?php else: ?>
    
        <style media="all">
            @page {
                size: <?php echo $sheet_width.$unit;?> <?php echo $sheet_height.$unit; ?>;
                padding:0;
                overflow: hidden;
            }
            .sheet{
                width: 100%;
                background-color: #FFEB3B!important;
            }
            .label{
                overflow: hidden;
                background-color:#fff;
                border-radius: 5px;
            }

        </style>
    <?php endif; ?>
    
</head>
<?php echo $buffer; ?>
</html>
