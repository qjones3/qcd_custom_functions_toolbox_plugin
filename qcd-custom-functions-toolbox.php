<?php
/**
 * Plugin Name:   QCDesígn Custom Functions Plugin
 * Plugin URI:    http://qcdesign.com/web-development/plugins/qc-custom-functions/
 * Description:   A library of QCDesígn's most common functionality enhancements
 * Version:       1.1.0
 * Author:        Quinton Jones
 * Author URI:    http://qcdesign.com/
 * License:       MIT License
 * License URI:   http://opensource.org/licenses/MIT
 * Text Domain:   qcdesign-custom-functions-plugin
 * Domain Path:   /languages
 *
 *
 * This WordPress plugin includes common security and admin customizations
 * to enhance the uer experience.
 *
 *
 */

//* Enqueue Custom Stylesheet
add_action('wp_enqueue_scripts', 'custom_style_sheet', 11);
function custom_style_sheet() {
  wp_enqueue_style( 'custom-styling', get_stylesheet_directory_uri() . '/custom.css' );
}

//* Load Custom Login Styles
add_action('login_head', 'custom_login_css');
function custom_login_css() {
  echo '<link href="'.get_stylesheet_directory_uri().'/login-styles.css" rel="stylesheet">';
}

//* Customize Login Logo link
add_filter( 'login_headerurl', 'my_login_logo_url' );
function my_login_logo_url() {
  return get_bloginfo( 'url' );
}


//* Customize Site Name and Info
add_filter( 'login_headertitle', 'my_login_logo_url_title' );
function my_login_logo_url_title() {
  return 'Your Site Name and Info';
}


//* Hide the Login Error Message
add_filter('login_errors',create_function('$a', "return null;"));


//* Change the Redirect URL to redirect users to the homepage
add_filter("login_redirect", "admin_login_redirect", 10, 3);
function admin_login_redirect( $redirect_to, $request, $user ) {
  global $user;
  if( isset( $user->roles ) && is_array( $user->roles ) ) {
    if( in_array( "administrator", $user->roles ) ) {
      return $redirect_to;
    } else {
      return home_url();
    }
    } else {
      return $redirect_to;
    }
}


//* Set "Remember Me" To Checked
add_action( 'init', 'login_checked_remember_me' );
function login_checked_remember_me() {
  add_filter( 'login_footer', 'rememberme_checked' );
}

function rememberme_checked() {
  echo '<script>document.getElementById("rememberme").checked = true;</script>';
}


//* Change Standard WordPress Admin Greeting
add_action( 'admin_bar_menu', 'wp_admin_bar_my_custom_account_menu', 11 );
function wp_admin_bar_my_custom_account_menu( $wp_admin_bar ) {
  $user_id = get_current_user_id();
  $current_user = wp_get_current_user();
  $profile_url = get_edit_profile_url( $user_id );
  if ( 0 != $user_id ) {
    //* Add the "My Account" menu
    $avatar = get_avatar( $user_id, 28 );
    $howdy = sprintf( __('Welcome back, %1$s'), $current_user->display_name );
    $class = empty( $avatar ) ? '' : 'with-avatar';
    $wp_admin_bar->add_menu( array( 'id' => 'my-account', 'parent' => 'top-secondary', 'title' => $howdy . $avatar, 'href' => $profile_url, 'meta' =>
    array( 'class' => $class, ), ) );
  }
}


//* Modify the Genesis content limit read more link
//* "Display post content" and character limit must be set in the Content Archives section of the Theme Settings page
add_filter( 'get_the_content_more_link', 'sp_read_more_link' );
function sp_read_more_link() {
  return '... <a class="more-link" href="' . get_permalink() . '" title="Read the entire article">Continue Reading &raquo;</a>';
}


//* Modify breadcrumb arguments.
add_filter( 'genesis_breadcrumb_args', 'sp_breadcrumb_args' );
function sp_breadcrumb_args( $args ) {
  $args['home'] = 'Home';
  $args['sep'] = ' &laquo; ';
  $args['list_sep'] = ', '; // Genesis 1.5 and later
  $args['prefix'] = '<div class="breadcrumb">';
  $args['suffix'] = '</div>';
  $args['heirarchial_attachments'] = true; // Genesis 1.5 and later
  $args['heirarchial_categories'] = true; // Genesis 1.5 and later
  $args['display'] = true;
  $args['labels']['prefix'] = '';
  $args['labels']['author'] = 'Archive for ';
  $args['labels']['category'] = 'Archive for '; // Genesis 1.6 and later
  $args['labels']['tag'] = 'Archive for ';
  $args['labels']['date'] = 'Archive for ';
  $args['labels']['search'] = 'Search for ';
  $args['labels']['tax'] = 'Archive for ';
  $args['labels']['post_type'] = 'Archive for ';
  $args['labels']['404'] = 'Not found: '; // Genesis 1.5 and later
  return $args;
}


//* Add Post Navigation - Previous and Next
add_action( 'genesis_entry_footer', 'genesis_prev_next_post_nav');


//* Customize the previous page link
add_filter ( 'genesis_prev_link_text' , 'sp_previous_page_link' );
function sp_previous_page_link ( $text ) {
    return '<span>&laquo;</span> Prev';
}

//* Customize the next page link
add_filter ( 'genesis_next_link_text' , 'sp_next_page_link' );
function sp_next_page_link ( $text ) {
    return 'Next <span>&raquo;</span>';
}

//* Force cookies to expire with the session for password protected pages
add_action( 'wp', 'post_pw_sess_expire' );
    function post_pw_sess_expire() {
    if ( isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
    // Setting a time of 0 in setcookie() forces the cookie to expire with the session
    setcookie('wp-postpass_' . COOKIEHASH, '', 0, COOKIEPATH);
}