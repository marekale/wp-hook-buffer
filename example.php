<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('class-hook-buffer.php');

class CustomWooCommerceEmailContent extends HookBuffer {
    private $custom_html='';
    public function __construct( $custom_html='<p>Custom email content here...</p>' ) {
        parent::__construct( 'buffer_example', 'woocommerce_email_header', 'woocommerce_email_order_details' );
        $this->custom_html = (string)$custom_html;
    }
    
    protected function filter() {
        $original_content = parent::filter();
        $custom_content   = $this->custom_html;
        return $custom_content;
    }
}