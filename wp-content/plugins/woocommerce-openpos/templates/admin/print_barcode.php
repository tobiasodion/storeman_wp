<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
global $OPENPOS_SETTING;
global $op_warehouse;
$sheet_width = $OPENPOS_SETTING->get_option('sheet_width','openpos_label');
$sheet_height = $OPENPOS_SETTING->get_option('sheet_height','openpos_label');
$sheet_padding_top = $OPENPOS_SETTING->get_option('sheet_margin_top','openpos_label');
$sheet_padding_right = $OPENPOS_SETTING->get_option('sheet_margin_right','openpos_label');
$sheet_padding_bottom = $OPENPOS_SETTING->get_option('sheet_margin_bottom','openpos_label');
$sheet_padding_left = $OPENPOS_SETTING->get_option('sheet_margin_left','openpos_label');
$vertical_space = $OPENPOS_SETTING->get_option('sheet_vertical_space','openpos_label');
$horizontal_space = $OPENPOS_SETTING->get_option('sheet_horizontal_space','openpos_label');
$label_width = $OPENPOS_SETTING->get_option('barcode_label_width','openpos_label');
$label_height = $OPENPOS_SETTING->get_option('barcode_label_height','openpos_label');

$label_padding_top = $OPENPOS_SETTING->get_option('barcode_label_padding_top','openpos_label');
$label_padding_right = $OPENPOS_SETTING->get_option('barcode_label_padding_right','openpos_label');
$label_padding_bottom = $OPENPOS_SETTING->get_option('barcode_label_padding_bottom','openpos_label');
$label_padding_left = $OPENPOS_SETTING->get_option('barcode_label_padding_left','openpos_label');

$barcode_width = $OPENPOS_SETTING->get_option('barcode_width','openpos_label');
$barcode_height = $OPENPOS_SETTING->get_option('barcode_height','openpos_label');
$barcode_label_template = $OPENPOS_SETTING->get_option('barcode_label_template','openpos_label');
$unit = $OPENPOS_SETTING->get_option('unit','openpos_label');
$default_qty = 30;
$product_id = isset($_GET['id']) ? 1*$_GET['id'] : 0;
if($product_id)
{
    $total_qty = $op_warehouse->get_total_qty($product_id);
    if($total_qty && $total_qty > 0)
    {
        $default_qty = $total_qty;
    }
}

