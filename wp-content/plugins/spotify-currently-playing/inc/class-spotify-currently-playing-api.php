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

    /**
     * Performs a GET request to the Spotify API.
     * 
     * @since 1.0.0
     * 
     * @param string $base_url The base URL to use for the request. Default is 'api'.
     * @param string $endpoint The API endpoint to request.
     * 
     * @return mixed|false The API response as an object, or false on failure.
     */
    public function get_request( $base_url = 'api', $endpoint ) {
        return $this->make_request( $this->get_base_url( $base_url ), $endpoint, 'GET' );
    }

    /**
     * Performs a POST request to the Spotify API.
     * 
     * @since 1.0.0
     * 
     * @param string $base_url The base URL to use for the request. Default is 'api'.
     * @param string $endpoint The API endpoint to request.
     * @param array|null $data The data to send with the POST request.
     * 
     * @return mixed|false The API response as an object, or false on failure.
     */
    public function post_request( $base_url = 'api', $endpoint, $data = null ) {
        return $this->make_request( $this->get_base_url( $base_url ), $endpoint, 'POST', $data );
    }

    /**
     * Gets the appropriate base URL based on the given type.
     * 
     * @since 1.0.0
     * 
     * @param string $base_url The base URL to use for the request. Default is 'api'.
     * 
     * @return string The base URL.
     */
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

    /**
     * Makes an HTTP request to the Spotify API.
     * 
     * @since 1.0.0
     * 
     * @param string $base_url The base URL to use for the request.
     * @param string $endpoint The API endpoint to request.
     * @param string $method The HTTP method to use for the request. Default is 'POST'.
     * @param array|null $data The data to send with the POST request.
     * 
     * @return mixed|false The API response as an object, or false on failure.
     */
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
