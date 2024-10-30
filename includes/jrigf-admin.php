<?php
function jrigf_register_settings() {
    //jrigf_feed_token
    add_option( 'jrigf_feed_token', '');
    register_setting( 'jrigf_options_group', 'jrigf_feed_token', 'jrigf_callback' );
    //jrigf_feed_title
    add_option( 'jrigf_feed_title', 'As seen on Instagram');
    register_setting( 'jrigf_options_group', 'jrigf_feed_title', 'jrigf_callback' );
    //jrigf_feed_color
    add_option( 'jrigf_feed_color', '#000000');
    register_setting( 'jrigf_options_group', 'jrigf_feed_color', 'jrigf_callback' );
    //jrigf_feed_count
    add_option( 'jrigf_feed_count', '12');
    register_setting( 'jrigf_options_group', 'jrigf_feed_count', 'jrigf_callback' );
    //jrigf_feed_page
    add_option( 'jrigf_feed_page', '');
    register_setting( 'jrigf_options_group', 'jrigf_feed_page', 'jrigf_callback' );
    //jrigf_feed_selector
    add_option( 'jrigf_feed_selector', '#main');
    register_setting( 'jrigf_options_group', 'jrigf_feed_selector', 'jrigf_callback' );
}  
add_action( 'admin_init', 'jrigf_register_settings' );

function jrigf_register_options_page() {
    add_options_page('Junior Shoppable Social Feed', 'JR Social Feed', 'manage_options', 'jrigf', 'jrigf_options_page');
}
add_action('admin_menu', 'jrigf_register_options_page');

function jrigf_options_page() {
?>
    <div class="wrap">
        <h1>Junior Shoppable Social Feed<br><br></h1>
        <h3><em>A simple, Instagram feed with a shoppable click-through feature. Works with or without WooCommerce...</em></h3>
        <br>
        <form method="post" action="options.php">
          <?php settings_fields( 'jrigf_options_group' ); ?>
          <h3>Settings</h3>
            
            <table class="form-table" role="presentation">
                <tr valign="top">
                    <th scope="row"><label for="jrigf_feed_token">Instagram Token</label></th>
                    <td>
                        <input type="text" id="jrigf_feed_token" name="jrigf_feed_token" value="<?php echo get_option('jrigf_feed_token'); ?>" />                      
                        <p class="description"><a target="_blank" href="https://www.instagram.com/oauth/authorize?app_id=2441378269449374&redirect_uri=https://junior.london/instagram/&scope=user_profile,user_media&response_type=code">Click here to get your token</a></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="jrigf_feed_title">Feed Title</label></th>
                    <td>
                        <input type="text" id="jrigf_feed_title" name="jrigf_feed_title" value="<?php echo get_option('jrigf_feed_title'); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="jrigf_feed_color">Shoppable icon colour</label></th>
                    <td>                        
                        <input type="text" class="my-color-field" id="jrigf_feed_color" name="jrigf_feed_color" value="<?php echo get_option('jrigf_feed_color'); ?>" />
                    </td>
                </tr>
                <tr valign="top">                    
                    <th scope="row"><label for="jrigf_feed_count">Number of Instagram posts to show (25 max)</label></th>
                    <td>
                        <input type="number" min="1" max="25" id="jrigf_feed_count" name="jrigf_feed_count" value="<?php echo get_option('jrigf_feed_count'); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th colspan="2">                        
                        <h3>Use shortcode [juniorfeed] to place the Instagram feed. Or choose page and location below:</h3>
                    </th>
                </tr>               
                <tr valign="top">
                    <th scope="row"><label for="jrigf_feed_page">Feed Page</label></th>
                    <td>
                        <?php
                        printf(
                            /* translators: %s: Select field to choose the front page. */
                            //__( 'Homepage: %s' ),
                            wp_dropdown_pages(
                                array(
                                    'name'              => 'jrigf_feed_page',
                                    'echo'              => 0,
                                    'show_option_none'  => __( '&mdash; Select &mdash;' ),
                                    'option_none_value' => '0',
                                    'selected'          => get_option( 'jrigf_feed_page' ),
                                )
                            )
                        );
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="jrigf_feed_title">Feed Selector</label></th>
                    <td>
                        <input type="text" id="jrigf_feed_selector" name="jrigf_feed_selector" value="<?php echo get_option('jrigf_feed_selector'); ?>" />
                    </td>
                </tr>
            </table>
          <?php  submit_button(); ?>        
        </form>
        
        <div id="jrigf-shoppable-tags" class="jrigf-shoppable-tags"></div>
        <br><br>
        <p class="description">Thank you for creating with <a href="https://junior.london" target="_blank">Junior</a></p>
        
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
                foreach ( $products as $post ) : setup_postdata( $post ); ?>
                    var productCamelized = camelize("<?php echo get_the_title($post->ID); ?>");
                    var productLowercase = '#'+productCamelized.toLowerCase();
                    allProducts.push(productLowercase);
            <?php endforeach; wp_reset_postdata( $post ); ?>
            //console.log( allProducts );
            
            if ( typeof allProducts != 'undefined' ) {
                jrigfGuideText = '<h3>Your Shoppable Hashtags</h3><p>';
                jrigfGuideText += allProducts.join(", ");
                jrigfGuideText += '</p><p class="description">Next time you upload an Instagram post on your phone, use any of these hashtags to make it shoppable</p>';
            }
            
            jrigfShoppableTags = document.getElementById('jrigf-shoppable-tags');
            jrigfShoppableTags.innerHTML = jrigfGuideText;
            
        </script>
        
    </div>
<?php
}

add_action( 'admin_enqueue_scripts', 'jrigf_mw_enqueue_color_picker' );
function jrigf_mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('jrigf-admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
?>