<?php
/**
Plugin Name: Element451 Events
Plugin URI:  https://github.com/spark451inc/wp-element451-events
Description: Powered by Spark451
Version:     1.0.5
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

      $this->max_events          = get_option('sp451_max_events', 3);
      $this->show_featured_first = get_option('sp451_show_featured_first', 0);
      $this->client_key          = get_option('sp451_client_key', '');
      $this->analytics_key       = get_option('sp451_analytics_key', '');
      $this->feature_key         = get_option('sp451_feature_key', '');
      $this->sp451_event_domain  = get_option('sp451_event_domain', '');
      $this->plch_image          = get_option('sp451_plch_img', $this->get_url('includes/assets/public/element_logo.jpg'));

      add_action( 'init', function () {

      } );

      add_action('wp_enqueue_scripts', [ $this, 'sp451_element_events_block_script_register' ]);
      add_action('admin_init', [ $this, 'register_fields_settings' ]);
      add_action('admin_menu', [ $this, 'admin_page_setup' ]);

      add_shortcode( 'element451_events', [ $this, 'render_events_list' ] );

      $this->deactivate();

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

    public function admin_page_setup() {
      add_menu_page(
        'Element451 Events - Powered by Spark451', // Page title
        'Element451 Events',                      // Menu title
        'manage_options',                         // Capability required
        'sp451-element-events-settings',                   // Menu slug
        [$this, 'sp451_element_events_settings_page_html'], // Callback function
        'dashicons-tide',                         // Icon URL
        300                                       // Position
      );
    }

    public function sp451_element_events_settings_page_html() {
      if (!current_user_can('manage_options')) {
        return;
      }

      if (isset($_POST['sp451_save_settings'])) {
        update_option('sp451_max_events', sanitize_text_field($_POST['sp451_max_events']));
        update_option('sp451_show_featured_first', isset($_POST['sp451_show_featured_first']) ? 1 : 0);
        update_option('sp451_client_key', sanitize_text_field($_POST['sp451_client_key']));
        update_option('sp451_analytics_key', sanitize_text_field($_POST['sp451_analytics_key']));
        update_option('sp451_feature_key', sanitize_text_field($_POST['sp451_feature_key']));
        update_option('sp451_event_domain', sanitize_text_field($_POST['sp451_event_domain']));
        update_option('sp451_plch_img', sanitize_text_field($_POST['sp451_plch_img']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
      }

      $max_events          = get_option('sp451_max_events', 3);
      $show_featured_first = get_option('sp451_show_featured_first', 0);
      $client_key          = get_option('sp451_client_key', '');
      $analytics_key       = get_option('sp451_analytics_key', '');
      $feature_key         = get_option('sp451_feature_key', '');
      $event_domain        = get_option('sp451_event_domain', '');
      $plch_img            = get_option('sp451_plch_img', ''); ?>

        <div class="wrap">
            <h1><?php esc_html_e('Element451 Events - Settings', 'textdomain'); ?></h1>
            <form method="post" action="">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="sp451_max_events"><?php esc_html_e('Maximum Events to Show', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <fieldset style="display: flex; gap: 0 10px;">
                                <label><input type="radio" name="sp451_max_events" value="3" <?php checked($max_events, 3); ?>> 3</label><br>
                                <label><input type="radio" name="sp451_max_events" value="6" <?php checked($max_events, 6); ?>> 6</label><br>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sp451_show_featured_first"><?php esc_html_e('Always Show Featured Events First:', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="sp451_show_featured_first" value="1" <?php checked($show_featured_first, 1); ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sp451_client_key"><?php esc_html_e('Client', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="sp451_client_key" value="<?php echo esc_attr($client_key); ?>" class="regular-text">
                            <p class="description">The name that appears in your Element451 instance. Example: “<strong>CLIENT</strong>.element451.io"</p>
                        </td>
                    </tr>
                </table>
                <h2 class="title">API Keys</h2>
                <p>Ask your Element451 support team for the appropriate API keys below.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sp451_analytics_key"><?php esc_html_e('Analytics Key', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="sp451_analytics_key" value="<?php echo esc_attr($analytics_key); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sp451_feature_key"><?php esc_html_e('Feature Key', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="sp451_feature_key" value="<?php echo esc_attr($feature_key); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sp451_event_domain"><?php esc_html_e('Event Domain', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="sp451_event_domain" value="<?php echo esc_attr($event_domain); ?>" class="regular-text">
                            <p class="description">URL where your Element451 event site exists.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sp451_plch_img"><?php esc_html_e('Placeholder Event Image', 'textdomain'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="sp451_plch_img" value="<?php echo esc_attr($plch_img); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e('Add the URL of the image that will be used as a placeholder.', 'textdomain'); ?></p>
                        </td>
                    </tr>
                </table>
                <h2 class="title">Instructions</h2>
                <p>
                    To add the Element451 Events block to your website, include the shortcode <strong>[element451_events]</strong> wherever you want the block to appear. Use the options above to customize how events appear.
                </p>
                <input type="hidden" name="sp451_save_settings" value="1">
              <?php submit_button(); ?>
            </form>
        </div>
      <?php
    }

    public function register_fields_settings() {
      register_setting('sp451-element-events-settings-group', 'sp451_max_events');
      register_setting('sp451-element-events-settings-group', 'sp451_show_featured_first');
      register_setting('sp451-element-events-settings-group', 'sp451_client_key');
      register_setting('sp451-element-events-settings-group', 'sp451_analytics_key');
      register_setting('sp451-element-events-settings-group', 'sp451_feature_key');
      register_setting('sp451-element-events-settings-group', 'sp451_event_domain');
      register_setting('sp451-element-events-settings-group', 'sp451_plch_img');
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
        ob_start();
        $this->shortcode_render('nodata');
        return ob_get_clean();
      }

      ob_start();
      $this->shortcode_render('app');
      return ob_get_clean();
    }
  }

  SP451_ELEMENT_EVENTS::instance()->init();
endif;