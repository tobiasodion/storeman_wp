<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $op_warehouse;
$warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : -1;
$warehouses = $op_warehouse->warehouses();
$_columns = array(
    'id' => array('type' => 'numeric', 'label' => __( 'ID', 'openpos' ), 'sortable' => false,'identifier' => true),
    'barcode' => array('type' => 'numeric', 'label' => __( 'Barcode', 'openpos' ), 'sortable' => false,'identifier' => true),
    'product_thumb' => array('type' => 'html', 'label' => __( 'Thumbnail', 'openpos' ), 'sortable' => false,'identifier' => false),
    'post_title' => array('type' => 'text', 'label' =>  __( 'Product Name', 'openpos' ), 'sortable' => false,'identifier' => false),
    'formatted_price' => array('type' => 'html', 'label' => __( 'Price', 'openpos' ), 'sortable' => false,'identifier' => false),
    'qty_html' => array('type' => 'html', 'label' => __( 'Qty', 'openpos' ), 'sortable' => false,'identifier' => false),
    'action' => array('type' => 'html', 'label' => __( 'Action', 'openpos' ) , 'sortable' => false,'identifier' => false),
);
$columns = apply_filters('op_admin_template_stock_columns',$_columns);
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo __( 'POS Stock Overview', 'openpos' ); ?></h1>
    
    <div style="display: block; width: 100%">
        <div style="width: 500px;margin: 0 auto;">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" type="get" action="<?php echo admin_url( 'admin.php' ); ?>">
                        <input type="hidden" name="page" value="op-stock">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><?php echo __( 'Choose Warehouse', 'openpos' ); ?></label>
                            <div class="col-sm-6">
                                <select name="warehouse_id" class="form-control">
                                    <option value="-1" <?php echo ($warehouse_id == -1) ? 'selected':''; ?> ><?php echo __( 'All Warehouse', 'openpos' ); ?></option>
                                    <?php foreach($warehouses as $warehouse): ?>
                                    <option value="<?php echo $warehouse['id']; ?>" <?php echo ($warehouse_id == $warehouse['id']) ? 'selected':''; ?>  ><?php echo $warehouse['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-2"><input type="submit" class="btn btn-success" value="<?php echo __( 'Choose', 'openpos' ); ?>" ></div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
    <form id="op-product-list"  onsubmit="return false;">
        <input type="hidden" name="action" value="admin_openpos_update_product_grid">
        <table id="grid-selection" class="table table-condensed table-hover table-striped op-product-grid">
            <thead>
            <tr>
                <?php foreach($columns as $column_id => $c): ?>
                <th data-column-id="<?php echo $column_id; ?>" class="col-<?php echo $column_id; ?>" data-sortable="<?php echo $c['sortable'] ? 'true' : 'false'; ?>" data-identifier="<?php echo $c['identifier'] ? 'true' : 'false'; ?>" data-type="<?php echo $c['type']; ?>"><?php echo $c['label']; ?></th>
                <?php endforeach; ?>
               
            </tr>
            </thead>
        </table>
    </form>
    <br class="clear">
</div>
<form enctype="multipart/form-data"  style="display: none;">
    <input type="file" id="product_image" name="product_image">
    <input type="hidden" id="product_image_id" name="product_image_id">
</form>

<script type="text/javascript">
    function exportInventory(){
            console.log('xx');
    }
    (function($) {
        "use strict";
        var grid = $("#grid-selection").bootgrid({
            ajax: true,
            post: function ()
            {
                /* To accumulate custom parameter with the request object */
                return {
                    warehouse_id: '<?php echo $warehouse_id; ?>',
                    action: "op_stock_products"
                };
            },
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            selection: false,
            multiSelect: false,
            formatters: {
                "link": function(column, row)
                {
                    return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
                },
                "price": function(column,row){

                    return row.formatted_price;
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><a href=\"javascript:void(0)\"class=\"pull-left btn btn-default  \" data-btn=\"export\" id=\"btn-vna-export\">Export</a><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div></div></div>"
            },
           labels: {
                all: "<?php echo __( 'All', 'openpos' ); ?>",
                infos: "<?php echo __( 'Showing {{ctx.start}} to {{ctx.end}} of {{ctx.total}} entries', 'openpos' ); ?>",
                loading: "<?php echo __( 'Loading...', 'openpos' ); ?>",
                noResults: "<?php echo __( 'No results found!', 'openpos' ); ?>",
                refresh: "<?php echo __( 'Refresh', 'openpos' ); ?>",
                search: "<?php echo __( 'Search', 'openpos' ); ?>"
            }
        }).on("loaded.rs.jquery.bootgrid", function()
        {

            grid.find(".update-row").on("click", function(e)
            {
                let _id = $(this).data("id");
                var id = 'product-row-'+ _id;
                var current_obj = $(this);
                var form_data = grid.find('#'+id).serialize();
                var addtion_data = new Array();
                let row = $(this).closest('tr').first().find('.field-data-update').each(function(){
                    let name_field = $(this).attr('name');
                    if(name_field)
                    {
                        let tmp = name_field+'='+$(this).val();
                        addtion_data.push(tmp);
                    }
                    
                });
                if(addtion_data.length > 0)
                {
                    form_data += '&'+addtion_data.join('&');
                }
                $.ajax({
                    url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: form_data+'&action=op_stock_products_update',
                    beforeSend:function(){
                        current_obj.addClass('loading');

                    },
                    success:function(data){
                        current_obj.removeClass('loading');
                       
                    }
                });

            });

            grid.find('.click-edit-price-a').on('click',function(){
                var parent_div = $(this).closest('.vna-row-price');
                var id = $(this).data("id");
                var field = $(this).data("field");
                if(!field)
                {
                    field = 'price';
                }
                if(parent_div.hasClass('active'))
                {
                    var input_price = parent_div.find('input').first().val();
                    if(input_price.length > 0)
                    {
                        $.ajax({
                            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: 'action=op_stock_products_update&field='+field+'&id='+id+'&field_value='+input_price,
                            beforeSend:function(){

                                parent_div.find('.row-price-input').prop('disabled',true);
                            },
                            success:function(data){
                                parent_div.find('.row-price-input').prop('disabled',false);
                               
                            }
                        });
                    }else {
                        alert('Please enter value');
                    }

                }else {
                    parent_div.addClass('active');
                }
                console.log($(this));
            });

            grid.find('.upload-a').on('click',function(){
                var parent_div = $(this).closest('.vna-cell-image');
                var id = $(this).data("id");
                var input_file = parent_div.find('input').first();
                var img_form = parent_div.find('form').first();
                $('input[name="product_image_id"]').val(id);
                $('#product_image').trigger('click');
            });
            grid.find('.product-allow-warehouse').on('click',function(){
                     var checked = $(this).prop('checked');
                     var input_qty = $(this).closest('p').find('.product-qty-warehouse').first();


                     if(checked)
                     {

                         input_qty.prop('readonly',false);
                     }else {
                         input_qty.prop('readonly',true);
                     }
            });
        });

        $('input#product_image').on('change',function(){

            var files = new FormData();

            files.append('field_value', $('#product_image')[0].files[0]);
            files.append('id', $('input[name="product_image_id"]').val());
            files.append('field', 'image');
            files.append('action', 'op_upload_product_image');

            $.ajax({
                type: 'post',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                processData: false,
                contentType: false,
                data: files,
                success: function (response) {
                    $("#grid-selection").bootgrid('reload');
                },
                error: function (err) {
                    console.log(err);
                }
            });
        })
        $(document).on('click','#btn-vna-export',function(){
            let term_str = $("#grid-selection").bootgrid("getSearchPhrase");
            let warehouse_id = '<?php echo $warehouse_id; ?>';
            

            $.ajax({
                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                type: 'post',
                dataType: 'json',
                data: {action: 'op_stock_products_export', warehouse_id: warehouse_id},
                beforeSend:function(){
                    console.log('processing.....');
                    
                },
                success:function(data){
                    if( data['export_file'])
                    {
                        document.location = data['export_file'];
                    }
                    
                }
            });
        })
        

    })( jQuery );
</script>

<style>
    .action-row a{
        display: block;
        padding: 3px 4px;
        text-decoration: none;
        border: solid 1px #ccc;
        text-align: center;
        margin: 5px;
    }
    .op-product-grid td{
        vertical-align: middle!important;
    }
    .row-price-input,
    .glyphicon-saved,
    .vna-row-price.active .glyphicon-pencil,
    .vna-row-price.active .row-price-span{
        display: none;
    }
    .vna-row-price.active .glyphicon-saved,
    .vna-row-price.active input.row-price-input{
        display: block;
    }
    .vna-cell-image{
        position: relative;
    }
    .upload-a{
        position: absolute;
        right: 0;
        top:0;
        outline: none;
        text-outline: none;
    }
    .warehouse-product-qty .product-allow-warehouse:focus,
    .warehouse-product-qty .product-allow-warehouse{
        outline: none;
        border-radius: 0;
    }
</style>
<?php do_action( 'op_admin_template_stock_after', $_columns ); ?>