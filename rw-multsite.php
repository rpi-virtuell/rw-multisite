<?php
/**
 * Plugin Name:      RW Multisite
 * Plugin URI:       https://github.com/rpi-virtuell/rw_multisite
 * Description:      Tools for Wordpress Multisite
 * Author:           Joachim Happel
 * Version:          0.0.6
 * Licence:          GPLv3
 * Author URI:       http://joachim-happel.de
 * Text Domain:      rw_remote_auth_client
 * Domain Path:      /languages
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rw_multisite
 * GitHub Branch:     master
 */


class RW_MultisiteTools{
	/**
	 * Liefert einen Array mit allen Blogs in einem Netzwerk zurück
	 *
	 * custom css recomended:
         *
	 * ul.rw-multisite-list {
	 *    margin-left:15px;
	 * }
	 * ul.rw-multisite-list li a{
	 *    margin: 0 5px 0 10px;
	 * }
	 *
	 * @returns array
	 */
	static function get_my_blogs() {
		$html = '';

		if(is_user_logged_in() && is_multisite()){
			$subsites=get_blogs_of_user(get_current_user_id( ), false);

			$capability = 'author';

			if( count ($subsites) > 0 ){
				$html= '<ul class="rw-multisite-list">';

				foreach( $subsites as $subsite ) {

					$subsite_id = get_object_vars($subsite)["userblog_id"];
					$subsite_domain = get_object_vars($subsite)["domain"];
					$subsite_path = get_object_vars($subsite)["path"];
					$subsite_name = get_object_vars($subsite)["blogname"];
					$subsite_url = get_object_vars($subsite)["siteurl"];
					if($subsite_name  == '') $subsite_name = $subsite_domain. $subsite_path;
					//$subsite_description = get_blog_option($subsite_id,'blogdescription');
					if( $subsite->archived == 0 && $subsite->mature == 0 && $subsite->spam == 0 && $subsite->deleted == 0 ){
						$html .= '<li><a href="' . $subsite_url.'">' . $subsite_name . '</a>';
					}
					if(current_user_can_for_blog($subsite_id, $capability)){
						$html .= ' <a href="' . $subsite_url.'/wp-admin/" title="Administration" style="box-shadow: none !important; text-decoration:none" class="rw-multisite-edit-button"><svg style="width:18px; height:18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></a>';
					}
					$html .= '</li>';
				}
				$html .= '</ul>';
			}else{
				$html .= 'Du hast noch keine Seiten auf diesem Server.';
			}


		}
		return $html;
	}
	/**
	 * gibt alle Blogs einer Multisite aus
	 */
	static function get_all_blogs() {
		$html= '<ul>';
		$subsites = get_sites();
		foreach( $subsites as $subsite ) {
			$subsite_id = get_object_vars($subsite)["blog_id"];
			$subsite_domain = get_object_vars($subsite)["domain"];
			$subsite_path = get_object_vars($subsite)["path"];
			$subsite_name = get_blog_details($subsite_id)->blogname;
			if($subsite_name  == '') $subsite_name = $subsite_domain. $subsite_path;
			$subsite_description = get_blog_option($subsite_id,'blogdescription');
			if ($subsite_id > 1)
				$html .= '<li><a href="http://' . $subsite_domain . $subsite_path .'">' . $subsite_name . '</a><br>'.$subsite_description.'</li>';
		}
		$html .= '</ul>';
		return $html;
	}



	static function custom_dashboard_widgets() {
		global $wp_meta_boxes;
		//wp_add_dashboard_widget('custom_help_widget', '<b  style="color:red;">Supportmeldung</b>',  function(){echo 'Inhalt';});
	}

	// fix Learningapp provider output
	function oembed_learningapps_provider_result($html, $url, $args) {

		//var_dump($html, $url, $args);

		$html = str_replace('http://LearningApps.org', 'https://LearningApps.org' , $html);

		return $html;

	}

	static function register_providers() {


		$provider = plugin_dir_url(__FILE__ ).'oembed.php';

		wp_oembed_add_provider('#https://.*/wp-admin/admin-ajax\.php\?action=h5p_embed.*#', $provider, true);
		wp_oembed_add_provider('https://religionsunterricht.net/ru/*', $provider, false);

	}



	static function allow_unfiltered_html( $caps, $cap, $user_id ) {
		if ( 'unfiltered_html' === $cap && (user_can( $user_id, 'editor' ) || user_can( $user_id, 'administrator' ) ) ) {
			$caps = array( 'unfiltered_html' );
		}

		return $caps;
	}


	static function init(){

		add_action('init', array('RW_MultisiteTools', 'register_providers'));
		add_action('init', array('RW_MultisiteTools', 'allow_more_tags'));
		add_filter('tiny_mce_before_init', array('RW_MultisiteTools', 'allow_iframes_for_tinyMCE'));
		add_filter('wp_kses_allowed_html', array('RW_MultisiteTools', 'allow_more_tags_in_post'), 10,2);


		add_shortcode('rw_multisite_list_sites',function(){
			return RW_MultisiteTools::get_all_blogs();
		});
		add_shortcode('rw_multisite_list_my_sites',function(){
			return RW_MultisiteTools::get_my_blogs();
		});

		add_action('wp_dashboard_setup', array('RW_MultisiteTools', 'custom_dashboard_widgets'));

		add_shortcode('etool', array('RW_MultisiteTools', 'etool_generator'));

		add_filter( 'embed_oembed_html',  array('RW_MultisiteTools', 'oembed_learningapps_provider_result') , 10,3 );

		add_filter( 'map_meta_cap', array('RW_MultisiteTools','allow_unfiltered_html') , 10,3 );

	}
	static function allow_more_tags(){
		/* allow more html tags to users  ++++++++++++++++++++++++++++++++++++++++++++++++ */
		//Allow more HTML-Tags in docs

		global $allowedposttags;
		$allowedposttags['iframe'] = array(
			'src'    				=> array(),
			'class' 				=> array(),
			'height' 				=> array(),
			'width'  				=> array(),
			'frameborder'  			=> array(),
			'style'		  			=> array(),
			'mozallowfullscreen'	=> array(),
			'webkitallowfullscreen'	=> array(),
			'allowfullscreen'		=> array(),
		);
		$allowedposttags['div'] = array(
			'class'    				=> array(),
			'height' 				=> array(),
			'width'  				=> array(),
			'style'		  			=> array()
		);
	}
	static function allow_more_tags_in_post($allowedposttags, $context){
		/* allow more html tags to users  ++++++++++++++++++++++++++++++++++++++++++++++++ */
		//Allow more HTML-Tags in docs

		if ( $context == 'post' ) {
			$allowedposttags['iframe'] = array(
				'src'    				=> array(),
				'class' 				=> array(),
				'height' 				=> array(),
				'width'  				=> array(),
				'frameborder'  			=> array(),
				'style'		  			=> array(),
				'mozallowfullscreen'	=> array(),
				'webkitallowfullscreen'	=> array(),
				'allowfullscreen'		=> array(),
			);
			$allowedposttags['div'] = array(
				'class'    				=> array(),
				'height' 				=> array(),
				'width'  				=> array(),
				'style'		  			=> array()
			);
		}
		return $allowedposttags;
	}
	function allow_iframes_for_tinyMCE($a){
		$a["extended_valid_elements"] = 'iframe[src|class|height|width|frameborder]';

		return $a;
	}
}
RW_MultisiteTools::init();


