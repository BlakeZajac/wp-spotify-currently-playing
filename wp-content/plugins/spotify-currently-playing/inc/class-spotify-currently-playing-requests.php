<?php

class Spotify_Currently_Playing_Requests {
    protected $api;
    protected $logging;

    public function __construct( Spotify_Currently_Playing_Api $api, Spotify_Currently_Playing_Logging $logging ) {
        $this->api = $api;
        $this->logging = $logging;
    }

    /**
     * To get the currentlly playing details, Spotify will first need us to get an authorisation token.
     * Once we have the authorisation token, we can use it to get the access token.
     * 
     * @TODO - I think the authorisation token is redundant as the user will need to use their own client ID and client secret to get the access token.
     * I am keeping the method until the integration is finalised.
     * 
     * We then make a POST request to the Spotify API to get the access token.
     * Once we have the access token, we can use it to make a GET request to the Spotify API to get the currently playing details.
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
