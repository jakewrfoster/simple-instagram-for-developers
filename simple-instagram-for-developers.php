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


if ( ! function_exists('fdinst_load_defaults') ) {
	function fdinst_load_defaults() {
		$fdinst_options = get_option('fdinst_options');
		if ( ! empty($fdinst_options) )
			return;

		@include_once('fdinst_defaults.php');

		if ( isset($fdinst_toolbars) ) {
			add_option( 'fdinst_options', $fdinst_options );
			add_option( 'fdinst_toolbars', $fdinst_toolbars, '', 'no' );
			add_option( 'fdinst_plugins', $fdinst_plugins, '', 'no' );
			add_option( 'fdinst_btns1', $fdinst_btns1, '', 'no' );
			add_option( 'fdinst_btns2', $fdinst_btns2, '', 'no' );
			add_option( 'fdinst_btns3', $fdinst_btns3, '', 'no' );
			add_option( 'fdinst_btns4', $fdinst_btns4, '', 'no' );
			add_option( 'fdinst_allbtns', $fdinst_allbtns, '', 'no' );
		}
	}
	add_action( 'admin_init', 'fdinst_load_defaults' );
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

/*
class CC_Instgram {

	public function __construct() {
		add_shortcode( 'ccinstagram', array($this, 'cc_instagram_feed') );
	}

	public function cc_instagram_feed() {

	}
}

class CC_Instagram_API {

	protected $apiurl = 'https://api.instagram.com/v1/tags/coffee/media/recent?access_token=';
	protected $user = '367274847'; // create collect user id
	protected $client_id = '077beab5bdd2442a92d723d888cbe337'; // instagram api client id
	protected $handshake = 0a0f8d7bb5044c9c90e95af272eab928;
	protected $handshake = d0da1605725c40c2b3f32176eee1d520; // NEWEST
	protected $access_token = '367274847.077beab.c1f43b76f8924dc6a597da4233883feb';


	public function __contruct( $user ) {
		$this->user = $user;
	}

	public function CcGetFeed() {

	}

}

$cc_instagram = new CC_Instgram();
*/






// fix SSL request error
add_action( 'http_request_args', 'no_ssl_http_request_args', 10, 2 );
function no_ssl_http_request_args( $args, $url ) {
	$args['sslverify'] = false;
	return $args;
}

// register shortcode
add_shortcode( 'fd_simple_instagram', 'fd_instagram_embed_shortcode' );

// define shortcode
function fd_instagram_embed_shortcode( $atts, $content = null ) {
	// define main output

	$before_output = '<div class="loading insta-loader"></div> <div class="flexslider-fade flexslider-main"> <ul class="slides">';
	$after_output = '</ul></div>';
	$str    = "";

	// get remote data
	$result = wp_remote_get( 'https://api.instagram.com/v1/users/'. $fdinst_options["user"] .'/media/recent/?client_id='. $fdinst_options["client_id"] .'&count='.$fdinst_options["count"] );

	if ( is_wp_error( $result ) ) {
		// error handling
		$error_message = $result->get_error_message();
		$str           = "Something went wrong: $error_message";
	} else {
		// processing further
		$result    = json_decode( $result['body'] );
		$main_data = array();
		$n         = 0;

		// get username and actual thumbnail
		foreach ( $result->data as $d ) {
			$main_data[ $n ]['user']         = $d->user->username;
			$main_data[ $n ]['thumbnail']    = $d->images->standard_resolution->url;
			$main_data[ $n ]['caption']      = $d->caption->text;
			$main_data[ $n ]['likes']        = $d->likes->count;
			$main_data[ $n ]['link']         = $d->link;
			$main_data[ $n ]['comments']     = $d->comments->count;
			$main_data[ $n ]['date'] = $d->created_time;
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
	}

	$output = $before_output . $str . $after_output;

	return $output;
}
?>