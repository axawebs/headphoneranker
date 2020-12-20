<?php
/**
 * @package headphone_ranker
 */

 /* 
 Plugin Name: Headphone Ranker
 Plugin URI: https://github.com/axawebs/headphoneranker
 Description: Custom created plugin for Headphone Ranking on Wordpress
 Version: 1.0
 Author: Chandima Jayasiri
 Author URI: mailto:chandimaj@icloud.com
 License: GPLV2 or later
 Text Domain: headphone_ranker
 */

 /*
 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (! defined( 'ABSPATH') ){
    die;
}

class headphoneRanker
{

    public $plugin_name;
    public $settings = array();
    public $sections = array();
    public $fields = array();

    //Method Access Modifiers
    // public - can be accessed from outside the class
    // protected - can only be accessed within the class ($this->protected_method())
    // protected - can only be accessed from constructor

    function __construct(){
        //add_action ('init', array($this, 'custom_post_type')); // tell wp to execute method on init
        $this->plugin_name = plugin_basename( __FILE__ );
    }

    function register(){
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );

        add_action ( 'admin_menu', array( $this, 'add_admin_pages' ));
        add_filter ("plugin_action_links_$this->plugin_name", array ($this, 'settings_link'));
       // add_filter( 'single_template', array($this, 'load_custom_post_specific_template'));
    }


    function add_admin_pages(){
        add_menu_page( 'Headphone Ranker Settings', 'HRanker Settings', 'manage_options', 'headphone_ranker_settings', array($this,'admin_index'), 'dashicons-pdf', 100);
    }    

    function admin_index(){
        //require template
        require_once plugin_dir_path( __FILE__ ) . 'templates/admin.php';
    }


    function settings_link($links){
        //add custom setting link
        $settings_link = '<a href="admin.php?page=headphone_ranker_settings">Settings Page</a>';
        array_push ( $links, $settings_link );
        return $links;
    }


    function activate(){
        // Plugin activated state
        // generate a Custom Post Style
        // $this->custom_post_type();
        $this->create_table();
        // Flush rewrite rules 
        flush_rewrite_rules();
    }
 
    function deactivate(){
        // Plugin deactivate state
        //Flush rewrite rules
        flush_rewrite_rules();
    }

    function uninstall(){
        //Plugin deleted
        //delete Custom Post Style
        //delete all plugin data from the DB

    }


    function create_table(){ 
        // create table if not exist
        global $wpdb;

        // Headphones
        $table_name = $wpdb->prefix."hranker_headphones";
        if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name) != $table_name) {
            $sql = 'CREATE TABLE '.$table_name.'(
            id INTEGER NOT NULL AUTO_INCREMENT,
            rank VARCHAR(50),
            device VARCHAR(50),
            price VARCHAR(20),
            value INT(2), 
            principle VARCHAR(100),
            overall_timbre VARCHAR(300),
            summary TEXT,
            ganre_focus VARCHAR(300),
            PRIMARY KEY  (id))';
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option("headphoneranker_db_version", "1.0");
        }

        // IEM
        $table_name = $wpdb->prefix."hranker_iem";
        if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name) != $table_name) {
            $sql = 'CREATE TABLE '.$table_name.'(
            id INTEGER NOT NULL AUTO_INCREMENT,
            rank VARCHAR(50),
            device VARCHAR(50),
            price VARCHAR(20),
            value INT(2), 
            overall_timbre VARCHAR(300),
            summary TEXT,
            ganre_focus VARCHAR(300),
            PRIMARY KEY  (id))';
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option("headphoneranker_db_version", "1.1");
        }

        // Earbuds
        $table_name = $wpdb->prefix."hranker_earbuds";
        if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name) != $table_name) {
            $sql = 'CREATE TABLE '.$table_name.'(
            id INTEGER NOT NULL AUTO_INCREMENT,
            rank VARCHAR(50),
            device VARCHAR(50),
            price VARCHAR(20),
            value INT(2), 
            principle VARCHAR(100),
            overall_timbre VARCHAR(300),
            summary TEXT,
            ganre_focus VARCHAR(300),
            PRIMARY KEY  (id))';
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option("headphoneranker_db_version", "1.2");
        }
    }
    
    //Enqueue on admin pages
    function enqueue_admin(){
        //Bootstrap
        wp_enqueue_style( 'bootstrap4_styles', plugins_url('/assets/bootstrap4/bootstrap_4_5_2_min.css',__FILE__));
        wp_enqueue_script( 'bootstrap4_scripts', plugins_url('/assets/bootstrap4/bootstrap_4_5_2_min.js',__FILE__), array('jquery','headphoneranker_popper_scripts'));
        //Font-Awesome
        wp_enqueue_style( 'fontawesome_css', plugins_url('/assets/font_awesome/css/font-awesome.css',__FILE__));
        //Admin scripts and styles
        wp_enqueue_style( 'headphoneranker_admin_styles', plugins_url('/assets/hranker_admin_style.css',__FILE__));
        wp_enqueue_script( 'headphoneranker_admin_script', plugins_url('/assets/hranker_admin_scripts.js',__FILE__), array('jquery'));
        //Select2
        wp_enqueue_style( 'headphoneranker_select2_styles', plugins_url('/assets/select2/select2.css',__FILE__));
        wp_enqueue_script( 'headphoneranker_select2_scripts', plugins_url('/assets/select2/select2.full.js',__FILE__), array('jquery'));
        //Popper
        wp_enqueue_script( 'headphoneranker_popper_scripts', plugins_url('/assets/popper/popper.min.js',__FILE__), array('jquery'));
    }

    //Enqueue on all other pages
    function enqueue(){
         //Bootstrap
         wp_enqueue_style( 'bootstrap4_styles', plugins_url('/assets/bootstrap4/bootstrap_4_5_2_min.css',__FILE__));
         wp_enqueue_script( 'bootstrap4_scripts', plugins_url('/assets/bootstrap4/bootstrap_4_5_2_min.js',__FILE__), array('jquery'));
         //Font-Awesome
         wp_enqueue_style( 'fontawesome_css', plugins_url('/assets/font_awesome/css/font-awesome.css',__FILE__));
         //Admin scripts and styles
         wp_enqueue_style( 'headphoneranker_admin_styles', plugins_url('/assets/hranker_admin_style.css',__FILE__));
         wp_enqueue_script( 'headphoneranker_admin_script', plugins_url('/assets/hranker_admin_scripts.js',__FILE__), array('jquery'));
         //Select2
         wp_enqueue_style( 'headphoneranker_select2_styles', plugins_url('/assets/select2/select2.css',__FILE__));
         wp_enqueue_script( 'headphoneranker_select2_scripts', plugins_url('/assets/select2/select2.full.js',__FILE__), array('jquery'));
    }
    
}

if ( class_exists('headphoneRanker') ){
    $headphone_ranker = new headphoneRanker();
    $headphone_ranker -> register();
}

//activate
register_activation_hook (__FILE__, array($headphone_ranker, 'activate'));

//deactivation
register_deactivation_hook (__FILE__, array($headphone_ranker, 'deactivate'));