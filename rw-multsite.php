<?php
/**
 * Plugin Name:      RW Multisite
 * Plugin URI:       https://github.com/rpi-virtuell/rw_multisite
 * Description:      Tools for Wordpress Multisite. Use shortcodes: [rw_multisite_list_sites] [rw_multisite_list_my_sites]
 * Author:           Joachim Happel
 * Version:          0.0.1
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
     * @returns array
     */
    static function get_my_blogs() {
        $html = '';

        if(is_user_logged_in()){
            $subsites=get_blogs_of_user(get_current_user_id( ), false);

            $html= '<h1 class="my-blog-sites">Meine Seiten:</h1><ul>';

            foreach( $subsites as $subsite ) {
                $subsite_domain = get_object_vars($subsite)["domain"];
                $subsite_path = get_object_vars($subsite)["path"];
                $subsite_name = get_object_vars($subsite)["blogname"];
                if($subsite_name  == '') $subsite_name = $subsite_domain. $subsite_path;
                //$subsite_description = get_blog_option($subsite_id,'blogdescription');
                $html .= '<li><a href="http://' . $subsite_domain . $subsite_path .'">' . $subsite_name . '</a></li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }
    /**
     * gibt alle Blogs einer Multisite aus
     */
    static function get_all_blogs() {
        $html= '<ul><span class="my-blog-sites">Seiten:</span>';
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

    static function init(){

        add_shortcode('rw_multisite_list_sites',function(){
            return RW_MultisiteTools::get_all_blogs();
        });
        add_shortcode('rw_multisite_list_my_sites',function(){
            return RW_MultisiteTools::get_my_blogs();
        });

        return $this;

    }

}
RW_MultisiteTools::init();