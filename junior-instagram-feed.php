<?php

/**
 * Plugin Name: Junior Shopable Social Feed
 * Plugin URI:  
 * Description: A simple, Instagram feed with a shoppable click-through feature. Works with or without WooCommerce. 
 * Version: 1.0.4
 * Author: Junior
 * Author URI: https://junior.london
 * Text Domain: junior-instagram-feed
 * Domain Path: /languages
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Get admin page
include( plugin_dir_path( __FILE__ ) . 'includes/jrigf-admin.php');
//
//
$jrigf_shortcode_used = false;
//
// Check for shortcode
function jrigf_output_shortcode() {    
    // Enqueue scripts     
    // Fire this function to locate the shortcode
    $jrigf_shortcode_location = jrigf_output();
    return $jrigf_shortcode_location;
}
// Register shortcode
add_shortcode( 'juniorfeed', 'jrigf_output_shortcode' );
//
// Check if this is the chosen page
add_action( 'template_redirect', 'jrigf_page_check' );
function jrigf_page_check() {
    // To prevent repeats
    global $jrigf_shortcode_used;
    // Check current page
    $page_id = get_queried_object_id();
    if( $page_id == get_option('jrigf_feed_page') ) {
        if ( !$jrigf_shortcode_used ) {
            // Run feed plugin
            $jrigf_product_list = jrigf_product_list();
            //
            //add_action( 'wp_enqueue_scripts', 'jrigf_frontend_scripts_and_styles' );
        }
    }
}
// Check if function already exists
// Main output function to display the feed
if ( !function_exists( 'jrigf_output' ) ) {
    //  Get the list of user and store them as an AJAX
    function jrigf_output() {
        ob_start();
        ?>
            <div id="jr-instagram-feed-wrap" class="jr-instagram-feed-clearfix">
            </div>
        <?php
                
        //fire this function when shortcode is used or when this page is selected
        $jrigf_product_list = jrigf_product_list();
        //return $jrigf_product_list;
        
        return ob_get_clean();
    }
}
//
//prepare product list
if ( !function_exists( 'jrigf_product_list' ) ) {
    //  Get the list of user and store them as an AJAX
    function jrigf_product_list() {
        // To prevent repeats
        global $jrigf_shortcode_used;
        if ( !$jrigf_shortcode_used ) {
        //ob_start();
        ?>
        <script>
            //console.log('scripting...');
            //camelize function
            function camelize(str) {
              return str.replace(/\W+(.)/g, function(match, chr)
               {
                    return chr.toUpperCase();
                });
            }
            var allProducts = [];
            <?php         
                $products = get_posts('post_type=product&posts_per_page=-1');
                if( $products ) {
                foreach ( $products as $post ) : setup_postdata( $post ); ?>
                    var productCamelized = camelize("<?php echo get_the_title($post->ID); ?>");
                    var productLowercase = productCamelized.toLowerCase();
                    allProducts.push({name:productLowercase,url:'<?php echo get_the_permalink($post->ID); ?>'});
            <?php endforeach; wp_reset_postdata( $post ); }; ?>
            //console.log( allProducts );            
        </script>
        <?php
        //
        //return ob_get_clean();
        $jrigf_shortcode_used = true;
        }
    }
}
/*
 *	Load the text domain
 */
function jrigf_load_plugin_textdomain() {
    load_plugin_textdomain( 'junior-instagram-feed', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
// Include it when the plugin loads
add_action( 'plugins_loaded', 'jrigf_load_plugin_textdomain' );

/*
 *	Add CSS and JS to the frontend
 */
function jrigf_frontend_scripts_and_styles() {
    wp_register_style( 'jrigf_frontend_css', plugins_url( 'junior-shopable-social-feed/public/css/jrigf-styles.css' ) );
    wp_enqueue_style( 'jrigf_frontend_css' ); 
    wp_register_script( 'jrigf_frontend_js', plugins_url( 'junior-shopable-social-feed/public/js/jrigf-scripts.js' ), array('jquery'),'',true );
    wp_enqueue_script( 'jrigf_frontend_js' );
    // Localize the script with new data
    // use php to see if shortcode is used and change insert point
    $jrigf_feed_insert_point = get_option('jrigf_feed_selector');
    $jrigf_feed_page = get_option('jrigf_page');
    $jrigf_title = get_option('jrigf_feed_title');
    $jrigf_fill = get_option('jrigf_feed_color');
    $jrigf_feed_count = get_option('jrigf_feed_count');
    $jrigf_token = get_option('jrigf_feed_token');
    //
    $jrigf_variables_array = array(
        'jrigfFeedInsertPoint' => $jrigf_feed_insert_point,
        'jrigfTitle' => $jrigf_title,
        'jrigfFill' => $jrigf_fill,
        'jrigfCount' => $jrigf_feed_count,
        'jrigfToken' => $jrigf_token
    );
    wp_localize_script( 'jrigf_frontend_js', 'jrigf_variables_array', $jrigf_variables_array );
}
add_action( 'wp_enqueue_scripts', 'jrigf_frontend_scripts_and_styles' );