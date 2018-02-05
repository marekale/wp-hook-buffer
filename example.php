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

// create the instance before 'woocommerce_email_header' hook fires, 
// and output filtered content just before 'woocommerce_email_order_details' hook
(new CustomWooCommerceEmailContent('<p>New WooCommerce order</p>'))->output();




add_action( 'marale_action_1', function () {
	$b = new HookBuffer( 'buffer2', 'next' );
	echo 'Output in marale_action_1';
} );


add_action( 'marale_action_2', function () {
	echo 'Output in marale_action_2';	
} );


add_action( 'marale_action_3', function () {
	var_dump( [
		'buffer1' => HookBuffer::buffer_exists('buffer1') ? [
			HookBuffer::b('buffer1')->get(),
			HookBuffer::b('buffer1')->get_status(),
			] : 'Buffer doesn\'t exist',
		'buffer2' => HookBuffer::buffer_exists('buffer2') ? [
			HookBuffer::b('buffer2')->get(),
			HookBuffer::b('buffer2')->get_status(),
			] : 'Buffer doesn\'t exist',
		'buffer3' => HookBuffer::buffer_exists('buffer3') ? [
			HookBuffer::b('buffer3')->get(),
			HookBuffer::b('buffer3')->get_status(),
			] : 'Buffer doesn\'t exist',
		'buffer4' => HookBuffer::buffer_exists('buffer4') ? [
			HookBuffer::b('buffer4')->get(),
			HookBuffer::b('buffer4')->get_status(),
			] : 'Buffer doesn\'t exist',
	] );
        
//        var_dump result:
//        
//        array(4) {
//          ["buffer1"]=>
//          array(2) {
//            [0]=>
//            string(25) "Output in marale_action_1"
//            [1]=>
//            string(8) "finished"
//          }
//          ["buffer2"]=>
//          array(2) {
//            [0]=>
//            string(25) "Output in marale_action_2"
//            [1]=>
//            string(8) "finished"
//          }
//          ["buffer3"]=>
//          array(2) {
//            [0]=>
//            string(56) "Output between $b2 instance creation and marale_action_2"
//            [1]=>
//            string(8) "finished"
//          }
//          ["buffer4"]=>
//          array(2) {
//            [0]=>
//            string(50) "Output between marale_action_2 and marale_action_3"
//            [1]=>
//            string(8) "finished"
//          }
//        }
} );



$b1 = new HookBuffer( 'buffer1', 'next' );

do_action( 'marale_action_1' );

$b2 = new HookBuffer( 'buffer3', NULL, 'next' );

echo 'Output between $b2 instance creation and marale_action_2';

$b3 = new HookBuffer( 'buffer4', 'next', 'marale_action_3' );

do_action( 'marale_action_2' );

echo 'Output between marale_action_2 and marale_action_3';

do_action( 'marale_action_3' );
