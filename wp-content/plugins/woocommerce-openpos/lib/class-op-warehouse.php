<?php
if(!class_exists('OP_Warehouse'))
{
    class OP_Warehouse{
        public $_post_type = '_op_warehouse';
        public $_meta_field = array();
        public $_meta_product_qty = '_op_qty_warehouse';
        public $_meta_total_product_qty = '_op_qty_total';
        public $_meta_website_instore = '_hide_pos_website';

        public function __construct()
        {
            add_action( 'init', array($this, 'init') );
            $this->_meta_field = array(
                'address' => '_op_address',
                'address_2' => '_op_address_2',
                'city' => '_op_city',
                'postal_code' => '_op_postal_code',
                'country' => '_op_country',
                'phone' => '_op_phone',
                'email' => '_op_email',
                'facebook' => '_op_facebook'
            );
            add_shortcode( 'op_product_warehouses', array($this,'_op_warehouses_func'));
        }
        public function init(){
            register_post_type( '_op_warehouse',
                    array(
                        'labels'              => array(
                            'name'                  => __( 'Warehouse', 'openpos' ),
                            'singular_name'         => __( 'Warehouse', 'openpos' )
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

        function _op_warehouses_func($atts){
            global $op_warehouses;
            $op_warehouses = array();
            $product_ids = array();
            if(isset($atts['product_id']))
            {
                $tmp_product_id = $atts['product_id'];
                $product = wc_get_product($tmp_product_id);
            }else{
                global $product;
            }
            if($product)
            {
                $product_type = $product->get_type();
                if($product_type == 'variable')
                {
                    $childrent = $product->get_children();
                    $product_ids = $childrent;
                }else{
                  
                    $product_ids[] = $product->get_id();
                }
            }

            if(!empty($product_ids))
            {
                $warehouses = array();
                if(isset($atts['ids']))
                {
                    $tmp = explode(',',$atts['ids']);
                    foreach($tmp as $t)
                    {
                        if($t != '')
                        {
                            $warehouse_id = (int)$t;
                            $wareshouse = $this->get($warehouse_id);
                            if($wareshouse)
                            {
                                $wareshouse['total_qty'] = 0;
                                foreach($product_ids as $_product_id)
                                {
                                    $wareshouse['total_qty'] += $this->get_qty($warehouse_id,$_product_id);
                                }
                                $warehouses[] = $wareshouse;
                            }
                        }
                    }
                }
                if(empty($warehouses))
                {
                    $tmp_warehouses = $this->warehouses();
                    foreach($tmp_warehouses as $tmp_warehouse)
                    {
                        if($tmp_warehouse['status'] == 'publish')
                        {
                            $tmp_warehouse['total_qty'] = 0;
                            foreach($product_ids as $_product_id)
                            {
                                $tmp_warehouse['total_qty'] += $this->get_qty($tmp_warehouse['id'],$_product_id);
                            }
                            $warehouses[] = $tmp_warehouse;
                        }
                    }
                }
                $op_warehouses = $warehouses;
                
                $template = OPENPOS_DIR.'templates/short_code_inventory.php';
                ob_start();
                load_template( $template, false );
                echo ob_get_clean();
            }
            
        }
        
        public function warehouses(){
            $result = array();
            $default_store = $this->get(0);

            $default = array(
                'id' => 0,
                'name' => __('Default online store','openpos'),
                'address' => '',
                'address_2' => '',
                'city' => '',
                'postal_code' => '',
                'country' => '',
                'phone' => '',
                'email' => '',
                'facebook' => '',
                'status' => 'publish',
                'total_qty' => ''
            );
            $result[] = array_merge($default,$default_store);
            $posts = get_posts([
                'post_type' => $this->_post_type,
                'post_status' => array('publish','draft'),
                'numberposts' => -1
            ]);
            foreach($posts as $p)
            {
                $result[] = $this->get($p->ID);
            }
            return apply_filters('op_warehouse_list',$result,$this);
        }
        public function get($id = 0){
            if($id == 0)
            {
                return array(
                    'id' => 0,
                    'name' => __('Default online store','openpos'),
                    'address' =>  WC()->countries->get_base_address(),
                    'address_2' => WC()->countries->get_base_address_2(),
                    'city' => WC()->countries->get_base_city(),
                    'postal_code' => WC()->countries->get_base_postcode(),
                    'country' => implode(':',array(WC()->countries->get_base_country(),WC()->countries->get_base_state())),
                    'phone' => '',
                    'email' => '',
                    'facebook' => '',
                    'status' => 'publish',
                    'total_qty' => 0
                );
            }
            $post = get_post($id);
            if(!$post)
            {
                return array();
            }
            $name = $post->post_title;

            $result = array(
                'id' => $id,
                'name' => $name,
                'total_qty' => 100,
                'status' => $post->post_status
            );
            foreach($this->_meta_field as $field => $meta_key)
            {
                $result[$field] = get_post_meta($id,$meta_key,true);
            }
            return apply_filters('op_warehouse_get_data',$result,$this);

        }
        public function delete($id){
            $post = get_post($id);
            if($post->post_type == $this->_post_type)
            {
                wp_trash_post( $id  );
            }
        }
        public function save($params){
            $id  = 0;
            if(isset($params['id']) && $params['id'] > 0)
            {
                $id = $params['id'];
            }
            $args = array(
                'ID' => $id,
                'post_title' => $params['name'],
                'post_type' => $this->_post_type,
                'post_status' => $params['status'],
                'post_parent' => 0
            );
            $post_id = wp_insert_post($args);
            if(!is_wp_error($post_id)){

                foreach($this->_meta_field as $field => $meta_key)
                {
                    if($meta_value = $params[$field])
                    {
                        update_post_meta($post_id,$meta_key,$meta_value);
                    }
                }
                return $post_id;
            }else{
                //there was an error in the post insertion,
                throw new Exception($post_id->get_error_message()) ;
            }
        }
        public function set_qty($warehouse_id = 0,$product_id,$qty = 0,$is_append = false){
            global $OPENPOS_CORE;
            $OPENPOS_CORE->addProductChange($product_id,$warehouse_id);
            $qty = (float)$qty;
            if($is_append)
            {
                $current_qty = $this->get_qty($warehouse_id,$product_id);
                if($current_qty)
                {
                    $qty += 1 * $current_qty;
                }
            }
            if($warehouse_id > 0)
            {
                $meta_key = $this->_meta_product_qty.'_'.$warehouse_id;
                update_post_meta($product_id,$meta_key,$qty);
                do_action('op_update_warehouse_qty',$warehouse_id,$product_id,$qty,$meta_key);
                //update_option('_openpos_product_version_'.$warehouse_id,time());
            }else{
                $product = wc_get_product($product_id);
                $product->set_stock_quantity($qty);
                $product->save();
            }
           

        }
        public function is_instore($warehouse_id = 0,$product_id){
            $result = true;
            if($warehouse_id == 0){
                $meta_key = $this->_meta_website_instore;
                $hide_pos_meta_value = get_post_meta($product_id,$meta_key,true);
                if($hide_pos_meta_value == 'yes')
                {
                    $result = false;
                }
            }
            elseif($warehouse_id > 0)
            {
                $product = wc_get_product($product_id);
                $product_type = $product->get_type();
                $meta_key = $this->_meta_product_qty.'_'.$warehouse_id;
                if($product_type == 'variable')
                {
                    $childs = $product->get_children();
                    $total = false;
                    foreach($childs as $child_id)
                    {
                        $qty = get_post_meta($child_id,$meta_key,true);
                        if($qty === false || $qty == '')
                        {
                            
                        }else{
                            $total += 1 * $qty;
                        }
                    }
                    if($total === false)
                    {
                        $result = false;   
                    }
                }else{
                    $qty = get_post_meta($product_id,$meta_key,true);
                    if($qty === false || $qty == '')
                    {
                        $result = false;
                    }
                }
            }
            return apply_filters('op_warehouse_is_instore',$result,$warehouse_id,$product_id,$this);

        }
        public function remove_instore($warehouse_id = 0,$product_id){
            if($warehouse_id > 0)
            {
                $meta_key = $this->_meta_product_qty.'_'.$warehouse_id;
                update_post_meta($product_id,$meta_key,'');
            }
            if($warehouse_id == 0){
                $meta_key = $this->_meta_website_instore;
                update_post_meta($product_id,$meta_key,'yes');
            }

        }
        public function add_instore_website($product_id)
        {
            $meta_key = $this->_meta_website_instore;
            delete_post_meta($product_id,$meta_key,'yes');
        }
        public function get_qty($warehouse_id = 0,$product_id){
            $qty = 0;
            if($warehouse_id > 0)
            {
                $meta_key = $this->_meta_product_qty.'_'.$warehouse_id;
                $qty = get_post_meta($product_id,$meta_key,true);

                if(!$qty)
                {
                    $qty = 0;
                }
            }else{
                $product = wc_get_product($product_id);
                if($product)
                {
                    $qty = $product->get_stock_quantity();
                }
                
            }
            return 1*$qty;
        }
        public function get_total_qty($product_id)
        {
            $qty = 0;
            $warehouses = $this->warehouses();
            
            foreach($warehouses as $w)
            {
                $warehouse_id = $w['id'];
                $qty += 1 * $this->get_qty($warehouse_id,$product_id);
               
            }
            return $qty;
        }
        public function get_order_meta_key(){
            $option_key = '_pos_order_warehouse';
            return $option_key;
        }
        public function get_transaction_meta_key(){
            $option_key = '_pos_transaction_warehouse';
            return $option_key;
        }
        public function getStorePickupAddress($warehouse_id = 0){
            $details = $this->get($warehouse_id);
            $result['address_1'] = isset($details['address']) ? $details['address']:'';
            $result['address_2'] = isset($details['address_2']) ? $details['address_2']:'';
            $result['city'] = isset($details['city']) ? $details['city']:'';
            $result['postcode'] = isset($details['postal_code']) ? $details['postal_code']:'';
            $country_state = isset($details['country']) ? $details['country']:'';
            $country = '';
            $state = '';
            if($country_state)
            {
                $location = wc_format_country_state_string($country_state);

                $country = $location['country'];
                $state = $location['state'];
            }
            $result['country'] = $country;
            $result['state'] = $state;
            return $result;
        }

        public function low_stock($warehouse_id, $product_id ) {
            if ( 'no' === get_option( 'woocommerce_notify_low_stock', 'yes' ) ) {
                return;
            }
            $product = wc_get_product($product_id);
            $qty = $this->get_qty($warehouse_id,$product_id);
            $stock   = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
            if($qty > $stock)
            {
                return;
            }
		    
            
            
            if ( false === apply_filters( 'woocommerce_should_send_low_stock_notification', true, $product->get_id() ) ) {
                return;
            }
            $warehouse = $this->get($warehouse_id);
            if(!empty($warehouse))
            {
            
                $warehouse_name = $warehouse['name']; 
                $subject = sprintf( '[%s] %s',  $warehouse_name, __( 'Product low in stock', 'woocommerce' ) );
                $message = sprintf(
                    /* translators: 1: product name 2: items in stock */
                    __( '%1$s is low in stock. There are %2$d left.', 'woocommerce' ),
                    html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
                    html_entity_decode( wp_strip_all_tags( $qty ) )
                );
        
                wp_mail(
                    apply_filters( 'woocommerce_email_recipient_low_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
                    apply_filters( 'woocommerce_email_subject_low_stock', $subject, $product, null ),
                    apply_filters( 'woocommerce_email_content_low_stock', $message, $product ),
                    apply_filters( 'woocommerce_email_headers', '', 'low_stock', $product, null ),
                    apply_filters( 'woocommerce_email_attachments', array(), 'low_stock', $product, null )
                );
            }
            
        }
    
       
        public function no_stock($warehouse_id, $product_id ) {
            if ( 'no' === get_option( 'woocommerce_notify_no_stock', 'yes' ) ) {
                return;
            }
            $product = wc_get_product($product_id);
            if ( false === apply_filters( 'woocommerce_should_send_no_stock_notification', true, $product->get_id() ) ) {
                return;
            }
            
            $warehouse = $this->get($warehouse_id);
            $qty = $this->get_qty($warehouse_id,$product_id);
            
            $nostock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
            if($qty > $nostock)
            {
                return;
            }

            if(!empty($warehouse) )
            {
               
                $warehouse_name = $warehouse['name']; 
                $subject = sprintf( '[%s] %s', $warehouse_name, __( 'Product out of stock', 'woocommerce' ) );
                /* translators: %s: product name */
                $message = sprintf( __( '%s is out of stock.', 'woocommerce' ), html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
        
                wp_mail(
                    apply_filters( 'woocommerce_email_recipient_no_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
                    apply_filters( 'woocommerce_email_subject_no_stock', $subject, $product, null ),
                    apply_filters( 'woocommerce_email_content_no_stock', $message, $product ),
                    apply_filters( 'woocommerce_email_headers', '', 'no_stock', $product, null ),
                    apply_filters( 'woocommerce_email_attachments', array(), 'no_stock', $product, null )
                );
            }
            
        }

        public function getTakeawayByKey($key){
            
            $key_meta = '_op_barcode_takeaway_key';
            $register_meta = '_op_barcode_takeaway_register';
            $args = array(
                'meta_query'        => array(
                    array(
                        'key'       => $key_meta,
                        'value'     => $key
                    )
                ),
                'post_type'         => $this->_post_type,
                'posts_per_page'    => '1',
                'post_status' => 'any'
            );
           
            // run query ##
            $posts = get_posts( $args );
            
            $result = null;
            foreach($posts as $p)
            {
                $result = $this->get($p->ID);

            }
            if($result != null)
            {
                $result['register_id'] = get_post_meta($result['id'],$register_meta,true);
                $result['warehouse_id'] = $result['id'];
            }else{
                $default_key = get_option('_op_barcode_takeaway_key',true);
                $default_register = get_option('_op_barcode_takeaway_register',true);
                if($key == $default_key && $default_register)
                {
                    $result = $this->get(0);
                    $result['register_id'] = $default_register;
                    $result['warehouse_id'] = 0;
                }
            }

            
            return $result;
        }

    }

}
?>