<?php

class Spotify_Currently_Playing_Requests {
    protected $auth;
    protected $api;

    public function __construct( Spotify_Currently_Playing_Auth $auth, Spotify_Currently_Playing_Api $api, Spotify_Currently_Playing_Logging $logging ) {
        $this->auth = $auth;
        $this->api = $api;
    }

    /**
     * Request an authorization token from Spotify.
     * 
     * This method sends a POST request to Spotify's authorization endpoint to obtain an authorization token.
     * If successful, the authorization token is returned.
     * 
     * @since 1.0.0
     * 
     * @return string|bool The authorization token if successful, false otherwise.
     */
    public function get_authorization_token() {
        SCP()->logging->write_log( 'A request has been made to get the Spotify authorization token.' );

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

        $result = $this->api->post_request( $endpoint );

        if ( $result ) {
            SCP()->logging->write_log( 'The Spotify authorization token has been generated.' );
        } else {
            SCP()->logging->write_log( 'The Spotify authorization token could not be generated.' );
            return false;
        }

        return $result;
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
    public function get_access_token( $authorization_code = null ) {
        SCP()->logging->write_log( 'A request has been made to generate the Spotify access token.' );

        $base_url = $this->auth->get_base_url_token();

        if ( ! $authorization_code ) {
            SCP()->logging->write_log( 'No authorization code was provided. Cannot request access token.' );
            return false;
        }

        $endpoint = $base_url . '?' . http_build_query( array(
            'grant_type' => 'authorization_code',
            'code' => $authorization_code,
            'redirect_uri' => get_home_url(),
        ) );

        $result = $this->api->post_request( $endpoint );

        if ( $result ) {
            SCP()->logging->write_log( 'The Spotify access token has been generated.' );
            $_SESSION['spotify_access_token'] = $result;
        } else {
            SCP()->logging->write_log( 'The Spotify access token could not be generated.' );
            return false;
        }

        return $result;
    }
}
