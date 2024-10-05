<?php

class Spotify_Currently_Playing_Logging {

    protected $display = false;

    public function __construct() {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
            $this->display = true;
        }
    }

    /**
     * Write a log entry
     */
    public function write_log( $message, $object = '' ) {
        if ( is_string( $message ) ) {
            error_log( $message );
        } else {
            error_log( 'Non-string message passed to write_log: ' . print_r( $message, true ) );
        }

        if ( $object instanceof WP_Error ) {
			error_log( sprintf( 'WP Error code: %s, message: %s', $object->get_error_code(), $object->get_error_message() ) );
		} else if ( is_array( $object ) || is_object( $object ) ) {
			error_log( print_r( $object, true ) );
		} else {
			error_log( (string) $object );
		}

        // Print to screen if log_display is set
        if ( $this->display ) {
            var_dump( $message, $object );
        }
    }
}
