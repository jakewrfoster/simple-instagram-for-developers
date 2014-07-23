<?php
/**
 * Plugin Name: Simple Instagram for Developers
 * Description: This plugin simply returns information from a single user's instagram feed using shortcode.
 * Version: 0.1.0
 * Author: Jake Foster
 * Author URI: http://createcollect.com/
 * Plugin URI: http://createcollect.com/
 * License: GPLv2 or later
 *
 */

if ( ! function_exists('fdinst_paths') ) {
	/*
	If using domain mapping or plugins that change the path dinamically, edit these to set the proper path and URL.
	*/
	function fdinst_paths() {
		if ( !defined('FDINST_URL') )
			define('FDINST_URL', plugin_dir_url(__FILE__));

		if ( !defined('FDINST_PATH') )
			define('FDINST_PATH', plugin_dir_path(__FILE__));
	}
	add_action( 'plugins_loaded', 'fdinst_paths', 50 );
}

if ( ! function_exists('fdinst_page') ) {
	function fdinst_page() {
		if ( !defined('FDINST_ADMIN_PAGE') )
			define('FDINST_ADMIN_PAGE', true);

		fdinst_paths();
		include_once( FDINST_PATH . 'fdinst_admin.php');
	}
}

if ( ! function_exists('fdinst_add_scripts') ) {
	function fdinst_add_scripts($page) {
		if ( 'settings_page_simple-instagram-developers' == $page ) {
			//wp_enqueue_script( 'fdinst-js', FDINST_URL . 'js/fdinst.js', array('jquery-ui-sortable'), '3.4.2', true );
			wp_enqueue_style( 'fdinst-css', FDINST_URL . 'css/fdinst-styles.css', array(), '0.1' );
		}
	}
}


if ( ! function_exists('fdinst_menu') ) {
	function fdinst_menu() {
		if ( function_exists('add_options_page') ) {
			add_options_page( 'Simple Instagram for Developers', 'Simple Instagram for Developers', 'manage_options', 'simple-instagram-developers', 'fdinst_page' );
			add_action( 'admin_enqueue_scripts', 'fdinst_add_scripts' );
		}
	}
	add_action( 'admin_menu', 'fdinst_menu' );
}


// fix SSL request error
add_action( 'http_request_args', 'no_ssl_http_request_args', 10, 2 );
function no_ssl_http_request_args( $args, $url ) {
	$args['sslverify'] = false;
	return $args;
}

if ( ! function_exists('fdinst_load_defaults') ) {
	function fdinst_load_defaults() {
		include_once('fdinst_defaults.php');
	}
	add_action( 'admin_init', 'fdinst_load_defaults' );
}

// register shortcode
add_shortcode( 'fd_simple_instagram', 'fd_instagram_embed_shortcode' );

// define shortcode
function fd_instagram_embed_shortcode( $atts, $content = null ) {
	// define main output
	if( get_option('fdinst_client_id') == '' ) {
		die('you need a client ID to use this plugin, fool');
	}

	$before_output = '<div class="loading insta-loader"></div> <div class="flexslider-fade flexslider-main"> <ul class="slides">';
	$after_output = '</ul></div>';
	$str    = "";

	// get remote data
	$result = wp_remote_get( 'https://api.instagram.com/v1/users/'. get_option("fdinst_user") .'/media/recent/?client_id='. get_option("fdinst_client_id") .'&count='.get_option("fdinst_count") );

	if ( is_wp_error( $result ) ) {
		// error handling
		$error_message = $result->get_error_message();
		$str           = "Something went wrong: $error_message";
	} else {
		// processing further

		$result    = json_decode( $result['body'] );
		$main_data = array();
		$n         = 0;

		if(!empty($result->data)) {
			// get username and actual thumbnail
			foreach ( $result->data as $d ) {
				$main_data[ $n ]['user']         = $d->user->username;
				$main_data[ $n ]['thumbnail']    = $d->images->standard_resolution->url;
				$main_data[ $n ]['caption']      = $d->caption->text;
				$main_data[ $n ]['likes']        = $d->likes->count;
				$main_data[ $n ]['link']         = $d->link;
				$main_data[ $n ]['comments']     = $d->comments->count;
				$main_data[ $n ]['date']         = $d->created_time;
				$n++;
			}

			// create main string, pictures embedded in links
			foreach ( $main_data as $data ) {
				$pattern = '/[\x{1F600}-\x{1F64F}]/u';
				$insta_title = remove_emoji( $data['caption'] );
				$the_time = '';
				$insta_date = cc_elapsed_time( $data['date'], false );
				$str .= '<li>
							<a class="insta-image" target="_blank" href="'.$data['link'].'">
								<img src="'.$data['thumbnail'].'" alt="'.$data['user'].' | Discover Creators. Become a Collector.">
							</a>
							<div class="insta-info">
							<div class="center">
								<h3 class="insta-name"><a target="_blank" href="http://instagram.com/createcollect">@createcollect</a></h3>
								<h4 class="insta-caption">'. $insta_title .'</h4>
								<h4 class="insta-date">'. $insta_date .'</h4>
							</div>

							</div>
						</li>' ;
			}

		$output = $before_output . $str . $after_output;

		return $output;

		} else {
			die('Something went wrong, check your settings... more advanced debugging coming soon');
		}
	}
}
?>