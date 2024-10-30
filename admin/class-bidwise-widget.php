<?php

// Creating the widget 
class Bidwise_Widget extends WP_Widget {

    public function __construct() {
        $widget_options = array( 
            'classname' => 'bidwise_widget',
            'description' => 'This widget contains a Bidwise Ad Unit.',
        );
        parent::__construct( 'bidwise_widget', 'Bidwise Ad Unit', $widget_options );
    }

    public function widget( $args, $instance ) {
      echo $args['before_widget'];
      if (isset($instance['aid'])) {
        $aid = $instance['aid'];
      } else {
        // if no aid was specified take the first one
        $aid = get_option('bidwise_units')[0]->id;
      }
      ?>

      <script async src="<?php echo bidwise_domain(false) ?>/scripts/units"></script>
      <div class="js-adsbybidwise"></div>
      <script>
        (adsbybidwise = window.adsbybidwise || []).push({aid: <?php echo $aid ?>});
      </script>

      <?php
      echo $args['after_widget'];
    }

    public function form( $instance ) {
      $aid = ! empty( $instance['aid'] ) ? $instance['aid'] : '';
      $units = get_option('bidwise_units');
      ?>
      <p>
        <label for="<?php echo $this->get_field_id( 'aid' ); ?>">Ad Unit:</label>
        <select id="<?php echo $this->get_field_id( 'aid' ); ?>" name="<?php echo $this->get_field_name( 'aid' ); ?>">
          <?php foreach($units as $key=>$value): ?>
            <option value="<?php echo $value->id ?>" <?php if ($aid == $value->id) {echo 'selected';} ?>><?php echo $value->name ?></option>
          <?php endforeach; ?>
        </select>
      </p>
      <p>You can add more adunits on <a href="http://www.bidwise.com/publishers/units" target="_blank">Bidwise.com</a></p>
      <?php 
    }

}


class Bidwise_Widget_Loader {
    public function load() {
        register_widget('Bidwise_Widget');
    }
}


