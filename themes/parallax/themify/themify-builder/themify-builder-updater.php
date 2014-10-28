<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themify_Builder_Updater {

	var $name;
	var $version;
	var $versions_url;
	var $package_url;

	public function __construct( $name, $version, $slug ) {
		$this->name = $name;
		$this->version = $version;
		$this->slug = $slug;
		$this->versions_url = 'http://themify.me/versions/versions.xml';
		$this->package_url = "http://themify.me/files/{$this->name}/{$this->name}.zip";

		if( isset( $_GET['page'] ) && ! isset( $_GET['action'] ) && ( $_GET['page'] == 'themify-builder' || $_GET['page'] == 'themify' ) ) {
			add_action( 'admin_notices', array( $this, 'check_version' ), 3 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		} elseif( isset( $_GET['page'] ) && isset( $_GET['action'] ) && ( $_GET['page'] == 'themify-builder' || $_GET['page'] == 'themify' ) ) {
			add_action( 'admin_notices', 'themify_builder_updater', 3 );
		}

		if( defined('WP_DEBUG') && WP_DEBUG ) {
			delete_transient( "{$this->name}_new_update" );
			delete_transient( "{$this->name}_check_update" );
		}
	}

	public function check_version() {
		$notifications = '<style type="text/css">.notifications p.update {background: #F9F2C6;border: 1px solid #F2DE5B;} .notifications p{width: 765px;margin: 15px 0 0 5px;padding: 10px;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;}</style>';

		// Check update transient
		$current = get_transient( "{$this->name}_check_update" ); // get last check transient
		$timeout = 60;
		$time_not_changed = isset( $current->lastChecked ) && $timeout > ( time() - $current->lastChecked );
		$newUpdate = get_transient( "{$this->name}_new_update" ); // get new update transient

		if ( is_object( $newUpdate ) && $time_not_changed ) {
			if ( version_compare( $this->version, $newUpdate->version, '<') ) {
				$notifications .= sprintf( __('<p class="update %s">%s version %s is now available. <a href="%s" title="" class="%s" target="%s" data-plugin="%s" data-package_url="%s">Update now</a> or view the <a href="http://themify.me/logs/%s-changelogs" title="" class="" target="_blank">change log</a> for details.</p>', 'themify'),
					$newUpdate->login,
					ucwords( $this->name ),
					$newUpdate->version,
					$newUpdate->url,
					$newUpdate->class,
					$newUpdate->target,
					$this->slug,
					esc_attr( $this->package_url ),
					$this->name
				);
				echo '<div class="notifications">'. $notifications . '</div>';
			}
			return;
		}

		// get remote version
		$remote_version = $this->get_remote_version();

		// delete update checker transient
		delete_transient( "{$this->name}_check_update" );

		$class = "";
		$target = "";
		$url = "#";
		
		$new = new stdClass();
		$new->login = 'login';
		$new->version = $remote_version;
		$new->url = $url;
		$new->class = 'themify-builder-upgrade-plugin';
		$new->target = $target;

		if ( version_compare( $this->version, $remote_version, '<' ) ) {
			set_transient( 'themify_builder_new_update', $new );
			$notifications .= sprintf( __('<p class="update %s">%s version %s is now available. <a href="%s" title="" class="%s" target="%s" data-plugin="%s" data-package_url="%s">Update now</a> or view the <a href="http://themify.me/logs/%s-changelogs" title="" class="" target="_blank">change log</a> for details.</p>', 'themify'),
				$new->login,
				ucwords( $this->name ),
				$new->version,
				$new->url,
				$new->class,
				$new->target,
				$this->slug,
				esc_attr( $this->package_url ),
				$this->name
			);
		}

		// update transient
		$this->set_update();

		echo '<div class="notifications">'. $notifications . '</div>';
	}

	public function get_remote_version() {
		$xml = new DOMDocument;
		$response = wp_remote_get( $this->versions_url );
		if( is_wp_error( $response ) ) 
			return;

		$body = trim( wp_remote_retrieve_body( $response ) );
		$xml->loadXML($body);
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput = true;
		$xpath = new DOMXPath($xml);
		$query = "//version[@name='".$this->name."']";
		$version = '';

		$elements = $xpath->query($query);

		if( $elements->length ) {
			foreach ($elements as $field) {
				$version = $field->nodeValue;
			}
		}

		return $version;
	}

	public function set_update() {
		$current = new stdClass();
		$current->lastChecked = time();
		set_transient( "{$this->name}_check_update", $current );
	}

	public function is_update_available() {
		$newUpdate = get_transient( "{$this->name}_new_update" ); // get new update transient

		if ( false === $newUpdate ) {
			$new_version = $this->get_remote_version( $this->name );
		} else {
			$new_version = $newUpdate->version;
		}

		if ( version_compare( $this->version, $new_version, '<') ) {
			return true;
		} else {
			false;
		}
	}

	public function enqueue() {
		wp_enqueue_script( 'themify-builder-plugin-upgrade', THEMIFY_BUILDER_URI . '/js/themify.builder.upgrader.js', array('jquery'), false, true );
	}
}

/**
 * Updater called through wp_ajax_ action
 */
function themify_builder_updater(){
	
	$url = isset( $_POST['package_url'] ) ? $_POST['package_url'] : null;
	$plugin_slug = isset( $_POST['plugin'] ) ? $_POST['plugin'] : null;

	if( ! $url || ! $plugin_slug ) return;

	//If login is required
	if($_GET['login'] == 'true'){

			if(isset($_POST['password'])){
	            $cred = $_POST;
	            $filesystem = WP_Filesystem($cred);
	        }
			else{
				$filesystem = WP_Filesystem();
			}

			$response = wp_remote_post(
				'http://themify.me/member/login.php',
				array(
					'timeout' => 300,
					'headers' => array(
						
					),
					'body' => array(
						'amember_login' => $_POST['username'],
						'amember_pass'  => $_POST['password']
					)
			    )
			);

			//Was there some error connecting to the server?
			if( is_wp_error( $response ) ) {
				$errorCode = $response->get_error_code();
				echo 'Error: ' . $errorCode;
				die();
			}

			//Connection to server was successful. Test login cookie
			$amember_nr = false;
			foreach($response['cookies'] as $cookie){
				if($cookie->name == 'amember_nr'){
					$amember_nr = true;
				}
			}
			if(!$amember_nr){
				_e('You are not a Themify Member.', 'themify');
				die();
			}
	}

	//remote request is executed after all args have been set
	include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once(THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-upgrader.php');
	$title = __('Update Themify Builder plugin', 'themify');
	$nonce = 'upgrade-themify_plugin';

	$upgrader = new Themify_Builder_Upgrader( new Plugin_Upgrader_Skin(
		array(
			'plugin' => $plugin_slug,
			'title' => __('Update Builder')
		)
	));
	$upgrader->upgrade( $plugin_slug, $url, $response['cookies'] );

	//if we got this far, everything went ok!	
	die();
}

/**
 * Validate login credentials against Themify's membership system
 */
function themify_builder_validate_login(){
	$response = wp_remote_post(
		'http://themify.me/member/login.php',
		array(
			'timeout' => 300,
			'headers' => array(
				
			),
			'body' => array(
				'amember_login' => $_POST['username'],
				'amember_pass'  => $_POST['password']
			)
	    )
	);

	//Was there some error connecting to the server?
	if( is_wp_error( $response ) ) {
		$errorCode = $response->get_error_code();
		echo 'Error: ' . $errorCode;
		die();
	}

	//Connection to server was successful. Test login cookie
	$amember_nr = false;
	foreach($response['cookies'] as $cookie){
		if($cookie->name == 'amember_nr'){
			$amember_nr = true;
		}
	}
	if(!$amember_nr){
		echo 'false';
		die();
	}

	echo 'true';
	die();
}

//Executes themify_updater function using wp_ajax_ action hook
add_action('wp_ajax_themify_builder_validate_login', 'themify_builder_validate_login');

add_filter( 'update_plugin_complete_actions', 'themify_builder_upgrade_complete', 10, 2 );
function themify_builder_upgrade_complete($update_actions, $plugin) {
	if ( $plugin == THEMIFY_BUILDER_SLUG ) {
		$update_actions['themify_complete'] = '<a href="' . self_admin_url('admin.php?page=themify-builder') . '" title="' . __('Return to Builder Settings', 'themify') . '" target="_parent">' . __('Return to Builder Settings', 'themify') . '</a>';
	}
	return $update_actions;
}
?>