<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * After setup theme hook
 */
function yoga_fitness_theme_setup(){
    /*
     * Make chile theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'yoga-fitness', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'yoga_fitness_theme_setup' );

function yoga_fitness_styles() {
    $my_theme = wp_get_theme();
	$version = $my_theme['Version'];

    if( blossom_spa_is_woocommerce_activated() ){
        $dependencies = array( 'blossom-spa-woocommerce', 'owl-carousel', 'blossom-spa-google-fonts' );  
    }else{
        $dependencies = array( 'owl-carousel', 'blossom-spa-google-fonts' );
    }

    wp_enqueue_style( 'yoga-fitness-parent-style', get_template_directory_uri() . '/style.css', $dependencies );

    wp_enqueue_script( 'yoga-fitness', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ), $version, true );
    
    $array = array( 
        'url'        => admin_url( 'admin-ajax.php' ),
    ); 
    wp_localize_script( 'yoga-fitness', 'yoga_fitness_data', $array ); 
}
add_action( 'wp_enqueue_scripts', 'yoga_fitness_styles', 10 );

//Remove a function from the parent theme
function yoga_fitness_remove_parent_filters(){ //Have to do it after theme setup, because child theme functions are loaded first
    remove_action( 'customize_register', 'blossom_spa_customizer_theme_info' );
    remove_action( 'customize_register', 'blossom_spa_customize_register_appearance' );
    remove_action( 'wp_enqueue_scripts', 'blossom_spa_dynamic_css', 99 );
}
add_action( 'init', 'yoga_fitness_remove_parent_filters' );

function yoga_fitness_customize_register( $wp_customize ) {
    
    $wp_customize->add_section( 'theme_info', array(
        'title'       => __( 'Demo & Documentation' , 'yoga-fitness' ),
        'priority'    => 6,
    ) );
    
    /** Important Links */
    $wp_customize->add_setting( 'theme_info_theme',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $theme_info = '<p>';
    $theme_info .= sprintf( __( 'Demo Link: %1$sClick here.%2$s', 'yoga-fitness' ),  '<a href="' . esc_url( 'https://blossomthemes.com/theme-demo/?theme=yoga-fitness' ) . '" target="_blank">', '</a>' ); 
    $theme_info .= '</p><p>';
    $theme_info .= sprintf( __( 'Documentation Link: %1$sClick here.%2$s', 'yoga-fitness' ),  '<a href="' . esc_url( 'https://docs.blossomthemes.com/yoga-fitness/' ) . '" target="_blank">', '</a>' ); 
    $theme_info .= '</p>';

    $wp_customize->add_control( new Blossom_Spa_Note_Control( $wp_customize,
        'theme_info_theme', 
            array(
                'section'     => 'theme_info',
                'description' => $theme_info
            )
        )
    );
    
    /** Appearance Settings */
    $wp_customize->add_panel( 
        'appearance_settings',
         array(
            'priority'    => 25,
            'capability'  => 'edit_theme_options',
            'title'       => __( 'Appearance Settings', 'yoga-fitness' ),
            'description' => __( 'Customize Color, Typography & Background Image', 'yoga-fitness' ),
        ) 
    );

    /** Typography Settings */
    $wp_customize->add_section(
        'typography_settings',
        array(
            'title'    => __( 'Typography Settings', 'yoga-fitness' ),
            'priority' => 20,
            'panel'    => 'appearance_settings'
        )
    );
    
    /** Primary Font */
    $wp_customize->add_setting(
        'primary_font',
        array(
            'default'           => 'Open Sans',
            'sanitize_callback' => 'blossom_spa_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Spa_Select_Control(
            $wp_customize,
            'primary_font',
            array(
                'label'       => __( 'Primary Font', 'yoga-fitness' ),
                'description' => __( 'Primary font of the site.', 'yoga-fitness' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_spa_get_all_fonts(),   
            )
        )
    );
    
    /** Secondary Font */
    $wp_customize->add_setting(
        'secondary_font',
        array(
            'default'           => 'Playfair Display',
            'sanitize_callback' => 'blossom_spa_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Spa_Select_Control(
            $wp_customize,
            'secondary_font',
            array(
                'label'       => __( 'Secondary Font', 'yoga-fitness' ),
                'description' => __( 'Secondary font of the site.', 'yoga-fitness' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_spa_get_all_fonts(),   
            )
        )
    );  

    /** Font Size*/
    $wp_customize->add_setting( 
        'font_size', 
        array(
            'default'           => 17,
            'sanitize_callback' => 'blossom_spa_sanitize_number_absint'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Spa_Slider_Control( 
            $wp_customize,
            'font_size',
            array(
                'section'     => 'typography_settings',
                'label'       => __( 'Font Size', 'yoga-fitness' ),
                'description' => __( 'Change the font size of your site.', 'yoga-fitness' ),
                'choices'     => array(
                    'min'   => 10,
                    'max'   => 50,
                    'step'  => 1,
                )                 
            )
        )
    );

    /** Move Background Image section to appearance panel */
    $wp_customize->get_section( 'colors' )->panel              = 'appearance_settings';
    $wp_customize->get_section( 'colors' )->priority           = 10;
    $wp_customize->get_section( 'background_image' )->panel    = 'appearance_settings';
    $wp_customize->get_section( 'background_image' )->priority = 15;


    /** Header Layout */
    $wp_customize->add_section(
        'header_layout',
        array(
            'title'    => __( 'Header Layout', 'yoga-fitness' ),
            'panel'    => 'layout_settings',
            'priority' => 10,
        )
    );
    
    $wp_customize->add_setting( 
        'header_layout_option', 
        array(
            'default'           => 'two',
            'sanitize_callback' => 'blossom_spa_sanitize_radio'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Spa_Radio_Image_Control(
            $wp_customize,
            'header_layout_option',
            array(
                'section'     => 'header_layout',
                'label'       => __( 'Header Layout', 'yoga-fitness' ),
                'description' => __( 'This is the layout for header.', 'yoga-fitness' ),
                'choices'     => array(                 
                    'one'   => get_stylesheet_directory_uri() . '/images/header/one.jpg',
                    'two'   => get_stylesheet_directory_uri() . '/images/header/two.jpg',
                )
            )
        )
    );

    /** Shopping Cart */
    $wp_customize->add_setting( 
        'ed_shopping_cart', 
        array(
            'default'           => true,
            'sanitize_callback' => 'blossom_spa_sanitize_checkbox'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Spa_Toggle_Control( 
            $wp_customize,
            'ed_shopping_cart',
            array(
                'section'         => 'header_settings',
                'label'           => __( 'Shopping Cart', 'yoga-fitness' ),
                'description'     => __( 'Enable to show Shopping cart in the header.', 'yoga-fitness' ),
                'active_callback' => 'blossom_spa_is_woocommerce_activated'
            )
        )
    );
}
add_action( 'customize_register', 'yoga_fitness_customize_register', 99 );

/**
 * Header Start
*/
function blossom_spa_header(){ 
    
    $header_layout = get_theme_mod( 'header_layout_option', 'two' ); 
    
    if( $header_layout == 'one' ) { ?>
        <header id="masthead" class="site-header" itemscope itemtype="http://schema.org/WPHeader">
            <div class="container">
                <div class="header-main">
                    <?php blossom_spa_site_branding(); ?>
                    <?php blossom_spa_header_contact(); ?>
                </div><!-- .header-main -->
                <div class="nav-wrap">
                    <?php blossom_spa_primary_nagivation(); ?>
                    <?php if( blossom_spa_social_links( false ) || blossom_spa_header_search( false ) ) : ?>
                        <div class="nav-right">
                            <?php blossom_spa_social_links(); ?>
                            <?php blossom_spa_header_search(); ?>
                        </div><!-- .nav-right -->   
                    <?php endif; ?>
                </div><!-- .nav-wrap -->
            </div><!-- .container -->    
        </header>
    <?php }else{ 
        $ed_cart   = get_theme_mod( 'ed_shopping_cart', true ); ?>
        <header id="masthead" class="site-header header-two" itemscope itemtype="http://schema.org/WPHeader">
            <?php if( blossom_spa_header_contact( false ) || blossom_spa_social_links( false ) || ( blossom_spa_is_woocommerce_activated() && $ed_cart ) ) : ?>
                <div class="header-t">
                    <div class="container">
                        <?php blossom_spa_header_contact( true, false ); ?>
                        <?php blossom_spa_social_links(); ?>
                        <?php if( blossom_spa_is_woocommerce_activated() && $ed_cart ) yoga_fitness_wc_cart_count(); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="header-main">
                <div class="container">
                    <?php blossom_spa_site_branding(); ?>
                </div>
            </div>
            <div class="nav-wrap">
                <div class="container">
                    <?php blossom_spa_primary_nagivation(); ?>
                    <?php if( blossom_spa_header_search( false ) ) : ?>
                        <div class="nav-right">
                            <?php blossom_spa_header_search(); ?>
                        </div><!-- .nav-right -->   
                    <?php endif; ?>
                </div>
            </div><!-- .nav-wrap -->
        </header>
<?php }
}

/**
 * Woocommerce Cart Count
 * @link https://isabelcastillo.com/woocommerce-cart-icon-count-theme-header 
*/
function yoga_fitness_wc_cart_count(){
    $count = WC()->cart->cart_contents_count; ?>
    <div class="cart">                                      
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_html_e( 'View your shopping cart', 'yoga-fitness' ); ?>">
            <span><i class="fa fa-shopping-cart"></i></span>
            <span class="count"><?php echo esc_html( $count ); ?></span>
        </a>
    </div>    
    <?php
}

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 * 
 * @link https://isabelcastillo.com/woocommerce-cart-icon-count-theme-header
 */
function yoga_fitness_add_to_cart_fragment( $fragments ){
    ob_start();
    $count = WC()->cart->cart_contents_count; ?>
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart" title="<?php esc_attr_e( 'View your shopping cart', 'yoga-fitness' ); ?>">
        <i class="fas fa-shopping-cart"></i>
        <span class="number"><?php echo absint( $count ); ?></span>
    </a>
    <?php
 
    $fragments['a.cart'] = ob_get_clean();
     
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'yoga_fitness_add_to_cart_fragment' );

/**
 * Ajax Callback for adding product in cart
 * 
*/
function yoga_fitness_add_cart_ajax() {
    global $woocommerce;
    
    $product_id = $_POST['product_id'];

    WC()->cart->add_to_cart( $product_id, 1 );
    $count = WC()->cart->cart_contents_count;
    $cart_url = $woocommerce->cart->get_cart_url(); 
    
    ?>
    <a href="<?php echo esc_url( $cart_url ); ?>" rel="bookmark" class="btn-add-to-cart"><?php esc_html_e( 'View Cart', 'yoga-fitness' ); ?></a>
    <input type="hidden" id="<?php echo esc_attr( 'cart-' . $product_id ); ?>" value="<?php echo esc_attr( $count ); ?>" />
    <?php 
    die();
}
add_action( 'wp_ajax_yoga_fitness_add_cart_single', 'yoga_fitness_add_cart_ajax' );
add_action( 'wp_ajax_nopriv_yoga_fitness_add_cart_single', 'yoga_fitness_add_cart_ajax' );

/**
 * Returns Google Fonts Url
*/ 
function blossom_spa_fonts_url(){
    $fonts_url = '';    
    $primary_font       = get_theme_mod( 'primary_font', 'Open Sans' );
    $ig_primary_font    = blossom_spa_is_google_font( $primary_font );    
    $secondary_font     = get_theme_mod( 'secondary_font', 'Playfair Display' );
    $ig_secondary_font  = blossom_spa_is_google_font( $secondary_font );    
    $site_title_font    = get_theme_mod( 'site_title_font', array( 'font-family'=>'Marcellus', 'variant'=>'regular' ) );
    $ig_site_title_font = blossom_spa_is_google_font( $site_title_font['font-family'] );
            
    /* Translators: If there are characters in your language that are not
    * supported by respective fonts, translate this to 'off'. Do not translate
    * into your own language.
    */
    $primary    = _x( 'on', 'Primary Font: on or off', 'yoga-fitness' );
    $secondary  = _x( 'on', 'Secondary Font: on or off', 'yoga-fitness' );
    $site_title = _x( 'on', 'Site Title Font: on or off', 'yoga-fitness' );
    
    if ( 'off' !== $primary || 'off' !== $secondary || 'off' !== $site_title ) {
        
        $font_families = array();
     
        if ( 'off' !== $primary && $ig_primary_font ) {
            $primary_variant = blossom_spa_check_varient( $primary_font, 'regular', true );
            if( $primary_variant ){
                $primary_var = ':' . $primary_variant;
            }else{
                $primary_var = '';    
            }            
            $font_families[] = $primary_font . $primary_var;
        }
         
        if ( 'off' !== $secondary && $ig_secondary_font ) {
            $secondary_variant = blossom_spa_check_varient( $secondary_font, 'regular', true );
            if( $secondary_variant ){
                $secondary_var = ':' . $secondary_variant;    
            }else{
                $secondary_var = '';
            }
            $font_families[] = $secondary_font . $secondary_var;
        }
        
        if ( 'off' !== $site_title && $ig_site_title_font ) {
            
            if( ! empty( $site_title_font['variant'] ) ){
                $site_title_var = ':' . blossom_spa_check_varient( $site_title_font['font-family'], $site_title_font['variant'] );    
            }else{
                $site_title_var = '';
            }
            $font_families[] = $site_title_font['font-family'] . $site_title_var;
        }
        
        $font_families = array_diff( array_unique( $font_families ), array('') );
        
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),            
        );
        
        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
     
    return esc_url_raw( $fonts_url );
}

function yoga_fitness_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Open Sans' );
    $primary_fonts   = blossom_spa_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Playfair Display' );
    $secondary_fonts = blossom_spa_get_fonts( $secondary_font, 'regular' );
    $font_size       = get_theme_mod( 'font_size', 17 );

    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Marcellus', 'variant'=>'regular' ) );
    $site_title_fonts     = blossom_spa_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 30 );
         
    $custom_css = '';
    $custom_css .= '

    /*Typography*/

    body{
        font-family : ' . esc_html( $primary_fonts['font'] ) . ';
        font-size   : ' . absint( $font_size ) . 'px;        
    }

    .site-branding .site-title{
        font-size   : ' . absint( $site_title_font_size ) . 'px;
        font-family : ' . esc_html( $site_title_fonts['font'] ) . ';
        font-weight : ' . esc_html( $site_title_fonts['weight'] ) . ';
        font-style  : ' . esc_html( $site_title_fonts['style'] ) . ';
    }

    /*Fonts*/
    button,
    input,
    select,
    optgroup,
    textarea, 
    .post-navigation a .meta-nav, section.faq-text-section .widget_text .widget-title, 
    .search .page-header .page-title {
        font-family : ' . esc_html( $primary_fonts['font'] ) . ';
    }

    .section-title, section[class*="-section"] .widget_text .widget-title, 
    .page-header .page-title, .widget .widget-title, .comments-area .comments-title, 
    .comment-respond .comment-reply-title, .post-navigation .nav-previous a, .post-navigation .nav-next a, .site-banner .banner-caption .title, 
    .about-section .widget_blossomtheme_featured_page_widget .widget-title, .shop-popular .item h3, 
    .pricing-tbl-header .title, .recent-post-section .grid article .content-wrap .entry-title, 
    .gallery-img .text-holder .gal-title, .wc-product-section .wc-product-slider .item h3, 
    .contact-details-wrap .widget .widget-title, section.contact-section .contact-details-wrap .widget .widget-title, 
    .instagram-section .profile-link, .widget_recent_entries ul li, .widget_recent_entries ul li::before, 
    .widget_bttk_description_widget .name, .widget_bttk_icon_text_widget .widget-title, 
    .widget_blossomtheme_companion_cta_widget .blossomtheme-cta-container .widget-title, 
    .site-main article .content-wrap .entry-title, .search .site-content .search-form .search-field, 
    .additional-post .post-title, .additional-post article .entry-title, .author-section .author-content-wrap .author-name, 
    .widget_bttk_author_bio .title-holder, .widget_bttk_popular_post ul li .entry-header .entry-title, 
    .widget_bttk_pro_recent_post ul li .entry-header .entry-title, 
    .widget_bttk_posts_category_slider_widget .carousel-title .title, 
    .widget_blossomthemes_email_newsletter_widget .text-holder h3, 
    .portfolio-text-holder .portfolio-img-title, .portfolio-holder .entry-header .entry-title {
        font-family : ' . esc_html( $secondary_fonts['font'] ) . ';
    }';
    
    if( blossom_spa_is_woocommerce_activated() ) {
        $custom_css .='
        .woocommerce div.product .product_title, 
        .woocommerce div.product .woocommerce-tabs .panel h2 {
            font-family : ' . esc_html( $primary_fonts['font'] ) . ';
        }

        .woocommerce.widget_shopping_cart ul li a, 
        .woocommerce.widget .product_list_widget li .product-title, 
        .woocommerce-order-details .woocommerce-order-details__title, 
        .woocommerce-order-received .woocommerce-column__title, 
        .woocommerce-customer-details .woocommerce-column__title {
            font-family : ' . esc_html( $secondary_fonts['font'] ) . ';
        }';
    }
           
    wp_add_inline_style( 'blossom-spa', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'yoga_fitness_dynamic_css', 99 );

/**
 * Footer Bottom
*/
function blossom_spa_footer_bottom(){ ?>
    <div class="footer-b">
        <div class="container">
            <div class="copyright">           
            <?php
                blossom_spa_get_footer_copyright();

                esc_html_e( ' Yoga Fitness | Developed By ', 'yoga-fitness' );
                echo '<a href="' . esc_url( 'https://blossomthemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( ' Blossom Themes', 'yoga-fitness' ) . '</a>.';
                
                printf( esc_html__( ' Powered by %s', 'yoga-fitness' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'yoga-fitness' ) ) .'" target="_blank">WordPress</a>. ' );
                if ( function_exists( 'the_privacy_policy_link' ) ) {
                    the_privacy_policy_link();
                }
            ?>               
            </div>
            <?php blossom_spa_social_links( true, false ); ?>
            <button aria-label="<?php esc_attr_e( 'go to top', 'yoga-fitness' ); ?>" class="back-to-top">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </div>
    <?php
}