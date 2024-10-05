<?php

class Spotify_Currently_Playing_Api {
    protected $auth;
    protected $base_url_authorize;
    protected $base_url_api;
    protected $base_url_token;

    public function __construct( Spotify_Currently_Playing_Auth $auth ) {
        $this->auth = $auth;
        $this->base_url_authorize = $auth->get_base_url_authorize();
        $this->base_url_api = $auth->get_base_url_api();
        $this->base_url_token = $auth->get_base_url_token();
    }

    public function get_request( $endpoint ) {
        return $this->make_request( $this->get_base_url(), $endpoint, 'GET' );
    }

    public function post_request( $endpoint, $data = null ) {
        return $this->make_request( $this->get_base_url(), $endpoint, 'POST', $data );
    }

    protected function get_base_url( $base_url = 'api' ) {
        switch( $base_url ) {
            case 'authorize':
                return $this->base_url_authorize;
            case 'token':
                return $this->base_url_token;
            default:
                return $this->base_url_api;
        }
    }

    protected function make_request( $base_url, $endpoint, $method = 'POST', $data = null ) {
        $url = $base_url . $endpoint;

        $args = array(
            'method' => $method,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode( $this->auth->get_client_id() . ':' . $this->auth->get_client_secret() ),
            )
        );

        if ( $method === 'POST' ) {
            $args['body'] = json_encode( $data );
        }

        $response = wp_remote_request( $url, $args );
        $response_code = wp_remote_retrieve_response_code( $response );
        
        if ( is_wp_error( $response ) ) {
            SCP()->logging->write_log( "API request failed with code $response_code", $response );
            return false;
        }

        $response_body = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response_body ) ) {
            SCP()->logging->write_log( 'Error retrieving API request response body', $response_body );
            return false;
        }

        $response_body = json_decode( $response_body );

        if ( ! is_object( $response_body ) ) {
            SCP()->logging->write_log( 'Error converting response body to JSON: ' . json_last_error_msg(), $response_body );
            return false;
        }

        if ( $response_code !== 200 && $response_code !== 201 ) {
            SCP()->logging->write_log( "API request failed with code $response_code", $response_body );
            return false;
        }

        return $response_body;
    }
}
