<?php
/*
 * Plugin Name: WP Spotify Currently Playing
 * Description: Display the currently playing song and album from Spotify on your website
 * Version: 1.0.0
 * Author: Blake Zajac
 * Author URI: https://blakezajac.com
 * 
 *  * Please add the following variables to your wp-config.php file
 *
 * ```php
 * define( 'SPOTIFY_CLIENT_ID', ' xxxxxx' );
 * define( 'SPOTIFY_CLIENT_SECRET', 'xxxxxx' );
 * define( 'SPOTIFY_REDIRECT_URI', 'xxxxxx' );
 * ```
 */

include_once 'inc/class-spotify-currently-playing.php';
