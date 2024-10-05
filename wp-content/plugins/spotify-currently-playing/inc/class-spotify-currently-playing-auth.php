<?php

class Spotify_Currently_Playing_Auth {
    protected $base_url_authorize;
    protected $base_url_api;
    protected $base_url_token;
    protected $client_id;
    protected $client_secret; // dont think we need this, keep for now
    protected $redirect_uri;

    public function __construct() {
        $this->base_url_authorize = 'https://accounts.spotify.com/authorize';
        $this->base_url_api = 'https://api.spotify.com/v1';
        $this->base_url_token = 'https://accounts.spotify.com/api/token';

        $this->client_id = defined( 'SPOTIFY_CLIENT_ID' ) ? SPOTIFY_CLIENT_ID : null;
        $this->client_secret = defined( 'SPOTIFY_CLIENT_SECRET' ) ? SPOTIFY_CLIENT_SECRET : null;
        $this->redirect_uri = defined( 'SPOTIFY_REDIRECT_URI' ) ? SPOTIFY_REDIRECT_URI : null;
    }

    public function get_base_url_authorize() {
        return $this->base_url_authorize;
    }

    public function get_base_url_api() {
        return $this->base_url_api;
    }

    public function get_base_url_token() {
        return $this->base_url_token;
    }

    public function get_client_id() {
        return $this->client_id;
    }

    public function get_client_secret() {
        return $this->client_secret;
    }

    public function get_redirect_uri() {
        return $this->redirect_uri;
    }
}
