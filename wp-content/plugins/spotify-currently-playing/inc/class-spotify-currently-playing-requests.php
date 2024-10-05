<?php

class Spotify_Currently_Playing_Requests {
    protected $auth;
    protected $api;

    public function __construct( Spotify_Currently_Playing_Auth $auth, Spotify_Currently_Playing_Api $api ) {
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

        $endpoint = '?' . http_build_query( array(
            'client_id' => $this->auth->get_client_id(),
            'response_type' => 'code',
            'redirect_uri' => $this->auth->get_redirect_uri(),
            'scope' => 'user-read-currently-playing',
            'state' => bin2hex( random_bytes( 16 ) ),
            'show_dialog' => 'true',
        ) );

        $result = $this->api->post_request( 'authorize', $endpoint );

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
     * @param string $authorization_code The authorization code received from the previous authorization step.
     * @see Spotify_Currently_Playing_Requests::get_authorization_token()
     * 
     * @return string|bool The access token if successful, false otherwise.
     */
    public function get_access_token( $authorization_code = null ) {
        SCP()->logging->write_log( 'A request has been made to generate the Spotify access token.' );

        if ( ! $authorization_code ) {
            SCP()->logging->write_log( 'No authorization code was provided. Cannot request access token.' );
            return false;
        }

        $endpoint = '?' . http_build_query( array(
            'grant_type' => 'authorization_code',
            'code' => $authorization_code,
            'redirect_uri' => $this->auth->get_redirect_uri(),
        ) );

        $result = $this->api->post_request( 'token', $endpoint );

        if ( $result ) {
            SCP()->logging->write_log( 'The Spotify access token has been generated.' );
            $_SESSION['spotify_access_token'] = $result;
        } else {
            SCP()->logging->write_log( 'The Spotify access token could not be generated.' );
            return false;
        }

        return $result;
    }

    /**
     * Retrieve the currently playing track from Spotify.
     * 
     * This method sends a GET request to Spotify's API to fetch information
     * about the users' currently playing track. If successful, it returns an array
     * containing the track's URL, title, artist, and album image.
     * 
     * @since 1.0.0
     * 
     * @return array|false An array containing track information if successful, false otherwise.
     */
    public function get_currently_playing() {
        SCP()->logging->write_log( 'A request has been made to get the currently playing track.' );

        $endpoint = '/me/player/currently-playing';

        $result = $this->api->get_request( $endpoint );

        if ( $result && isset( $result->item ) ) {
            SCP()->logging->write_log( 'The currently playing track has been retrieved.' );

            return array(
                'is_playing' => $result->is_playing ?? false,
                'url' => $result->item->external_urls->spotify ?? '',
                'title' => $result->item->name ?? '',
                'artist' => implode( ', ', array_map( function( $artist ) { return $artist->name; }, $result->item->artists ) ),
                'album_image' => $result->item->album->images[0]->url ?? '',
            );
        } else {
            SCP()->logging->write_log( 'The currently playing track could not be retrieved.' );
            return false;
        }
    }
}
