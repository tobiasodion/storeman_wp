<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 4/10/19
 * Time: 13:33
 */
if(!class_exists('OP_Woo_Order'))
{
    class OP_Woo_Order{
        private $settings_api;
        private $_core;
        private $_session;
        public function __construct()
        {
            $this->_session = new OP_Session();
            $this->settings_api = new Openpos_Settings();
            $this->_core = new Openpos_Core();
            //add_action('woocommerce_before_cart_table',array($this,'woocommerce_before_cart_table'));
            add_action('op_add_order_item_meta',array($this,'op_add_order_item_meta'),10,2);


            add_filter( 'woocommerce_order_number', array( $this, 'display_order_number' ), 10, 2 );

        }

        public function getOrderNotes($order_id){
            $result = array();

            $order = wc_get_order($order_id);
            if($order)
            {
                $order_created_at = esc_html( sprintf( __( '%1$s at %2$s', 'woocommerce' ), $order->get_date_created()->date_i18n( wc_date_format() ), $order->get_date_created()->date_i18n( wc_time_format() ) ) );
                    
                $result[] = array(
                    'content' =>   esc_html( sprintf( __( 'Created Order  %1$s', 'openpos' ),$order->get_order_number())),
                    'created_at' => $order_created_at 
                );
                $notes = wc_get_order_notes( array( 'order_id' => $order_id ) );
                foreach ($notes as $note)
                {
                    $created_at = esc_html( sprintf( __( '%1$s at %2$s', 'woocommerce' ), $note->date_created->date_i18n( wc_date_format() ), $note->date_created->date_i18n( wc_time_format() ) ) );
                    $content = $note->content;
                    if($note->customer_note)
                    {
                        $content.= ' - '.$note->customer_note;
                    }
                    $result[] = array(
                        'content' => $content,
                        'created_at' => $created_at
                    );
                }

            }

            return $result;
        }
        public function addOrderNote($order_id,$note){
            $order = wc_get_order($order_id);
            if($order && $note)
            {
                wc_create_order_note($order_id,$note);
            }
        }
        public function addOrderStatusNote($order_id,$note,$status){
            $order = wc_get_order($order_id);
            if($order && $note)
            {
                $order->set_status($status, $note);
                $order->save();
                
            }
        }

        public function formatOrderNumber($order_number){
            $pos_sequential_number_enable = $this->settings_api->get_option('pos_sequential_number_enable','openpos_general');
            if($pos_sequential_number_enable == 'yes')
            {
                $pos_sequential_number_prefix = $this->settings_api->get_option('pos_sequential_number_prefix','openpos_general');
                $order_number    = apply_filters(
                    'op_wc_custom_order_numbers',
                    sprintf( '%s%s', $pos_sequential_number_prefix, $order_number ),
                    'value',
                    $order_number
                );
                return (string) apply_filters( 'op_display_woocommerce_order_number_formatted', $order_number);
            }else{
                return $order_number;
            }
        }

        public function display_order_number($order_number, $order ){
            $is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
            $order_id              = ( $is_wc_version_below_3 ? $order->id : $order->get_id() );
            $pos_sequential_number_enable = $this->settings_api->get_option('pos_sequential_number_enable','openpos_general');
            $_op_wc_custom_order_number_formatted     = get_post_meta( $order_id, '_op_wc_custom_order_number_formatted', true );
            if($_op_wc_custom_order_number_formatted)
            {
                return $_op_wc_custom_order_number_formatted;
            }
            if($pos_sequential_number_enable == 'yes')
            {
                $order_number_meta     = get_post_meta( $order_id, '_op_wc_custom_order_number', true );
                $order_number = $order_id;
                if($order_number_meta)
                {
                    $order_number = (int)$order_number_meta;


                    $pos_sequential_number_prefix = $this->settings_api->get_option('pos_sequential_number_prefix','openpos_general');

                    $order_number    = apply_filters(
                        'op_wc_custom_order_numbers',
                        sprintf( '%s%s', $pos_sequential_number_prefix, $order_number ),
                        'value',
                        $order_number
                    );

                }
                return (string) apply_filters( 'op_display_woocommerce_order_number', $order_number, $order );
            }else{
                return $order_number;
            }


        }

        public function update_max_order_number(){
                $current_order_number = get_option('_op_wc_custom_order_number',0);
                if(!$current_order_number)
                {
                    $current_order_number = 1;
                }
                update_option('_op_wc_custom_order_number',$current_order_number+1);
                return $current_order_number+1;
        }

        public function update_order_number($order_id)
        {
            $pos_sequential_number_enable = $this->settings_api->get_option('pos_sequential_number_enable','openpos_general');

            if($pos_sequential_number_enable == 'yes')
            {
                $next_number = $this->update_max_order_number();
                update_post_meta( $order_id, '_op_wc_custom_order_number', $next_number );
                update_post_meta( $order_id, '_op_wc_custom_order_number_formatted', $this->formatOrderNumber($next_number ));
                return $next_number;
            }else{
                return $order_id;
            }

        }
        public function get_order_id_from_number($order_number){
            global $wpdb;
            $wp_post_meta_table = $wpdb->postmeta;
            $result_select    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' .$wp_post_meta_table. '` WHERE meta_key = "_op_wc_custom_order_number" AND meta_value=%s', $order_number) ); //phpcs:ignore
            $order_id = 0;
            if($result_select && $post_id = $result_select->post_id)
            {
                $order_id = $post_id;
            }
            if(!$order_id)
            {
                $result_select    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' .$wp_post_meta_table. '` WHERE meta_key = "_op_order_number" AND meta_value=%s', $order_number) ); //phpcs:ignore
            
                if($result_select && $post_id = $result_select->post_id)
                {
                    $order_id = $post_id;
                }
            }
            if(!$order_id)
            {
                if( is_numeric($order_number) &&  $post = get_post($order_number))
                {
                    $order_id =  $post->ID;
                }
            }
            
            return apply_filters('op_get_order_id_from_number',$order_id,$order_number);
        }
        public function get_order_id_from_local_id($order_number){
            global $wpdb;
            $wp_post_meta_table = $wpdb->postmeta;
            $result_select    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' .$wp_post_meta_table. '` WHERE meta_key = "_op_local_id" AND meta_value=%s', $order_number) ); //phpcs:ignore
            if($result_select && $post_id = $result_select->post_id)
            {
                return $post_id;
            }
            return 0;
        }
        public function get_order_id_from_order_number_format($order_number){
            global $wpdb;
            $wp_post_meta_table = $wpdb->postmeta;
            $order_number = strtolower(trim($order_number,'#'));
            $result_select    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' .$wp_post_meta_table. '` WHERE meta_key = "_op_order_number_format" AND LOWER(meta_value)=%s', $order_number) ); //phpcs:ignore
            if($result_select && $post_id = $result_select->post_id)
            {
                return $post_id;
            }
            $result_select    = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' .$wp_post_meta_table. '` WHERE meta_key = "_op_wc_custom_order_number_formatted" AND LOWER(meta_value)=%s', $order_number) ); //phpcs:ignore
            
            if($result_select && $post_id = $result_select->post_id)
            {
                return $post_id;
            }
            return 0;
        }

        public function reset_order_number($order_number_data){
            $order_number = 0;
            if(is_numeric($order_number_data))
            {
                $order_number = $order_number_data;
            }else{
                if(isset($order_number_data['order_number']) && $order_number_data['order_number'])
                {
                    $order_number = $order_number_data['order_number'];
                }
                if(isset($order_number_data['order_id']) && $order_number_data['order_id'])
                {
                    $order_id = $order_number_data['order_id'];
                    delete_metadata( 'post', $order_id, '_op_wc_custom_order_number', false );
                    delete_metadata( 'post', $order_id, '_op_wc_custom_order_number_formatted', false );
                }
                
                
            }
            $current_order_number = get_option('_op_wc_custom_order_number',0);
            if(($current_order_number - $order_number) == 0)
            {
                update_option('_op_wc_custom_order_number',($order_number - 1));
            }
        }
       
        public function op_add_order_item_meta($order_item,$_item_data){
            $product_id = $_item_data['product_id']; 
            $tmp_price = get_post_meta($product_id,'_op_weight_base_pricing',true);
            if($tmp_price == 'yes')
            {
                $options = $_item_data['options'];
                if(!empty($options))
                {
                    $weight = 0;
                    foreach($options as $option)
                    {
                        if(isset($option['option_id']) && $option['option_id'] == 'op_weight')
                        {
                            $weight = array_sum($option['value_id']);
                        }
                    }
                    if($weight > 0)
                    {
                        $order_item->add_meta_data('_op_item_weight' , $weight);

                        $product = wc_get_product($product_id);
                        $product_weight = $product->get_weight();
                        if(floatval($product_weight))
                        {
                            //$new_weight = floatval($product_weight) - $weight;
                            //$product->set_weight($new_weight);
                            //$product->save();
                        }
                    }

                }
                //$tmp_price = 'no';
            }
            
        }
        public function remove_order_items($order,$silent = false){
            $source = get_post_meta($order->get_id(),'_op_order_source',true);
            $order_id = $order->get_id();
            $tmp_items = $order->get_items();
            // revert reducted item
            $changes = array();
            
            foreach($tmp_items as $item)
            {
                if($item)
                {
                    if ( ! $item->is_type( 'line_item' ) ) {
                        continue;
                    }
                    $product            = $item->get_product();
                    $item_stock_reduced = $item->get_meta( '_reduced_stock', true );

                    if($source == 'openpos')
                    {
                        //pending outlet order
                    }else{
                      
                        if ( !$item_stock_reduced || ! $product || ! $product->managing_stock() ) {
                            continue;
                        }
                        
                        if($item_stock_reduced)
                        {
                            $qty = 1 * $item_stock_reduced;
                            $new_stock = wc_update_product_stock( $product, $qty, 'increase' ); //revert stock

                            $changes[] = array(
                                'product' => $product,
                                'from'    => $new_stock - $qty,
                                'to'      => $new_stock,
                            );

                            $item->delete_meta_data( '_reduced_stock' );
		                    $item->save();
                        }
                    }
                    

                    
                    
                }
                
            }
            
           
            if(!empty($changes) && !$silent)
            {
                wc_trigger_stock_change_notifications( $order, $changes );
            }
            
            //end
            $order->remove_order_items();
            $order->get_data_store()->set_stock_reduced( $order_id, false );
            
        }
        public function reGenerateDraftOrder($order_id,$new_order_number = 0,$new_order_format = ''){
            global $wpdb;
            $data = array(
               
                'post_status'           => 'auto-draft',
                'post_type'             => 'shop_order'
            );
            if($order_id)
            {
                $data['ID'] = $order_id;
            }
            $table = $wpdb->posts;
            if ( false === $wpdb->insert( $table, $data ) ) {
                return false;
            }else{
                $post_id = (int) $wpdb->insert_id;
              
                if($new_order_number)
                {
                    update_post_meta( $post_id, '_op_wc_custom_order_number', $new_order_number );
                }
                if($new_order_format)
                {
                    update_post_meta( $post_id, '_op_wc_custom_order_number_formatted', $new_order_format );
                }
                return  get_post($post_id);
            }
            
            
           
        }
        public function remove_draft_cart($cart_id)
        {
            $post = get_post($cart_id);
           
            if($post && $post->post_status == 'auto-draft')
            {
                wp_delete_post($cart_id,true);
            }
        }

    }
}
