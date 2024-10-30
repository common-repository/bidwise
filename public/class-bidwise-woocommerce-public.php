<?php

class Bidwise_Woocommerce_Public {

  public function __construct( ) {
    $this->product_count = 0;
  }

  public function before_shop() {

  }

  // this function is executed once, for every product
  // here we count the number of products on a page
  public function shop_loop() {
    $this->product_count += 1;
  }

  public function after_shop() {
    $total_products = apply_filters('loop_shop_per_page', 12);
    $options = get_option('bidwise_options');
    
    if (!empty($options['woocommerce_shop']) && !empty($options['end_shop'])) {
      if ($options['woocommerce_shop'] == 1 ||
        ($options['woocommerce_shop'] == 2 && $this->product_count <= $total_products/2)) {
        echo bidwise_adunit($options['end_shop']);
      }
    }
  }

  public function no_products() {
    $options = get_option('bidwise_options');
    if (!empty($options['woocommerce_shop']) && !empty($options['end_shop'])) {
      if ($options['woocommerce_shop'] == 1 || $options['woocommerce_shop'] == 2) {
        echo bidwise_adunit($options['end_shop']);
      }
    }
  }
}


