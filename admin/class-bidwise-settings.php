<?php


class Bidwise_Settings {
  
  public function __construct($loader) {
    $this->loader = $loader;
    $this->define_hooks();
  }

  function define_hooks () {
    $this->loader->add_action('admin_init', $this, 'add_settings');
    $this->loader->add_action('admin_menu', $this, 'admin_menu');

  }

  public function add_settings() {
    register_setting('bidwise', 'bidwise_options');

    // First, we register a section. This is necessary since all future options must belong to one.
    add_settings_section(
        'bidwise_settings_section',         // ID used to identify this section and with which to register options
        __('Options for content sites (eg: blogs)', 'bidwise'),                  // Title to be displayed on the administration page
        array($this, 'sandbox_general_options_callback'), // Callback used to render the description of the section
        'bidwise'                           // Page on which to add this section of options
    );
    
    add_settings_field(
      'begin_post',
      '', // no header
      array($this, 'bidwise_select_insert'),
      'bidwise',
      'bidwise_settings_section',
      [
        'label_for' => 'begin_post',
        'class' => 'bidwise-row',
        'post_insert' => 'at the <b>' . __('beginning of every post', 'bidwise') . '</b>',
      ]
    );

    add_settings_field(
      'end_post',
      '', // no header
      array($this, 'bidwise_select_insert'),
      'bidwise',
      'bidwise_settings_section',
      [
        'label_for' => 'end_post',
        'class' => 'bidwise-row',
        'post_insert' => 'at the <b>' . __('end of every post', 'bidwise') . '</b> (Recommended)',
      ]
    );

    if ( class_exists( 'WooCommerce' ) ) {
      add_settings_section(
          'bidwise_woocommerce_settings_section',
          __('Options for WooCommerce Stores', 'bidwise'),
          array($this, 'woocommerce_settings_section'), 
          'bidwise'
      );
    }

    add_settings_field(
      'end_shop',
      '', // no header
      array($this, 'bidwise_select_insert'),
      'bidwise',
      'bidwise_woocommerce_settings_section',
      [
        'label_for' => 'end_shop',
        'class' => 'bidwise-row',
        'post_insert' => '<b>' . __('below product listings', 'bidwise') . '</b> (eg: search results)',
      ]
    );

    add_settings_field(
      'woocommerce_shop',
      '', // no header
      array($this, 'bidwise_woocommerce_field'),
      'bidwise',
      'bidwise_woocommerce_settings_section',
      [
        'label_for' => 'woocommerce_shop',
        'class' => 'bidwise-row',
      ]
    );
  }

  /* ------------------------------------------------------------------------ *
   * Section Callbacks
   * ------------------------------------------------------------------------ */
  
  /**
   * This function provides a simple description for the General Options page. 
   *
   * It is called from the 'sandbox_initialize_theme_options' function by being passed as a parameter
   * in the add_settings_section function.
   */
  function sandbox_general_options_callback() {
    // echo '<p>' . __('Select additional locations to insert bidwise ads.') .'</p>';
  }

  function woocommerce_settings_section() {
    echo '<p>' . __('Bidwise works slightly different for online stores. It enables shops to display ads in a way that minimizes sales cannibalization and customer distraction. Configure your WooCommerce settings below:') . '</p>';
  }

  /* ------------------------------------------------------------------------ *
   * Field Callbacks
   * ------------------------------------------------------------------------ */
  
  // field callbacks can accept an $args parameter, which is an array.
  // $args is defined at the add_settings_field() function.
  // wordpress has magic interaction with the following keys: label_for, class.
  // the "label_for" key value is used for the "for" attribute of the <label>.
  // the "class" key value is used for the "class" attribute of the <tr> containing the field.
  // you can add custom key value pairs to be used inside your callbacks.
  function bidwise_select_insert( $args ) {
    $units = get_option('bidwise_units');
    $options = get_option('bidwise_options');
    $aid = $options[$args['label_for']];

    // output the field
    ?>
    Insert
    <select name="bidwise_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
      <option value="" <?php if (!isset($aid)) {echo 'selected';} ?>>
      <?php echo __( 'Nothing', 'bidwise' ); ?>
      </option>

      <?php foreach($units as $key=>$value): ?>
        <option value="<?php echo $value->id ?>" <?php selected($aid, $value->id) ?>><?php echo $value->name ?></option>
      <?php endforeach; ?>
    </select>

    <?php echo $args['post_insert'] ?>
    <?php
  }

