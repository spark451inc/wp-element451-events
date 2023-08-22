<?php
/**
Plugin Name: Element451 Events
Plugin URI:  https://github.com/spark451inc/wp-element451-events
Description: Powered by Spark451
Version:     1.0.2
Author:      Spark451.com
Author URI:  https://www.spark451.com/
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: element451-events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined('SP451_ELEMENT_EVENTS_DIR') ) :
    define( 'SP451_ELEMENT_EVENTS_DIR', plugin_dir_path( __FILE__ ));
endif;

define( 'SP451_ELEMENT_EVENTS_ACF_PATH', SP451_ELEMENT_EVENTS_DIR . '/includes/acf/' );
define( 'SP451_ELEMENT_EVENTS_ACF_URL', plugin_dir_url( __FILE__ ) . '/includes/acf/' );

include_once( SP451_ELEMENT_EVENTS_ACF_PATH . 'acf.php' );

require_once dirname( __FILE__ ) . '/includes/internal/singleton.php';
require_once dirname( __FILE__ ) . '/includes/internal/template.php';

require plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! class_exists( 'SP451_ELEMENT_EVENTS') ) :

	class SP451_ELEMENT_EVENTS extends SP451_ELEMENT_EVENTS_Singleton {
		protected $sp451_element_events_template;

        protected $max_events          = 3;
        protected $show_featured_first = false;
        protected $client_key          = '';
        protected $analytics_key       = '';
        protected $feature_key         = '';
        protected $plch_image          = '';
        protected $sp451_event_domain  = '';

		public function init() {
			$this->include( 'includes/templates.php' );
			$this->sp451_element_events_template = new SP451_ELEMENT_EVENTS_Template_Loader();

			add_action( 'init', function () {

            } );

            add_action('wp_enqueue_scripts', [ $this, 'sp451_element_events_block_script_register' ]);

            add_filter('acf/settings/url', function ( $url ) {
                return SP451_ELEMENT_EVENTS_ACF_URL;
            });

            // Set ACF json folder inside acf plugin folder
            add_filter('acf/settings/save_json', function ( $path ) {
                $path = $this->plugin_path('includes/acf/acf-json');
                return $path;
            });
//             Load from new folder
            add_filter('acf/settings/load_json', function ( $paths ) {
                // append path
                $paths[] = $this->plugin_path('includes/acf/acf-json');
                // return
                return $paths;
            });

            add_shortcode( 'element451_events', [ $this, 'render_events_list' ] );

            $this->deactivate();
            $this->admin_page_setup();

            add_action('acf/init', function () {
                $this->max_events          = get_field('sp451_max_events', 'sp451-element-events');
                $this->show_featured_first = get_field('sp451_show_featured_first', 'sp451-element-events');
                $this->client_key          = get_field('sp451_client_key', 'sp451-element-events');
                $this->analytics_key       = get_field('sp451_analytics_key', 'sp451-element-events');
                $this->feature_key         = get_field('sp451_feature_key', 'sp451-element-events');
                $this->sp451_event_domain  = get_field('sp451_event_domain', 'sp451-element-events');
                $this->plch_image          = get_field('sp451_plch_img', 'sp451-element-events') ? get_field('sp451_plch_img', 'sp451-element-events')['url'] : $this->get_url('includes/assets/public/element_logo.jpg');
            });

            $myUpdateChecker = PucFactory::buildUpdateChecker(
                'https://github.com/spark451inc/wp-element451-events',
                __FILE__, //Full path to the main plugin file or functions.php.
                'element451-events'
            );

            $myUpdateChecker->setBranch('master');

        }

		public function deactivate() {
			register_deactivation_hook( __FILE__, function () {

			} );
		}

        public function plugin_path( $filename = '' ) {
            return plugin_dir_path( __FILE__ ) . ltrim( $filename, '/' );
        }

        public function include( $filename = '' ) {
            $file_path = $this->plugin_path( $filename );

            if ( file_exists( $file_path ) ) {
                include_once $file_path;
            }
        }

        private function admin_page_setup() {
            if ( function_exists('acf_add_options_page') ) {
                acf_add_options_page(array(
                    'page_title' 	=> '<div style="line-height: 1;">Element451 Events</div><em style="font-size: 12px;">Powered by Spark451</em>',
                    'menu_title'	=> '<span>Element451 Events</span>',
                    'menu_slug' 	=> 'sp451-element-events',
                    'post_id'       => 'sp451-element-events',
                    'capability'	=> 'edit_posts',
                    'icon_url'      => 'dashicons-tide',
                    'redirect'		=> false,
                    'position'      => 300,
                ));
            }
        }

        public function sp451_element_events_block_script_register() {
            $path = $this->get_url('includes/assets/app/dist/main.js');
            $path_css = $this->get_url('includes/assets/app/dist/main.css');

            wp_register_script( 'sdc-script', $path, [ 'jquery' ], '1.1', true );

            wp_enqueue_script( 'sdc-script' );

            wp_localize_script( 'sdc-script', 'sp451_api_params', array(
                'ajaxUrl'                   => site_url() . '/wp-admin/admin-ajax.php',
                'sp451_max_events'          => $this->max_events,
                'sp451_show_featured_first' => $this->show_featured_first,
                'sp451_client'              => $this->client_key,
                'sp451_apiUrl'              => 'api.451.io',
                'sp451_analytics'           => $this->analytics_key,
                'sp451_feature'             => $this->feature_key,
                'sp451_event_domain'        => $this->sp451_event_domain,
                'sp451_plch_img'            => $this->plch_image,
            ) );
            wp_enqueue_style( 'sdc-style', $path_css, [], true);
        }

        public function get_url( $filename = '' ) {
            return plugin_dir_url( __FILE__ ) . ltrim( $filename, '/' );
        }

        public function shortcode_render($template) {
            if (!$template)
                return;

            $data = [];

            $this->sp451_element_events_template
                ->set_template_data( $data )
                ->get_template_part( 'template', $template );
        }

        public function render_events_list()
        {
            if (!$this->client_key || !$this->analytics_key || !$this->feature_key) {
                $this->shortcode_render('nodata');
                return;
            }

            $this->shortcode_render('app');
        }
	}

    SP451_ELEMENT_EVENTS::instance()->init();
endif;