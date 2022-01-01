<?php
//install_plugins_tabs
if(!class_exists('OP_Addon'))
{
    class OP_Addon{
        public function __construct()
        {
            add_filter('install_plugins_tabs',array($this,'install_plugins_tabs'),10,1);
        }
        public function install_plugins_tabs($tabs){
            $tabs['op_addons']   = _x( 'OpenPOS Addons', 'openpos' );
            return $tabs;
        }
    }
}