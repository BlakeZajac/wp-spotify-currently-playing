<?php

class Spotify_Currently_Playing_Login {
    protected $api;
    protected $logging;

    public function __construct( Spotify_Currently_Playing_Api $api, Spotify_Currently_Playing_Logging $logging ) {
        $this->api = $api;
        $this->logging = $logging;
    }

    /**
     * 
     */
    public function get_authorisation_token() {
        $this->logging->write_log( 'An attempt to get the Spotify authorisation token was made.' );

        $base_url = 'https://accounts.spotify.com/authorize';
        $client_id = '';

        $endpoint = $base_url . '?' . http_build_query( array(
            'client_id' => $client_id,
            'response_type' => 'code',
            'redirect_uri' => get_home_url(),
            'scope' => 'user-read-currently-playing',
            'state' => bin2hex( random_bytes( 16 ) ),
            'show_dialog' => 'true',
        ) );

        /**
         * Debugging
         */
        $this->logging->write_log( 'Endpoint: ' . print_r( $endpoint, true ) );
    }
}
