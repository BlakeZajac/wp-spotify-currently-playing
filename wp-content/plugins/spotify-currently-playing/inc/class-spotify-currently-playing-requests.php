<?php

class Spotify_Currently_Playing_Requests {
    protected $auth;
    protected $api;
    protected $logging;

    public function __construct( Spotify_Currently_Playing_Auth $auth, Spotify_Currently_Playing_Api $api, Spotify_Currently_Playing_Logging $logging ) {
        $this->auth = $auth;
        $this->api = $api;
        $this->logging = $logging;
    }

    /**
     * Generate the Spotify authorization URL.
     * 
     * This method creates a URL that, when accessed, will prompt the user to authorize the application to access their Spotify data.
     * The URL includes necessary paramaters such as client ID, redirect URI, and required scopes.
     * 
     * @since 1.0.0
     * 
     * @return string The complete Spotify authorization URL.
     */
    public function get_authorization_token() {
        $this->logging->write_log( 'A request has been made to get the Spotify authorization token.' );

        $base_url = $this->auth->get_base_url_authorize();
        $client_id = $this->auth->get_client_id();

        $endpoint = $base_url . '?' . http_build_query( array(
            'client_id' => $client_id,
            'response_type' => 'code',
            'redirect_uri' => get_home_url(),
            'scope' => 'user-read-currently-playing',
            'state' => bin2hex( random_bytes( 16 ) ),
            'show_dialog' => 'true',
        ) );

        return $endpoint;
    }

    /**
     * Request an access token from Spotify.
     * 
     * This method sends a POST request to Spotify's token endpoint to obtain an access token using the authorization code
     * received from the previous authorization step. If successful, the access token is stored in the session.
     * 
     * @since 1.0.0
     * 
     * @return string|bool The access token if successful, false otherwise.
     */
    public function request_access_token() {
        $this->logging->write_log( 'A request has been made to generate the Spotify access token.' );

        $base_url = $this->auth->get_base_url_token();
        $authorization_code = ''; // @todo Get this from the previous method

        $endpoint = $base_url . '?' . http_build_query( array(
            'grant_type' => 'authorization_code',
            'code' => $authorization_code,
            'redirect_uri' => get_home_url(),
        ) );

        $result = $this->api->post_request( $endpoint );

        if ( $result ) {
            $this->logging->write_log( 'The Spotify access token has been generated.' );
            $_SESSION['spotify_access_token'] = $result;
        } else {
            $this->logging->write_log( 'The Spotify access token could not be generated.' );
            return false;
        }

        return $result;
    }
}