$qty = isset($_GET['qty']) ? 1 * $_GET['qty'] : apply_filters( 'op_print_label_default_qty', $default_qty);
?>
<div class="wrap">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    
    <p  class="page-label"> <?php echo __( 'Barcode Label Composer', 'openpos' ); ?></p>
    <div class="label-container">
    <div class="label-setting">
        <form method="post" id="label-setting-frm">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="form-row" style="width: 100%;margin: 0 auto;">
                <table style="width: 100%;">
                    <tr>
                        <th><?php echo __( 'Unit:', 'openpos' ); ?></th>
                        <td>
                            <select name="unit">
                                <option value="in" <?php echo ($unit == 'in')? 'selected':''; ?>><?php echo __( 'Inch', 'openpos' ); ?></option>
                                <option value="mm" <?php echo ($unit == 'mm')? 'selected':''; ?>><?php echo __( 'Millimeter', 'openpos' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Sheet Width x Height:', 'openpos' ); ?></th>
                        <td><input type="text" value="<?php echo $sheet_width; ?>" name="sheet_width"> x <input name="sheet_height" type="text" value="<?php echo $sheet_height; ?>"></td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Vertical Spacing:', 'openpos' ); ?></th>
                        <td><input type="text" name="sheet_vertical_space" value="<?php echo $vertical_space; ?>"></td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Horizontal Spacing:', 'openpos' ); ?></th>
                        <td><input type="text" name="sheet_horizontal_space"  value="<?php echo $horizontal_space; ?>"></td>
                    </tr>


                    <tr>
                        <th><?php echo __( 'Sheet Margin (top x right x bottom x left):', 'openpos' ); ?></th>
                        <td>
                            <input type="text" name="sheet_margin_top"   value="<?php echo $sheet_padding_top; ?>"> x
                            <input type="text" name="sheet_margin_right" value="<?php echo $sheet_padding_right; ?>"> x
                            <input type="text" name="sheet_margin_bottom" value="<?php echo $sheet_padding_bottom; ?>"> x
                            <input type="text" name="sheet_margin_left" value="<?php echo $sheet_padding_left; ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo __( 'Label Size (w x h):', 'openpos' ); ?></th>
                        <td>
                            <input type="text" name="barcode_label_width" value="<?php echo $label_width; ?>"> x <input name="barcode_label_height" type="text" value="<?php echo $label_height; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Label Padding (top x right x bottom x left):', 'openpos' ); ?></th>
                        <td>
                            <input type="text" name="barcode_label_padding_top"   value="<?php echo $label_padding_top; ?>"> x
                            <input type="text" name="barcode_label_padding_right" value="<?php echo $label_padding_right; ?>"> x
                            <input type="text" name="barcode_label_padding_bottom" value="<?php echo $label_padding_bottom; ?>"> x
                            <input type="text" name="barcode_label_padding_left" value="<?php echo $label_padding_left; ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo __( 'Barcode Image Size ( w x h ):', 'openpos' ); ?></th>
                        <td>
                            <input type="text" name="barcode_width" value="<?php echo $barcode_width; ?>"> x <input name="barcode_height" type="text" value="<?php echo $barcode_height; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Template:', 'openpos' ); ?></th>
                        <td>
                            <textarea style="width: 100%;min-height:150px;" name="barcode_label_template"><?php echo $barcode_label_template; ?></textarea>
                            <p class="help">
                                <?php echo __( 'use [barcode with="" height=""] to adjust barcode image, [op_product attribute="attribute_name"] with attribute name: <b>name, price ,regular_price, sale_price, width, height,length,weight</b> and accept html,inline style css string', 'openpos' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __( 'Number Of Label:', 'openpos' ); ?></th>
                        <td><input type="number" name="total" value="<?php echo $qty; ?>"></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <button type="button"  id="preview-label-btn"><?php echo __( 'Save And Preview', 'openpos' ); ?></button>
                            <button type="button" id="print-label-btn" name="print" ><?php echo __( 'Print', 'openpos' ); ?></button>
                            
                        </td>
                    </tr>
                </table>

            </div>
        </form>
    </div>
    <div class="preview-live">
        <iframe id="preview-frame" style="width:calc(100% - 1px);height:100%;min-height:490px;    background: #fff;
        border: none;" src=""><?php echo __( 'Preview', 'openpos' ); ?></iframe>
    </div>
    </div>


</div>

<style>
    #print-label-btn{
        border: solid 1px #000;
        padding:  5px 7px;
        text-transform: uppercase;
        margin-top: 15px;
        background:  #000;
        color: #fff;
    }
    #preview-label-btn{
        border: solid 1px blue;
        padding:  5px 7px;
        text-transform: uppercase;
        margin-top: 15px;
        background:  blue;
        color: #fff;
    }
    form input{
        width: 100px;
        text-align: right;
        padding: 5px 2px;
    }
    .form-row tr:nth-child(odd){
        background: #e6e6e6;
    }
    .form-row tr td{
        padding: 5px;
    }
    .form-row th{
        text-align: left;
        font-size:10px;
        padding-left: 5px;
    }
    .label-setting{
        width: calc(50% - 2px);
        float: left;
        overflow: auto;
        background: #ccc;
        padding: 5px ;
        min-height: 500px;
        border:solid 1px #ccc;
    }
    .preview-live{
        float: left;
        height: fit-content;
        display: block;
        width: calc(50% - 2px);
        overflow: auto;
        min-height: 500px;
        border:solid 1px #00BCD4;
        padding: 5px 0;
        background: #00BCD4;
    }
    .page-label{
        font-size: 20px;
        font-weight: bold;
        padding: 20px 0;
    }
    .label-setting input{
        width: 60px;
    }
</style>
<script type="text/javascript">
    (function($) {
        var form_values = 'product_id=<?php echo (int)$_GET['id']; ?>';// $('#label-setting-frm').serialize();
            let total = $('#label-setting-frm').find('input[name="total"]').first().val();
            total = 1 * total;
            if(!total)
            {
                total = 1;
            }
            form_values += '&total='+total;
            form_values += "&is_preview=1&is_print=0&action=print_barcode" ;
            var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?'+form_values;
            $('#preview-frame').attr('src',frame_url);


        $('body').on('click','#preview-label-btn',function () {
                var _form_values_save = $('#label-setting-frm').serialize();
                var _form_values = 'product_id=<?php echo (int)$_GET['id']; ?>';
                let total = $('#label-setting-frm').find('input[name="total"]').first().val();
                total = 1 * total;
                if(!total)
                {
                    total = 1;
                }
                _form_values += '&total='+total;
                
                form_values = _form_values+"&is_preview=1&is_print=0&action=print_barcode" ;

                $.ajax({
                        url: '<?php echo admin_url( 'admin-ajax.php?action=save_bacode_setting' ); ?>',
                        type: 'post',
                        dataType: 'json',
                        data: _form_values_save,
                        beforeSend:function(){
                            $('body').addClass('op_loading');
                        },
                        success:function(data){
                           
                            $('body').removeClass('op_loading');
                            var  t = new Date().getTime();
                            var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?t'+t+'&'+form_values;
                            $('#preview-frame').attr('src',frame_url);
                        }
                });


               
        })

        $('body').on('click','#print-label-btn',function () {
                let total = $('#label-setting-frm').find('input[name="total"]').first().val();
                //var form_values = $('#label-setting-frm').serialize();
                var form_values = 'product_id=<?php echo (int)$_GET['id']; ?>';
                total = 1 * total;
                if(!total)
                {
                    total = 1;
                }
                form_values += '&total='+total;
                form_values += "&is_preview=0&is_print=1&action=print_barcode" ;
                var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?'+form_values;
                window.open(frame_url);
        })
        var form_height = $('#label-setting-frm').height();
        if(form_height > 400)
        {
            form_height -= 5;
            $('#preview-frame').css('height',form_height+'px');
        }
        console.log(form_height);
    }(jQuery));
</script>