  function bidwise_woocommerce_field($args) {
    $options = get_option('bidwise_options');
    $selected = $options[$args['label_for']];
    ?>
    <fieldset>
      <label>
        <input type="radio" name="bidwise_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="1" <?php checked($selected, 1) ?>> 
        <span class="">Always show ads below product listings (Recommended)</span>
      </label>
      <br>
      <label>
        <input type="radio" name="bidwise_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="2" <?php checked($selected, 2) ?>> 
        <span>Show ads only when there are few or no relevant product results</span>
      </label>
<!--       <br>
      <label>
        <input type="radio" name="bidwise_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="3" <?php checked($selected, 3) ?>> 
        <span>Don't show any ads on product listings</span>
      </label> -->
    </fieldset>
    <?php
  }












  // adds a top level menu item to the dashboard, `admin_menu`
  public function admin_menu() {
    // selecting icon https://gelwp.com/articles/adding-a-base64-image-as-an-admin-menu-icon/
    $icon = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTUuOCAxOCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PHN0eWxlIHR5cGU9InRleHQvY3NzIj4uc3Qwe2ZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO2ZpbGw6Y3VycmVudENvbG9yO308L3N0eWxlPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNS43LDUuNGMwLDAuOS0wLjIsMS42LTAuNiwyLjVDMTQuNCw5LjQsMTQuNiwxOCw4LjYsMThDNy4yLDE4LDAsMTgsMCwxOFM0LjksNS45LDQuOSw1LjRjMC0yLjYsMi00LjksNC41LTUuM0M5LjgsMCwxMC4xLDAsMTAuNCwwYzAuMywwLDAuNywwLDEsMC4xYzIuMiwwLjQsMy45LDIuMSw0LjMsNC4zQzE1LjcsNC43LDE1LjgsNS4xLDE1LjcsNS40TDE1LjcsNS40eiBNOC42LDE2LjZjNC4zLDAsNC44LTYuOCw0LjgtNi44Yy0wLjksMC42LTEuOSwwLjktMywwLjljLTAuMywwLTAuNywwLTEtMC4xYzAsMC0wLjgsNC4yLTIuNiw1LjlDNy40LDE2LjYsOC4xLDE2LjYsOC42LDE2LjZ6IE01LjQsNy41bC0zLjYsOS4xYzAuMiwwLDAuOCwwLDEuOSwwYzMuNywwLDQuNS02LjMsNC41LTYuM0M2LjksOS44LDUuOSw4LjgsNS40LDcuNXogTTE0LjQsNC45YzAtMC42LTAuMi0xLjItMC42LTEuNmMwLDAsMCwwLDAsMHYwYy0wLjUtMC42LTEuMi0xLTItMWMtMC41LDAtMSwwLjItMS41LDAuNUM5LjksMi41LDkuNCwyLjQsOC45LDIuNGMtMS40LDAtMi42LDEuMi0yLjYsMi42YzAsMC4xLDAsMC40LDAsMC40YzAsMi4yLDEuOCw0LDQsNGMyLjIsMCw0LTEuOCw0LTRDMTQuNCw1LjMsMTQuNCw1LDE0LjQsNC45eiBNMTIsNS42Yy0wLjMsMS0xLjYsMS45LTEuNiwxLjlTOC45LDYuNiw4LjYsNS42QzguNCw1LjUsOC4yLDUuMyw4LjIsNC45YzAtMC40LDAuMy0wLjcsMC43LTAuN3MwLjcsMC4zLDAuNywwLjdjMCwwLjEsMCwwLjQsMCwwLjRjMCwwLjUsMC45LDEuMywwLjksMS4zczAuOC0wLjgsMC44LTEuM2MwLDAsMC0wLjMsMC0wLjRjMC0wLjQsMC4zLTAuNywwLjctMC43czAuNywwLjMsMC43LDAuN0MxMi41LDUuMywxMi4zLDUuNSwxMiw1LjZ6Ii8+PC9zdmc+";
    add_menu_page(
        'Bidwise Settings', // page title
        'Bidwise', // menu text
        'manage_options',
        'bidwise',
        array($this, 'bidwise_options_page'),
        $icon,
        99
    );
  }

