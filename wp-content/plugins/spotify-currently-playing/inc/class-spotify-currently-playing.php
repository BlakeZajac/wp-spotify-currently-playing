<?php

/**
 * Class Spotify_Currently_Playing
 */
class Spotify_Currently_Playing {
    
    /**
     * Singleton class instance
     * 
     * @var Spotify_Currently_Playing
     */
    private static $instance = null;

    /**
     * Authentication instance
     * 
     * @var Spotify_Currently_Playing_Auth
     */
    public $auth = null;

    /**
     * API instance
     * 
     * @var Spotify_Currently_Playing_Api
     */
    public $api = null;

    /**
     * Logging instance
     * 
     * @var Spotify_Currently_Playing_Logging
     */
    public $logging = null;    

    /**
     * Requests instance
     * 
     * @var Spotify_Currently_Playing_Requests
     */
    public $requests = null;

    /**
     * Spotify_Currently_Playing constructor
     */
    public function __construct() {
        $this->includes();
        $this->init();
        $this->hooks();
    }

    public static function get_instance() {
        if ( is_null ( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function hooks() {

    }

    public function includes() {
        include_once 'class-spotify-currently-playing-logging.php';
        include_once 'class-spotify-currently-playing-auth.php';
        include_once 'class-spotify-currently-playing-api.php';
        include_once 'class-spotify-currently-playing-requests.php';
    }

    public function init() {
        $this->auth = new Spotify_Currently_Playing_Auth();
        $this->api = new Spotify_Currently_Playing_Api( $this->auth );
        $this->logging = new Spotify_Currently_Playing_Logging();
        $this->requests = new Spotify_Currently_Playing_Requests( $this->api, $this->logging );
    }
}

/**
 * Get access to the class via 'SCP()'
 * 
 * @return Spotify_Currently_Playing
 */
function SCP() {
    return Spotify_Currently_Playing::get_instance();
}

/**
 * Add to globals for backwards compatibility
 */
$GLOBALS['SCP'] = SCP();
