<?php

/**
 * Description
 *
 * @author MarekA
 */

if (!class_exists( 'HookBuffer' ) ) :

class HookBuffer {
	
	private static $buffers = [];
	private $buffer_name = '';
	private $buffer = '';
	private $buffer_status = 'waiting';
	private $priority1;
	private $priority2;
	private $tag1;
	private $tag2;
	private $output=FALSE;

	public function __construct( $buffer_name, $tag1='', $tag2='' ) {
		if ( !( function_exists( 'add_action' ) 
			&&  function_exists( 'remove_action' ) )
			|| self::buffer_exists( $buffer_name ) ) {
			throw new Exception;
		}
		$this->buffer_name = (string)$buffer_name;
		$this->tag1 = (string)$tag1;
		$this->tag2 = (string)$tag2;
		$this->init();
		self::$buffers[$buffer_name] = $this;
	}
	
	public static function buffer_exists( $buffer_name ) {
		return array_key_exists((string)$buffer_name, self::$buffers);
	}
	
	public static function buffer_ready( $buffer_name ) {
		$b = self::b( (string)$buffer_name );
		return ( $b !== FALSE && $b->get_status() === 'finished' ) ? TRUE : FALSE;
	}
	
	public static function b( $buffer_name ) {
		$buffer_name = (string)$buffer_name;
		return self::buffer_exists($buffer_name) ? 
				self::$buffers[$buffer_name] : FALSE;
	}
	
	private function init() {
		$tag1 = $this->tag1;
		$tag2 = $this->tag2;
		if ( $tag1 ) {
			if ( $tag2 ) {
				$this->add_actions( 'last', 'first' );
			} else {
				$this->tag2 = $tag1;
				$this->add_actions( 'first', 'last' );
			}
		} else {
			if ( $tag2 ) {
				$this->tag1 = NULL;
				$this->add_actions( NULL, 'first' );
				$this->start_buffering( );
			} else {
				return;
			}
		}
	}
	
	private function init_priorities( $position1, $position2 ) {
		if ( $position1 === 'first' ) {
			$this->priority1 = -9999;
		} elseif ( $position1 === 'last' ) {
			$this->priority1 = 9999;
		} else {
			$this->priority1 = NULL;
		}
		
		if ( $position2 === 'first' ) {
			$this->priority2 = -9999    ;
		} elseif ( $position2 === 'last' ) {
			$this->priority2 = 9999;
		} else {
			$this->priority2 = NULL;
		}
	}
	
	private function add_actions( $position1, $position2 ) {
		
		$this->init_priorities($position1, $position2);
		
		if ( $this->tag1 ) {
			add_action( 
				$this->tag1,
				[ $this, 'start_buffering' ],
				$this->priority1 );
		}
		if ( $this->tag2 ) {
			add_action(
				$this->tag2, 
				[ $this, 'stop_buffering' ],
				$this->priority2 );
		}
		return $this;
	}
	
	public function start_buffering( $var=NULL ) {
		if ( $this->get_status() === 'waiting' ) {
			$this->buffer_status = 'buffering';
			ob_start();
		}	
		return $var;
	}
	
	public function stop_buffering( $var=NULL ) {
		if ( $this->get_status() === 'buffering' ) {
			$this->buffer = ob_get_contents();
			ob_end_clean();
			$this->buffer_status = 'finished';
			$this->remove_actions();
			if ( $this->output ) {
				$this->output();
			}
		} else {
                    $this->buffer_status = 'finished';
                    $this->remove_actions();
                }
		return $var;
	}
	
	public function get_status() {
		return $this->buffer_status;
	}
	
	public function get() {
		return $this->get_status() === 'finished' ? $this->filter() : FALSE;
	}
	
	protected function filter() {
		return $this->buffer;
	}
	
	public function output() {
		if ( $this->get_status() === 'finished' ) {
			echo $this->get();
		} else {
			$this->output = TRUE;
		}
		return $this;
	}
	
	private function remove_actions() {
		remove_action( 
				$this->tag1,
				[ $this, 'start_buffering' ],
				$this->priority1 );
		remove_action(
				$this->tag2, 
				[ $this, 'stop_buffering' ],
				$this->priority2 );
		return $this;
	}
}

endif;
