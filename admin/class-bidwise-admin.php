<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.bidwise.com
 * @since      1.0.0
 *
 * @package    Bidwise
 * @subpackage Bidwise/admin
 */


// Every time the settings page of the plugin is visited, the list of adunits is updated
// and stored in the `bidwise_units` option. This value is used later 
function bidwise_update_units() {
  $args = array(
    'body' => array(
      'token' => get_option('bidwise_token'),
      'url' => get_site_url(),
    ),
    'timeout' => '5',
    'redirection' => '5',
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => array(),
    'cookies' => array()
  );
  
  $request = wp_remote_get(bidwise_route_units(), $args);

  if(is_wp_error($request)) {
    return false; // Bail early
  }

  $body = wp_remote_retrieve_body($request);
  $data = json_decode($body);

  // update the adunits array in the options
  update_option('bidwise_units', $data->units);
  return $data;
}





/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bidwise
 * @subpackage Bidwise/admin
 * @author     Ariel Rodriguez Romero <ariel@bidwise.com>
 */
class Bidwise_Admin {

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($loader, $plugin_name, $version ) {
    $this->loader = $loader;
    $this->plugin_name = $plugin_name;
    $this->version = $version;

    $this->notifications = new Bidwise_Notifications($loader);
    $this->settings = new Bidwise_Settings($loader);

    $this->define_hooks();
  }


  function define_hooks() {
    $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
    $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );

    // when the admin is loaded, and the plugin activated, execute the signup call
    $this->loader->add_action('admin_init', $this, 'admin_init');

    $this->loader->add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path( dirname( __FILE__ ) ) . 'bidwise.php'), $this, 'plugin_add_settings_link');

    $this->loader->add_action('rest_api_init', $this, 'rest_api_init');
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bidwise-admin.css', array(), $this->version, 'all' );
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bidwise-admin.js', array( 'jquery' ), $this->version, false );
  }

  // Add settings link on installed plugin page
  // thanks to https://hugh.blog/2012/07/27/wordpress-add-plugin-settings-link-to-plugins-page/
  function plugin_add_settings_link( $links ) {
    $mylinks = array(
      '<a href="' . admin_url('admin.php?page='. $this->plugin_name) .'">' . __( 'Settings' ) . '</a>'
    );
    return array_merge($mylinks, $links);
  }

  public function admin_init() {
    // check if the plugin was just activated, and execute the signup in that case
    if(is_admin() && get_option('bidwise-plugin-activation') == 'just-activated') {
      delete_option('bidwise-plugin-activation');
      $this->signup_call(array(
        'email' => get_option('admin_email'),
      ));
    }
  }

  // This method will only be called once when the plugin is activated
  // It's triggers the process to register a new publisher on the bidwise system
  private function signup_call($options) {
    $name = get_userdata(get_current_user_id());
    $args = array(
      'body' => array(
        'url' => get_site_url(),
        'email' => $options['email'],
        'blogname' => get_option('blogname'),
        'language' => get_locale(),
        'name' => $name ? $name->display_name : null,
      ),
      'timeout' => '5',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => array(),
      'cookies' => array()
    );
    
    $request = wp_remote_post(bidwise_route_signup(), $args);
    // echo var_dump($request);

    if( is_wp_error( $request ) ) {
      $this->notifications->mark_signup_failed();
      return false; // Bail early
    }

    $body = wp_remote_retrieve_body( $request );
    $data = json_decode( $body );
    // echo var_dump($data);

    if (isset($data->success) && $data->success == false) {
      $this->notifications->mark_signup_failed($data->error);
      return false; // Bail early
    }

    update_option('bidwise_token', $data->token);
    update_option('bidwise_units', $data->units);

    // set default plugin settings after signup, if the options aren't defined
    if (!get_option('bidwise_options', false)) {
      update_option('bidwise_options', array(
        'begin_post' => null,
        'end_post' => get_option('bidwise_units')[0]->id,
        'woocommerce_shop' => 1,
        'end_shop' => get_option('bidwise_units')[0]->id,
      ));
    }
  }

  function rest_api_init () {
    register_rest_route( 'bidwise', '/signup', array(
      'methods' => 'POST',
      'callback' => array($this, 'rest_signup'),
    ) );
  }

  function rest_signup(WP_REST_Request $request) {
    update_option('bidwise_signup_failed', false);
    $email = $request['email'];
    $this->signup_call(array(
      'email' => $email,
    ));

    if ( wp_redirect( admin_url('admin.php?page=bidwise') ) ) {
      exit;
    }
  }

}

