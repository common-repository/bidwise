<?php

function bidwise_development() {
  return $_SERVER['SERVER_NAME'] == 'localhost';
}

// get the bidwise domain in both dvelopment and production environments
// the parameter `$back` specifies if the domain will be used in the back-end
// by the container or the front-end. This is important, because the docker
// configuration in development needs a special domain `
function bidwise_domain($back = true) {
	$internal_domain = getenv('BIDWISE_INTERNAL_DOMAIN');
	$domain = getenv('BIDWISE_DOMAIN');
  if ($back) {
    // development environment
    return !empty($internal_domain) ? $internal_domain : 'http://www.bidwise.com';
  } else {
    return !empty($domain) ? $domain : '//www.bidwise.com';
  }
}

function bidwise_route_signup() {
  return bidwise_domain() . '/p/signup.json';
}

function bidwise_route_units() {
  return bidwise_domain() . '/p/meta.json';
}

// list of adunits
function bidwise_route_dashboard() {
  return 'http://www.bidwise.com' . '/publishers/dashboard';
}

// list of adunits
function bidwise_route_edit_units() {
  return 'http://www.bidwise.com' . '/publishers/units';
}

// url to edit an adunit
function bidwise_route_edit_unit($id) {
  return bidwise_route_edit_units(); # 'http://www.bidwise.com' . '/publishers/units/' . $id;
}


