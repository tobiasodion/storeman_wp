<?php
if(!class_exists('OP_Report'))
{
    class OP_Report{
        public $_zpost_type = 'op_z_report';
        public $_core;
        public function __construct()
        {
            global $OPENPOS_CORE;
            $this->_core = $OPENPOS_CORE;
            add_action( 'init', array($this, '_wp_init') );
            $this->init();
        }
        function init(){
             
            add_filter('op_report_result',array($this,'op_report_result'),10,3);
            add_action( 'wp_ajax_print_zreport', array($this,'print_zreport') );
        }
        function _wp_init(){
            register_post_type( 'op_z_report',
                    array(
                        'labels'              => array(
                            'name'                  => __( 'Z-Report', 'openpos' ),
                            'singular_name'         => __( 'Z-Report', 'openpos' )
                        ),
                        'description'         => __( 'This is where you can add new transaction that customers can use in your store.', 'openpos' ),
                        'public'              => false,
                        'show_ui'             => false,
                        'capability_type'     => 'op_report',
                        'map_meta_cap'        => true,
                        'publicly_queryable'  => false,
                        'exclude_from_search' => true,
                        'show_in_menu'        => false,
                        'hierarchical'        => false,
                        'rewrite'             => false,
                        'query_var'           => false,
                        'supports'            => array( 'title','author','content' ),
                        'show_in_nav_menus'   => false,
                        'show_in_admin_bar'   => false
                    )
            );
        }
        public function add_z_report($data){
            
            if(!isset($data['login_time']))
            {
                    $data['login_time'] = strtotime($data['session_data']['logged_time']);
                    $data['login_time'] = $data['login_time'] * 1000;
            }
            if(!isset($data['logout_time']))
            {
                $data['logout_time'] = time();
                $data['logout_time'] = $data['logout_time'] * 1000;
            }

            $login_time = round($data['login_time']/1000);
            $logout_time = round($data['logout_time']/1000);

            $open_balance = $data['open_balance'];
            $close_balance = $data['close_balance'];
            $sale_total = $data['sale_total'];
            $custom_transaction_total = $data['custom_transaction_total'];
            $item_discount_total = $data['item_discount_total'];
            $cart_discount_total = $data['cart_discount_total'];
            $tax = isset($data['tax']) ? $data['tax'] : $data['taxes'];

            $cashier_name = $data['session_data']['name'];
            $login_cashdrawer_id = $data['session_data']['login_cashdrawer_id'];
            $login_warehouse_id = $data['session_data']['login_warehouse_id'];
            $cash_drawers = $data['session_data']['cash_drawers'];
            
            $session_id = $data['session_data']['session'];
            $cashdrawer_name = $login_cashdrawer_id;
            foreach($cash_drawers as $c)
            {
                if($c['id'] == $login_cashdrawer_id)
                {
                    $cashdrawer_name = $c['name'];                }
            }
            $WC_DateTime_login = $this->formatTimeStamp($login_time);
            $login_date_str = $WC_DateTime_login->date_i18n( 'd/m/Y h:i:s');
            $WC_DateTime_logout = $this->formatTimeStamp($logout_time);
            $logout_date_str = $WC_DateTime_logout->date_i18n( 'd/m/Y h:i:s');
            $user_id = $data['cashier_user_id'];

            $title = $cashier_name.'@'.$cashdrawer_name;

            $z_reports = array();
            if($session_id)
            {
                $post_type = $this->_zpost_type;
                //start check zreport exist
                $args = array(
                    'post_type' => $post_type,
                    'post_status' => 'any',
                    'meta_query' => array(
                        array(
                            'key' => 'session_id',
                            'value' => $session_id,
                            'compare' => '=',
                        )
                    ),
                    'fields' => 'ids'
                );
                $query = new WP_Query($args);
    
                $z_reports = $query->get_posts();
                
            }
            $insert_data = array(
                'post_title'=> $title,
                'post_content'=> json_encode($data),
                'post_type'=> $this->_zpost_type,
                'post_author'=> $user_id,
                'post_status'  => 'publish'
            );
            if(!empty($z_reports))
            {
                $insert_data['ID'] = end($z_reports);
            }
            $id = wp_insert_post($insert_data);
            if($id)
            {
                add_post_meta($id,'login_time',$login_time);
                add_post_meta($id,'logout_time',$logout_time);

                add_post_meta($id,'login_date',$login_date_str);
                add_post_meta($id,'logout_date',$logout_date_str);

                add_post_meta($id,'login_cashdrawer_id',$login_cashdrawer_id);
                add_post_meta($id,'login_warehouse_id',$login_warehouse_id);
                add_post_meta($id,'session_id',$session_id);

                add_post_meta($id,'open_balance',$open_balance);
                add_post_meta($id,'close_balance',$close_balance);
                add_post_meta($id,'sale_total',$sale_total);
                add_post_meta($id,'custom_transaction_total',$custom_transaction_total);

                add_post_meta($id,'item_discount_total',$item_discount_total);
                add_post_meta($id,'cart_discount_total',$cart_discount_total);
                add_post_meta($id,'tax',$tax);
            }
            return $id;
            
        }
        function formatTimeStamp($timestamp){
            $datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
            // Set local timezone or offset.
            if ( get_option( 'timezone_string' ) ) {
                $datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
            } else {
                $datetime->set_utc_offset( wc_timezone_offset() );
            }
            return $datetime;
        }
        function getZReportPosts($from_time,$to_time){
            $meta_query_args = array(
                'post_type'  => $this->_zpost_type,
                'number' => -1,
                'post_status'      => 'publish',
                'meta_query' => array(
                    array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'login_time',
                            'value'   => $from_time,
                            'compare' => '>'
                        ),
                        array(
                            'key'     => 'logout_time',
                            'value'   => $to_time,
                            'compare' => '<'
                        )
                    )
                ),
                
            );
            $post_query = new  WP_Query( $meta_query_args );
            
            return $post_query->posts;
            
        }
        function op_report_result($result,$ranges,$report_type){
            global $OPENPOS_CORE;
            global $op_woo;   
            $this->_core = $OPENPOS_CORE;
            $report_outlet_id =  isset($_REQUEST['report_outlet']) ? $_REQUEST['report_outlet'] : 0;
            $report_register_id =  isset($_REQUEST['report_register']) ? $_REQUEST['report_register'] : 0;
            
            $from = $ranges['start'];
            $to = $ranges['end'];
            
            $WC_DateTime_start = wc_string_to_datetime( $from );
            $start_timestamp = $WC_DateTime_start->getTimestamp() ;

            $WC_DateTime_end = wc_string_to_datetime( $to );
            $end_timestamp = $WC_DateTime_end->getTimestamp() ;

            if($report_type == 'z_report')
            {
                

                $posts = $this->getZReportPosts($start_timestamp,$end_timestamp);
                
                $table_data = array();
                $result['orders_export_data'] = array();
                $orders_export_data = array();
                $orders_export_data[] = array(
                    __('Session','openpos'),
                    __('Clock IN','openpos'),
                    __('Clock OUT','openpos'),
                    __('Open Cash','openpos'),
                    __('Close Cash','openpos'),
                    __('Total Sales','openpos'),
                    __('Total Custom Transaction','openpos'),
                    __('Total Item Discount','openpos'),
                    __('Total Cart Discount','openpos'),
                );
                foreach($posts as $p)
                {
                    $login_date = get_post_meta($p->ID,'login_date',true);
                    $logout_date = get_post_meta($p->ID,'logout_date',true);
                    $open_balance = get_post_meta($p->ID,'open_balance',true);
                    $close_balance =  get_post_meta($p->ID,'close_balance',true);
                    $sale_total = get_post_meta($p->ID,'sale_total',true);
                    $custom_transaction_total = get_post_meta($p->ID,'custom_transaction_total',true);
                    $item_discount_total = get_post_meta($p->ID,'item_discount_total',true);
                    $cart_discount_total = get_post_meta($p->ID,'cart_discount_total',true);

                    $login_cashdrawer_id = (int)get_post_meta($p->ID,'login_cashdrawer_id',true);
                    $login_warehouse_id = (int)get_post_meta($p->ID,'login_warehouse_id',true);
                    
                    if(!$sale_total)
                    {
                        $sale_total = 0;
                    }

                    if($report_outlet_id >= 0 &&  $report_outlet_id != $login_warehouse_id)
                    {
                        continue;
                    }

                    if($report_register_id > 0 && $report_register_id != $login_cashdrawer_id)
                    {
                        continue;
                    }

                    
                    $orders_export_data[] = array(
                        $p->post_title,
                        $login_date,
                        $logout_date,
                        (float)$open_balance,
                        (float)$close_balance,
                        (float)$sale_total,
                        (float)$custom_transaction_total,
                        (float)$item_discount_total,
                        (float)$cart_discount_total
                    );

                    $tmp = array(
                        ''.$p->ID,
                        $p->post_title,
                        $login_date,
                        $logout_date,
                        (float)$open_balance,
                        (float)$close_balance,
                        (float)$sale_total,
                        get_the_date('',$p),
                        '<a target="_blank" href="'.esc_url(admin_url( 'admin-ajax.php?action=print_zreport&id='.$p->ID )).'">'.__('Print','openpos').'</a>'
                    );
                    $table_data[] = $tmp; 
                    
                }
                $table_label = array(
                    __('#','openpos'),
                    __('Session','openpos'),
                    __('Clock IN','openpos'),
                    __('Clock OUT','openpos'),
                    __('Open Cash','openpos'),
                    __('Close Cash','openpos'),
                    __('Total Sales','openpos'),
                    __('Record At','openpos'),
                    __('Action','openpos'),
                );
                $result['table_data'] = array('data' => $table_data,'label'=> $table_label); 
                $result['orders_export_data']  =  $orders_export_data;
                
            }
            if($report_type == 'debt_report')
            {
                $table_data = array();
                $result['orders_export_data'] = array();
                $orders_export_data = array();
                $orders_export_data[] = array(
                    __('Order','openpos'),
                    __('Grand Total','openpos'),
                    __('Customer Paid','openpos'),
                    __('Customer','openpos'),
                    __('Cashier','openpos'),
                    __('Created At','openpos')
                );
                $sales = array();
                $laybuy_order_status = $this->_core->getPosLayBuyOrderStatus();
                $laybuy_order_status = 'wc-'.$laybuy_order_status;
                if($report_register_id > 0)
                {
                        $sales = $this->_core->getPosRegisterOrderByDate($report_register_id,$from,$to,array($laybuy_order_status));
                }elseif($report_outlet_id > 0){
                        $sales = $this->_core->getPosWarehouseOrderByDate($report_outlet_id,$from,$to,array($laybuy_order_status));
                }else{
                        $sales = $this->_core->getPosOrderByDate($from,$to,array($laybuy_order_status));
                }
                $deb_total = 0;
                $deb_total_num = 0;
                foreach($sales as $_order)
                {
                    $id = $_order->ID;
                    $is_oder_laybuy = get_post_meta($id,'_op_allow_laybuy',true);
                    if($is_oder_laybuy == 'yes' )
                    {
                        $order_formatted = $op_woo->formatWooOrder($id);
                        $customer_total_paid = $order_formatted['customer_total_paid'];
                        $total_paid = $order_formatted['total_paid'];
                        if(( $total_paid - $customer_total_paid ) <= 0)
                        {
                            continue;
                        }
                        $deb_total += ( $total_paid - $customer_total_paid );

                        $order = new WC_Order($id);
                        $grand_total = $order->get_total() - $order->get_total_refunded();

                        $created_at = $order->get_date_created();
                        $customer_name = __('Guest','openpos');
                        $customer_phone = '';
                        $customer_email = '';
                        if(isset($order_formatted['customer']) && $order_formatted['customer']['name'])
                        {
                            $customer = $order_formatted['customer'];
                            $customer_name = $order_formatted['customer']['name'];
                            if( $customer['phone'] && $customer['phone'] != null)
                            {
                                $customer_phone = $customer['phone'] ;
                            }
                            if( $customer['email'] && $customer['email'] != null)
                            {
                                $customer_email = $customer['email'] ;
                            }
                        }
                        
                        $author_name = $order_formatted['sale_person_name'];
                       

                        $deb_total_num++;


                        $orders_export_data[] = array(
                            $order->get_order_number(),
                            $grand_total,
                            $customer_total_paid,
                            $customer_name,
                            $author_name,
                            wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) )
                        );
                        if($customer_phone)
                        {
                            $customer_name .= '<br/>'.$customer_phone;
                        }

                        if($customer_email)
                        {
                            $customer_email .= '<br/>'.$customer_email;
                        }

                        $table_data[] = array(
                            $order->get_order_number(),
                            strip_tags(wc_price($grand_total)),
                            strip_tags(wc_price($customer_total_paid)),
                            $customer_name,
                            $author_name,
                            wc_format_datetime( $created_at ).' '.wc_format_datetime( $created_at, get_option( 'time_format' ) ) ,
                            '<a target="_blank" href="'.esc_url($order->get_edit_order_url()).'">'.__( 'View', 'openpos' ) .'</a>'
                        );

                        

                    }
                   
                }
                
                $summary_html = '';
                $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3 text-center" style="margin-bottom: 15px;">';
                $summary_html .= '<div class="summary-block">';
                $summary_html .= '<dl>';
                $summary_html .= '<dt>'.__('Total Order','openpos').'</dt>';
                $summary_html .= '<dd>'.$deb_total_num.'</dd>';
                $summary_html .= '</dl>';
                $summary_html .= '</div>';
                $summary_html .= '</div>';
                $summary_html .= '<div class="col-md-3 col-log-3 col-sm-3 col-xs-3 text-center" style="margin-bottom: 15px;">';
                $summary_html .= '<div class="summary-block">';
                $summary_html .= '<dl>';
                $summary_html .= '<dt>'.__('Total Debit','openpos').'</dt>';
                $summary_html .= '<dd>'.wc_price($deb_total).'</dd>';
                $summary_html .= '</dl>';
                $summary_html .= '</div>';
                $summary_html .= '</div>';


                $result['summary_html'] = $summary_html;
               
                $table_label = array(
                    __('#','openpos'),
                    __('Grand Total','openpos'),
                    __('Customer Paid','openpos'),
                    __('Customer','openpos'),
                    __('Cashier','openpos'),
                    __('Created At','openpos'),
                    __('View','openpos'),
                );
                $result['table_data'] = array('data' => $table_data,'label'=> $table_label); 
                $result['orders_export_data']  =  $orders_export_data;
            }
            
            return $result;
           
        }
        public function getSellerSaleByOrder($order){
            $items = $order->get_items();
            $_op_sale_by_person_id = get_post_meta($order->get_id(),'_op_sale_by_person_id',true);
            $_op_sale_by_cashier_id = get_post_meta($order->get_id(),'_op_sale_by_cashier_id',true);
            if(!$_op_sale_by_person_id)
            {
                $_op_sale_by_person_id = $_op_sale_by_cashier_id;
            }
            $result = array();
           
            foreach($items as $item)
            {
                $_item_sale_id = $item->get_meta('_op_seller_id');
                if(!$_item_sale_id)
                {
                    $_item_sale_id = $_op_sale_by_person_id;
                }
                
                $item_data = $item->get_data();
                if(isset($result[$_op_sale_by_person_id]))
                {
                    $result[$_op_sale_by_person_id] += $item_data['subtotal'];
                }else{
                    $result[$_op_sale_by_person_id] = $item_data['subtotal'];
                }
            }
            return $result;
        }
        public function getSaleCommision($order){
            $commision = 0;
            $items = $order->get_items();
            
            foreach($items as $item)
            {
                $cost_price = 0;

                $item_data = $item->get_data();
                //$item_qty = $item->get_quantity();

                $metas = $item->get_meta_data();
                //$sub_total = $item_data['subtotal'];
                $sub_total = $item_data['total'];
                $total_tax = $item_data['total_tax'];
                $quantity = $item_data['quantity'];
                
                
                foreach($metas as $meta)
                {
                    if($meta->key == '_op_cost_price' && $meta->value)
                    {
                        $cost_price = $meta->value; 
                        if(is_array($cost_price) || !is_numeric($cost_price))
                        {
                        	$cost_price = 0;
                        }
                    }
                }
                
                
                $commision += $sub_total  - ($quantity * $cost_price );
               
                
            }

            $point_discount = get_post_meta($order->get_id(), '_op_point_discount', true);
            $point_discount_amount = 0;
            
            if(is_array($point_discount) && isset($point_discount['point_money']))
            {
                
                $point_discount_amount += 1 * $point_discount['point_money'];
                
            }
            
            return ($commision -  $point_discount_amount) ;
        }
        public function getSaleBySellerReport($sellers,$from,$to){
            global $OPENPOS_CORE;
            $this->_core = $OPENPOS_CORE;
            $orders = $this->_core->getPosOrderByDate($from,$to);
            $result = array();
            foreach($orders as $_order)
            {
                $order = new WC_Order($_order->ID);
                $items = $order->get_items();
                $_op_sale_by_person_id = get_post_meta($_order->ID,'_op_sale_by_person_id',true);
                foreach($items as $item)
                {
                    if(!$item)
                    {
                        continue;
                    }
                    $_item_sale_id = $item->get_meta('_op_seller_id');
                    if(!$_item_sale_id )
                    {
                        $_item_sale_id  = $_op_sale_by_person_id;
                    }
                    if(!empty($sellers) && !in_array($_item_sale_id,$sellers) )
                    {
                        continue;
                    }
                    if(!isset($result[$_item_sale_id]))
                    {
                        $result[$_item_sale_id] = array(
                            'total_qty' => 0,
                            'total_sale' => 0,
                        );
                    }
                    $item_qty = 1 * $item->get_quantity();
                    $item_data = $item->get_data();
                    $item_sale = 1 * $item_data['subtotal'];
                    $result[$_item_sale_id]['total_qty'] += $item_qty;
                    $result[$_item_sale_id]['total_sale'] += $item_sale;
                }
            }
           
            return $result;
        }
        public function getSaleByCashierReport($sellers,$from,$to){
            global $OPENPOS_CORE;
            $this->_core = $OPENPOS_CORE;
            $orders = $this->_core->getPosOrderByDate($from,$to);
            $result = array();
            foreach($orders as $_order)
            {
                $order = new WC_Order($_order->ID);
                $items = $order->get_items();
                $_op_sale_by_person_id = get_post_meta($_order->ID,'_op_sale_by_person_id',true);
                if(!isset($result[$_op_sale_by_person_id]))
                {
                    $result[$_op_sale_by_person_id] = array(
                        'total_qty' => 0,
                        'total_order' => 0,
                        'total_sale' => 0,
                        'total_tip' => 0,
                    );
                }
                $result[$_op_sale_by_person_id]['total_order'] += 1;
                $grand_total = $order->get_total() - $order->get_total_refunded();
                $result[$_op_sale_by_person_id]['total_sale'] += $grand_total;
                $result[$_op_sale_by_person_id]['total_tip'] += $this->getSaleTip($order);
                foreach($items as $item)
                {
                    if(!$item)
                    {
                        continue;
                    }
                    $item_qty = 1 * $item->get_quantity();
                    $item_data = $item->get_data();
                    
                    $result[$_op_sale_by_person_id]['total_qty'] += $item_qty;
                    
                }
            }
           
            return $result;
        }
        public function print_zreport(){
            $id = isset($_REQUEST['id']) ? 1 * $_REQUEST['id'] : 0;
            $post = get_post($id);
            if($post && $post->post_type == 'op_z_report')
            {
                $author_id = $post->post_author;
                $info_title = $post->post_title;
                $login_date = get_post_meta($post->ID,'login_date',true);
                $logout_date = get_post_meta($post->ID,'logout_date',true);
                $open_balance = get_post_meta($post->ID,'open_balance',true);
                $close_balance =  get_post_meta($post->ID,'close_balance',true);
                $sale_total = get_post_meta($post->ID,'sale_total',true);
                $custom_transaction_total = get_post_meta($post->ID,'custom_transaction_total',true);
                $item_discount_total = get_post_meta($post->ID,'item_discount_total',true);
                $cart_discount_total = get_post_meta($post->ID,'cart_discount_total',true);

                $login_cashdrawer_id = (int)get_post_meta($post->ID,'login_cashdrawer_id',true);
                $login_warehouse_id = (int)get_post_meta($post->ID,'login_warehouse_id',true);
                $session_id = get_post_meta($post->ID,'session_id',true);
                $order_data = $this->getOrderReportBySession($session_id);
                $transaction_data = $this->getTransactionReportBySession($session_id);
                $report_items = array();
                $report_payment_methods = array();
                $dicount_item_count = 0;
                $discount_cart_count = 0;
                $tax_total = 0;
                $shipping_total = 0;

                if(!$sale_total)
                {
                    $sale_total = 0;
                }
                foreach($order_data as $order_id)
                {
                    
                    $_order = new WC_Order($order_id);
                    if($_order)
                    {
                        $pos_order = get_post_meta($order_id,'_op_order',true);
                        $tax_total += $pos_order['tax_amount'];
                        $shipping_cost = $pos_order['shipping_cost'];
                        $shipping_tax = $pos_order['shipping_tax'];
                        $shipping_total += $shipping_cost - $shipping_tax;
                        $items = $_order->get_items();
                        foreach($items as $item)
                        {
                            $item_id = $item->get_id();
                            $product_id = $item->get_product_id();
                            $variation_id = $item->get_variation_id();
                            $qty = $item->get_quantity();
                            $index_id = $product_id;
                            $total = $item->get_total();
                            if($variation_id)
                            {
                                $index_id = $variation_id;
                            }
                            if(!$index_id)
                            {
                                $index_id = $item_id;
                            }
                            if(isset($report_items[$index_id]))
                            {
                                $report_items[$index_id]['qty'] += $qty;
                                $report_items[$index_id]['total'] += $total;
                            }else{
                                $report_items[$index_id] = array(
                                    'qty' => $qty,
                                    'name' => $item->get_name(),
                                    'product_id' => $product_id,
                                    'variation_id' => $variation_id,
                                    'total' => $total
                                );
                            }
                            
                        }
                        
                    }
                }
                
                foreach($transaction_data as $transaction_id)
                {
                    $transaction_details = get_post_meta($transaction_id,'_transaction_details',true);
                    if(isset($transaction_details['source_type']) && $transaction_details['source_type'] == 'order')
                    {
                        $payment_code = $transaction_details['payment_code'];
                        $payment_name = $transaction_details['payment_name'];
                        $in_amount = $transaction_details['in_amount'];
                        $out_amount = $transaction_details['out_amount'];
                        if(isset($report_payment_methods[$payment_code]))
                        {
                               
                            $report_payment_methods[$payment_code]['total'] += $in_amount;
                            $report_payment_methods[$payment_code]['total'] -= $out_amount;
                        }else{
                            $report_payment_methods[$payment_code] = array(
                                'payment_code' => $payment_code,
                                'payment_name' => $payment_name,
                                'total' => ($in_amount - $out_amount)
                            );
                        }
                       
                    }
                    
                }
                
                

                    
                require(OPENPOS_DIR.'templates/admin/report/print_z_report.php');
            }else{
                echo __('ohhh!record not found','openpos');
            }
            
            exit;
        }
        public function getOrderReportBySession( $session_id)
        {
            global $wpdb;
            $posts = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_op_session_id' AND  meta_value = '".$session_id."'", ARRAY_A);
            $result = array();
            foreach($posts as $p)
            {
                $result[] = $p['post_id'];
            }
            
            return $result;
        }
        public function getTransactionReportBySession( $session_id)
        {
            global $wpdb;
            $posts = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_op_trans_session_id' AND  meta_value = '".$session_id."'", ARRAY_A);
            $result = array();
            foreach($posts as $p)
            {
                $result[] = $p['post_id'];
            }
             return $result;
        }
        public function getSaleTip($order){
            $tip_amount = 0;
            $tip = get_post_meta($order->get_id(), '_op_tip', true);
            if($tip && isset($tip['total']))
            {
                $tip_amount += 1 * $tip['total'];
            }
            return $tip_amount;
        }
    }
}
?>