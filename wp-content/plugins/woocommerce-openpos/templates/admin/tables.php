<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
global $op_warehouse;
global $op_table;
global $op_woo;
global $op_register;
$warehouses = $op_warehouse->warehouses();
$tables = $op_table->tables();

$default = array(
    'id' => 0,
    'name' => '',
    'warehouse' => 0,
    'position' => 0,
    'type' => 'default',
    'cost' => 0,
    'cost_type' => 'hour',
    'status' => 'publish',
);
$is_new = true;
if(isset($_GET['id']) && $id = $_GET['id'])
{
    $current_register = $op_table->get($id);
    if(!empty($current_register))
    {
        $default = $current_register;
        $is_new = false;
    }
}
?>
<style type="text/css">
    .register-name ul{
        list-style: none;
        display: block;
        margin:0;
        padding:0;
    }
    .register-name ul li{
        float:left;
        padding:3px;
        display: inline-block;
    }
    .register-frm{
        background-color: #ccccccb3;
    }
    .status-draft{
        color: red;
    }
    .status-publish{
        color: green;
    }
</style>
<div class="wrap">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1 class="wp-heading-inline"><?php echo __( 'All Tables', 'openpos' ); ?></h1>
    <br class="clear" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 register-frm">
                <h4><?php echo ($is_new) ?  __( 'New Table', 'openpos' ) : __( 'Edit Table', 'openpos' ); ?></h4>
                <form class="form-horizontal" id="register-frm">
                    <input type="hidden" name="action" value="openpos_update_table">
                    <input type="hidden" name="id" value="<?php echo $default['id']; ?>">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Name', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="<?php echo $default['name']; ?>" placeholder="Table Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Outlet', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" name="warehouse">
                                <?php foreach ($warehouses as $w): ?>
                                    <option <?php echo ($default['warehouse'] == $w['id'] ) ? 'selected':''; ?> value="<?php echo $w['id']; ?>"><?php echo $w['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __( 'Position', 'openpos' ); ?></label>
                        <div class="col-sm-4">
                            <input type="number"  class="form-control text-right" name="position" value="<?php echo $default['position']; ?>" placeholder="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __( 'Type', 'openpos' ); ?></label>
                        <div class="col-sm-4">
                            <select class="form-control" name="type">
                                <option <?php echo ($default['type'] == 'default') ? 'selected':''; ?> value="default"><?php echo __('Default','openpos'); ?></option>
                                <option <?php echo ($default['type'] == 'hire') ? 'selected':''; ?> value="hire"><?php echo __('Hire','openpos'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group table-hire-details" style="display:<?php echo ($default['type'] == 'default') ? 'none':'block' ?>;">
                        <label class="col-sm-2 control-label"><?php echo __( 'Cost', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-sm-6">
                                        <input type="number" class="form-control text-right" name="cost" value="<?php echo $default['cost']; ?>" placeholder="0.00">
                                </div>
                                <div class="col-sm-6">
                                    <select class="form-control" name="cost_type">
                                        <option <?php echo ($default['cost_type'] == 'session') ? 'selected':''; ?> value="session"><?php echo __('Per Session','openpos'); ?></option>
                                        <option <?php echo ($default['cost_type'] == 'hour') ? 'selected':''; ?> value="hour"><?php echo __('Per Hours','openpos'); ?></option>
                                        <option <?php echo ($default['cost_type'] == 'minute') ? 'selected':''; ?> value="minute"><?php echo __('Per Minute','openpos'); ?></option>
                                        <option <?php echo ($default['cost_type'] == 'day') ? 'selected':''; ?> value="day"><?php echo __('Per Day','openpos'); ?></option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __( 'Status', 'openpos' ); ?></label>
                        <div class="col-sm-4">
                            <select class="form-control" name="status">
                                <option <?php echo ($default['status'] == 'publish') ? 'selected':''; ?> value="publish"><?php echo __('Active','openpos'); ?></option>
                                <option <?php echo ($default['status'] == 'draft') ? 'selected':''; ?> value="draft"><?php echo __('Inactive','openpos'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-8 col-sm-4">
                            <button type="submit" class="btn btn-default"><?php echo __( 'Save', 'openpos' ); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8">
                <h4><?php echo __( 'All Tables', 'openpos' ); ?></h4>
                <div class="table-responsive">
                    <table class="table register-list">
                        <tr>
                            <th><?php echo __( 'Name', 'openpos' ); ?></th>
                            <th><?php echo __( 'Outlet', 'openpos' ); ?></th>
                            <th class="text-center"><?php echo __( 'Position', 'openpos' ); ?></th>
                            <th><?php echo __( 'Status', 'openpos' ); ?></th>
                        </tr>
                        <?php foreach($tables as $table): ?>
                            <?php


                                $outlet = $op_warehouse->get($table['warehouse']);

                            ?>
                            <tr>
                                <td class="register-name">
                                    <p>
                                        <?php if(isset($table['type']) && $table['type'] == 'hire'): ?>
                                            <span class="icon-table-hire">$</span>
                                        <?php endif; ?>
                                        <?php echo $table['name']; ?>
                                    </p>
                                    <ul>
                                        <li><a class="qrcode-generate" href="javascript:void(0);" data-id="<?php echo esc_attr($table['id']); ?>"><?php echo __('Qrcode','openpos'); ?></a></li>
                                        <li>|</li>
                                        <li><a href="<?php echo admin_url('admin.php?page=op-tables&id='.esc_attr($table['id'])); ?>"><?php echo __('Edit','openpos'); ?></a></li>
                                        <li>|</li>
                                        <li><a href="javascript:void(0);" class="delete-register-btn" data-id="<?php echo $table['id']; ?>"><?php echo __('Delete','openpos'); ?></a></li>
                                    </ul>
                                </td>
                                <td>
                                    <p><?php echo $outlet['name']; ?></p>
                                    <?php if(isset($table['type']) && $table['type'] == 'hire'): ?>
                                            <p class="table-hire-cost-description">
                                            <?php echo wc_price($table['cost']); ?> / <?php echo $table['cost_type']; ?>
                                            </p>
                                   <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <p><?php echo $table['position']; ?></p>
                                </td>
                                <td>
                                    <span class="status-<?php echo esc_attr($table['status']); ?>"><?php echo $table['status'] == 'publish' ?  __( 'Active', 'openpos' ) :  __( 'Inactive', 'openpos' ); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(count($tables) == 0): ?>
                            <tr>
                                <td colspan="4"><?php echo __('No table found','openpos'); ?></td>
                            </tr>
                        <?php endif; ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display:none;" class="table-qr-dialog">
    <div id="dialog" class="op-qr-dialog" title="<?php echo __('Table Qrcode','openpos');?>">
    
    <div class="container-fluid">
        <div class="row justify-content-center text-center">
                    <img src="https://via.placeholder.com/150x150" class="img-thumbnail" id="table-qrcode-image" alt="qrcode">
        </div>
        <div class="row" style="margin-top:10px;">
            <p>
                <a href="#" id="table-qrcode-verify-url" target="_blank"><?php echo __('Verify URL','openpos'); ?></a>
            </p>
        </div>
        <div class="row" style="margin-top:15px;">
            <form id="generate-qrcode-frm">
                <div class="mb-3 row">
                    <label for="staticEmail" class="col-sm-2 col-form-label"><?php echo __('Register','openpos'); ?></label>
                    <div class="col-sm-10">
                        <select class="form-select" name="qrcode_register" aria-label="Default select example">
                                <option selected value="0"><?php echo __('Choose register','openpos'); ?></option>
                            
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <p style="padding: 5px 0 ;"><?php echo __('Choose register and click Generate button','openpos');?></p>
    <p style="padding: 5px 0 ;"><?php echo __('Current Table','openpos');?>: <span style="color:red;font-weight:bold;"  id="current-qrcode-table-name"></span></p>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function(){
            var selected_table = 0;

            $('#register-frm').on('submit',function(){
                var data = $(this).serialize();
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        if(data.status == 1)
                        {
                            window.location.href = '<?php echo admin_url('admin.php?page=op-tables'); ?>';

                        }else {
                            alert(data.message);
                            $('body').removeClass('op_loading');
                        }
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
                console.log(data);
                return false;
            });

            $(document).on('click','.delete-register-btn',function(){
                var id = $(this).data('id');
               
                if(confirm('Are you sure ? '))
                {
                    $.ajax({
                        url: openpos_admin.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        data: {action: 'openpos_delete_table',id:id},
                        beforeSend:function(){
                            $('body').addClass('op_loading');
                        },
                        success:function(data){
                            if(data.status == 1)
                            {
                                location.reload();
                            }else {
                                alert(data.message);
                                $('body').removeClass('op_loading');
                            }
                        },
                        error:function(){
                            $('body').removeClass('op_loading');
                        }
                    });
                }
            });

            $('select[name="type"]').change(function(){
                var table_type = $(this).val();
                if(table_type == 'hire')
                {
                    $('.table-hire-details').show();
                }else{
                    $('.table-hire-details').hide();
                }
            });
            function generate_qr_code(){
                let form_data = $('#generate-qrcode-frm').serialize();
                let register = 1 * $('select[name="qrcode_register"]').val();
                if(register == 0)
                {
                    alert('Please choose register');
                }else{
                    $.ajax({
                        url: openpos_admin.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        data: {action: 'openpos_geneate_qrcode_table',register:register,id: selected_table},
                        beforeSend:function(){
                            $('select[name="qrcode_register"]').prop('disabled',true);
                        },
                        success:function(data){
                            $('select[name="qrcode_register"]').prop('disabled',false);
                            if(data.status == 1)
                            {
                                generateDialog(data['data']);
                            }else {
                                alert(data.message);
                            
                            }
                        },
                        error:function(){
                            $('body').removeClass('op_loading');
                        }
                    });
                }
            }
            function generateDialog(data){
                let table = data['table'];
                let qrcode = data['qrcode'];
                let register = data['register'];
                let url = data['url'];
                if(data['registers'])
                {
                    let registers = data['registers'];
                    $('select[name="qrcode_register"] option').each(function() {
                        if ( $(this).val() != '0' ) {
                            $(this).remove();
                        }
                    });
                    for(var i = 0; i < registers.length; i++)
                    {
                        $('select[name="qrcode_register"]').append($('<option>',
                        {
                            value: registers[i]['id'],
                            text : registers[i]['name']
                        }));
                    }
                }
                
                $('select[name="qrcode_register"]').val(register);
                $('#table-qrcode-image').attr('src',qrcode);
                $('#table-qrcode-verify-url').attr('href',url);
                $('#current-qrcode-table-name').text(table['name']);
                
            }
            $('.qrcode-generate').on('click',function(){
                var id = $(this).data('id');
                selected_table = id;
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: {action: 'openpos_qrcode_table',id:id},
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        $('body').removeClass('op_loading');
                        
                        if(data.status == 1)
                        {
                            generateDialog(data['data']);
                            $( "#dialog" ).dialog({
                                    resizable: false,
                                    height: "auto",
                                    width: 400,
                                    modal: true,
                                    buttons: {
                                        "<?php echo __('Generate','openpos');?>": function() {
                                                generate_qr_code();
                                        },
                                        "<?php echo __('Close','openpos');?>": function() {
                                        $( this ).dialog( "close" );
                                        }
                                    },
                                    classes: {
                                        "ui-dialog": "op-class-dialog-open"
                                    }
                            });
                        }else {
                            alert(data.message);
                           
                        }
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
                
                
            })

        });



    })( jQuery );
</script>