  // options page html
  public function bidwise_options_page() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    if (get_option('bidwise_signup_failed', false)) {
      $this->render_signup_failed_settings();
    } else {
      $this->render_settings();
    }
  }

  private function render_settings() {
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
      // add settings saved message with the class of "updated"
      add_settings_error( 'bidwise', 'bidwise_message', __( 'Settings Saved', 'bidwise' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'bidwise' );

    // update the adunits list
    $data = bidwise_update_units();

    $units = get_option('bidwise_units');
    $email = isset($data->email) ? $data->email : get_option('admin_email');
    // echo var_dump($units);
    ?>
    <div class="wrap">
      <h1 class="bidwise-title"><?= esc_html(get_admin_page_title()); ?></h1>

      <?php if (isset($data->earnings) && $data->earnings) { ?>
        <div class="bidwise-block">
          <h3>Earnings Summary</h3>

          <table class="bidwise-earnings">
            <tr>
              <td>
                Yesterday<br>
                <span class="bidwise-revenue"><?php $this->echo_money($data->earnings->yesterday) ?></span>
              </td>
              <td>
                This month so far<br>
                <span class="bidwise-revenue"><?php $this->echo_money($data->earnings->current_month) ?></span>
              </td>
              <td>
                Last Month<br>
                <span class="bidwise-revenue"><?php $this->echo_money($data->earnings->last_month) ?></span>
              </td>
            </tr>
          </table>

          <p>You're registered on Bidwise with the email <b><?php echo $email ?></b>, to check your earnings and account details <a href="<?php echo bidwise_route_dashboard() ?>" target="_blank">login here</a>.</p>
        </div>
      <?php } ?>

      <div class="bidwise-block">
        <?php if (!(isset($data->earnings) && $data->earnings)) { ?>
          <p>You're registered on Bidwise with the email <b><?php echo $email ?></b>, to check your earnings and account details <a href="<?php echo bidwise_route_dashboard() ?>" target="_blank">login here</a>.</p>
        <?php } ?>

        <h3>Ad Units</h3>

        <?php if (count($units) == 1) { ?>
          <div>We have created a default ad unit for your site, to modify it or to create additional ad units go to <a href="<?php echo bidwise_route_edit_units() ?>" target="_blank">Ad Units</a> on your Bidwise dashboard.</div>
        <?php } else { ?>
          <div>You have several ad units configured, to create additional ones go to  <a href="<?php echo bidwise_route_edit_units() ?>" target="_blank">Ad Units</a> on your Bidwise dashboard.</div>
        <?php } ?>

        <ul class="subsubsub">
          <?php foreach($units as $key=>$value): ?>
            <li>
              <a href="<?php echo bidwise_route_edit_unit($value->id) ?>" target="_blank"><?php echo $value->name; ?></a>
              <?php if ($key + 1 < count($units)) {echo(' | ');} ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <br class="clear">
        <hr>

        <p>
          Don't forget to go to your <a href="/wp-admin/widgets.php">widgets</a> section (Appearance -> Widgets) and drop the bidwise widget wherever you want Bidwise ads to appear.
        </p>

        <form action="options.php" method="post">
          <input type="hidden" value="/wp-admin/options.php?page=bidwise" name="_wp_http_referer"> 
            <?php
            // output security fields for the registered setting "bidwise_options"
            settings_fields('bidwise');
            // output setting sections and their fields
            // (sections are registered for "bidwise", each field is registered to a specific section)
            do_settings_sections('bidwise');

            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
      </div>
    </div>
    <?php
  }

  private function render_signup_failed_settings () {
    $email = get_option('admin_email');
    ?>
    <div class="wrap">
      <h1 class="bidwise-title"><?= esc_html(get_admin_page_title()); ?></h1>

      <div class="bidwise-block">
        <p>
          There was a problem creating your account in bidwise, please review your email below and try again:
        </p>

        <form action="/wp-json/bidwise/signup" method="post">
          <table class="form-table">
            <tr>
              <th scope="row">
                <label for="email">Email Address </label>
              </th>
              <td>
                <input name="email" type="email" id="email" value="<?php echo $email ?>" class="regular-text">
                <p class="description" id="admin-email-description">This address will be used to create your account on Bidwise.com</p>
              </td>
            </tr>
          </table>

          <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Create bidwise account"></p>

          <p>Having problems signing up? <a href="mailto:publishers@bidwise.com">Contact Us!</a></p>
        </form>
      </div>
    </div>
    <?php
  }

  private function echo_money($cents) {
    echo '$' . $cents/100;
  }


}

