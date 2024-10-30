<?php



class Bidwise_Notifications {

  // initialize the notifications
  public function __construct($loader) {
    $this->loader = $loader;

    $this->define_hooks();
  }

  function define_hooks() {
    $this->loader->add_action('admin_notices', $this, 'notifications');
  }

  // responsible for rendering the notifications
  public function notifications() {
    if (get_option('bidwise_signup_failed_notification', false)) {
      $error = get_option('bidwise_signup_failed_notification');

      // show the notification only once
      update_option('bidwise_signup_failed_notification', false);
      $screen = get_current_screen();
      if ( $screen->id != 'toplevel_page_bidwise' ) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
              <?php _e('There was an error activating Bidwise, please go to the ', 'bidwise'); ?>
              <a href="<?php echo admin_url('admin.php?page=bidwise') ?>"><?php _e('settings page', 'bidwise'); ?></a>
              <?php _e('to customize options', 'bidwise'); ?>
            </p>

            <?php if (is_string($error)) {?>
              <p>> <?php echo $error; ?></p>
            <?php } ?>
        </div>
        <?php
      } else {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
              <?php _e('There was an error activating Bidwise, please try again later.', 'bidwise'); ?>
            </p>

            <?php if (is_string($error)) {?>
              <p>> <?php echo $error; ?></p>
            <?php } ?>
        </div>
        <?php
      }
    }
  }

  // schedule a signup failed notification
  function mark_signup_failed($error = true) {
    // this option contains the $error from the server that should be displayed
    update_option('bidwise_signup_failed_notification', $error);

    // this option will be true until the user completes the signup, the option `bidwise_signup_failed_notification`
    // is true only when a notification should be displayed.
    update_option('bidwise_signup_failed', true);
  }

}






