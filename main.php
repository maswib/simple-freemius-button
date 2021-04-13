<?php
/**
 * Plugin Name: Simple Freemius Button
 * Plugin URI: https://wahyuwibowo.com/projects/simple-freemius-button/
 * Description: Create Freemius purchase button on your WordPress site
 * Author: Wahyu Wibowo
 * Author URI: https://wahyuwibowo.com
 * Version: 1.0.1
 * Text Domain: simple-freemius-button
 * Domain Path: languages
 * 
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Simple_Freemius_Button {
    
    private static $_instance = NULL;
    
    public static $default_settings = array();
    
    /**
     * Initialize all variables, filters and actions
     */
    public function __construct() {
        // Plugin Folder Path.
        if ( !defined( 'SIMPLE_FREEMIUS_BUTTON_PLUGIN_DIR' ) ) {
            define( 'SIMPLE_FREEMIUS_BUTTON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        // Plugin Folder URL.
        if ( !defined( 'SIMPLE_FREEMIUS_BUTTON_PLUGIN_URL' ) ) {
            define( 'SIMPLE_FREEMIUS_BUTTON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }
        
        add_action( 'wp_loaded',                 array( $this, 'register_scripts' ) );
        add_filter( 'http_request_args',         array( $this, 'dont_update_plugin' ), 5, 2 );
        add_shortcode( 'simple_freemius_button', array( $this, 'add_shortcode' ) );
    }

    /**
     * retrieve singleton class instance
     * @return instance reference to plugin
     */
    public static function instance() {
        if ( NULL === self::$_instance ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function register_scripts() {
        wp_register_script( 'freemius-checkout', 'https://checkout.freemius.com/checkout.min.js', array( 'jquery' ), false, true );
        wp_register_script( 'simple-freemius-button',   SIMPLE_FREEMIUS_BUTTON_PLUGIN_URL . 'assets/js/button.js', array( 'freemius-checkout' ), false, true );
    }
        
    public function dont_update_plugin( $r, $url ) {
        if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) ) {
            return $r; // Not a plugin update request. Bail immediately.
        }
        
        $plugins = json_decode( $r['body']['plugins'], true );
        unset( $plugins['plugins'][plugin_basename( __FILE__ )] );
        $r['body']['plugins'] = json_encode( $plugins );
        
        return $r;
    }
    
    public function add_shortcode( $attributes ) {
        $defaults = array(
            'plugin_id'              => '',
            'plan_id'                => '',
            'public_key'             => '',
            'buy_button_link_prefix' => '#buy-',
            'buy_button_selector'    => '.freemius-buy-button a',
            'free_trial_link_prefix' => '#trial-',
            'free_trial_selector'    => '.freemius-trial-button a',
            'currency'               => 'usd', // usd, eur, gbp
        );
        
        $attributes = shortcode_atts( $defaults, $attributes );
        
        wp_enqueue_script( 'freemius-checkout' );
        wp_enqueue_script( 'simple-freemius-button' );
        
        wp_localize_script( 'simple-freemius-button', 'Simple_Freemius_Button', array(
            'plugin_id'              => $attributes['plugin_id'],
            'plan_id'                => $attributes['plan_id'],
            'public_key'             => $attributes['public_key'],
            'buy_button_link_prefix' => $attributes['buy_button_link_prefix'],
            'buy_button_selector'    => $attributes['buy_button_selector'],
            'free_trial_link_prefix' => $attributes['free_trial_link_prefix'],
            'free_trial_selector'    => $attributes['free_trial_selector'],
            'currency'               => $attributes['currency'],
        ) );
    }
}

Simple_Freemius_Button::instance();