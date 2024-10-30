<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.bidwise.com
 * @since      1.0.0
 *
 * @package    Bidwise
 * @subpackage Bidwise/public
 */


/* Renders an adunit */
function bidwise_adunit( $aid ) {
  ob_start();
  ?>
  <script async src="<?php echo bidwise_domain(false) ?>/scripts/units"></script>
  <div class="js-adsbybidwise"></div>
  <script>
    (adsbybidwise = window.adsbybidwise || []).push({aid: <?php echo $aid ?>});
  </script>
  <?php
  return ob_get_clean();
}



/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bidwise
 * @subpackage Bidwise/public
 * @author     Ariel Rodriguez Romero <ariel@bidwise.com>
 */
class Bidwise_Public {

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
   * @param      string    $plugin_name       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /* Add bidwise integration code for the HEAD section */
	public function head_code() {
    $signup_failed = get_option('bidwise_signup_failed', false);
    if (!$signup_failed) {
      $domain = bidwise_domain(false);
      ?>
        <script>
          (function(document, window, src){
            window['bidwiseOptions'] = {
              <?php if ($domain != '//www.bidwise.com') { ?>
              origin: '<?php echo $domain ?>',
              override: {
              	items_selector: 'a',
              }
              <?php } ?>
            };
            var a = document.createElement('script'), m = document.getElementsByTagName('script')[0];
            (localStorage && localStorage.getItem('$$pload') && ((window.stop && !window.stop()) || true)) || (a.async = 1);
            a.src = src; m.parentNode.insertBefore(a, m)
          })(document, window, '<?php echo $domain ?>/scripts/publisher');
        </script>
      <?php
    }
	}

	public function the_content($content) {
		$signup_failed = get_option('bidwise_signup_failed', false);

    if (!$signup_failed) {if( is_single() && get_post_type() == 'post') {
			$options = get_option('bidwise_options');
			// check if a post should be inserted before the content
			if (!empty($options['begin_post'])) {
				$content = bidwise_adunit($options['begin_post']) . $content;
			}
			if (!empty($options['end_post'])) {
				$content .= bidwise_adunit($options['end_post']);}
      }
			}

		return $content;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bidwise-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bidwise-public.js', array( 'jquery' ), $this->version, false );
	}

}
