<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cubedesigns.gr
 * @since      1.0.0
 *
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/admin
 * @author     Apostolos Gouvalas <apo.gouv@gmail.com>
 */
class Cubedesigns_Recaptcha_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action( 'admin_menu', array($this, 'add_plugin_settings_page') );
        add_action('admin_init', array($this, 'settings_init'));

        // Fetch options once and save them to class property
        $this->options = get_option('cubedesigns_recaptcha_settings');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cubedesigns_Recaptcha_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cubedesigns_Recaptcha_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        $css_admin_version = $this->version;
    
        if (CUBEDESIGNS_RECAPTCHA_DEVELOPMENT) {
            $css_admin_version = filemtime(plugin_dir_path( __FILE__ ) . 'css/cubedesigns-recaptcha-admin.css');
            if (! $css_admin_version) {
                $css_admin_version = date("Y.m.d");
            }
        }

		wp_enqueue_style( 
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/cubedesigns-recaptcha-admin.css',
            array(),
            $css_admin_version,
            'all'
        );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cubedesigns_Recaptcha_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cubedesigns_Recaptcha_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        $js_admin_version = $this->version;
    
        if (CUBEDESIGNS_RECAPTCHA_DEVELOPMENT) {
            $js_admin_version = filemtime(plugin_dir_path( __FILE__ ) . 'js/cubedesigns-recaptcha-admin.js');
            if (! $js_admin_version) {
                $js_admin_version = date("Y.m.d");
            }
        }

		wp_enqueue_script( 
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/cubedesigns-recaptcha-admin.js',
            array( 'jquery' ),
            $js_admin_version,
            false
        );

	}

    /**
     * Add plugin settings page to admin menu.
     */
    public function add_plugin_settings_page()
    {
        add_options_page(
            __('CubeDesigns - reCAPTCHA Settings', 'cubedesigns-recaptcha'),
            __('CubeDesigns - reCAPTCHA', 'cubedesigns-recaptcha'),
            'manage_options',
            'cubedesigns_recaptcha_settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render the plugin settings page.
     */
    public function render_settings_page()
    {
        ?>
        <div class="wrap" id="cubedesigns_recaptcha_settings_page_wrapper">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
			<form method="post" action="options.php" id="cubedesigns_recaptcha_settings_page_form">
                <?php
                settings_fields('cubedesigns_recaptcha_settings_group');
                do_settings_sections('cubedesigns_recaptcha_settings_page');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

	public function settings_init() {
        register_setting('cubedesigns_recaptcha_settings_group', 'cubedesigns_recaptcha_settings');

        $icon_tools_html = '<span class="cdr-icon dashicons dashicons-admin-generic"></span>';
        $icon_options_general_html = '<span class="cdr-icon dashicons dashicons-admin-settings"></span>';

        add_settings_section(
            'cubedesigns_recaptcha_section',
            $icon_tools_html . __('General Settings', 'cubedesigns-recaptcha'),
            array($this, 'settings_section_callback'),
            'cubedesigns_recaptcha_settings_page'
        );

        add_settings_field(
            'cubedesigns_recaptcha_enable_spam_protect',
            __('Enable reCAPTCHA spam prrotection', 'cubedesigns-recaptcha'),
            array($this, 'enable_recaptcha_spam_protect_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_section'
        );

        // Add fields for public and private reCAPTCHA keys
        add_settings_field(
            'cubedesigns_recaptcha_public_key_v2',
            __('reCAPTCHA Site Key (v2)', 'cubedesigns-recaptcha'),
            array($this, 'public_key_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_section'
        );

        add_settings_field(
            'cubedesigns_recaptcha_private_key_v2',
            __('reCAPTCHA Secret Key (v2)', 'cubedesigns-recaptcha'),
            array($this, 'private_key_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_section'
        );
        
        add_settings_field(
            'cubedesigns_recaptcha_theme_v2',
            __('reCAPTCHA Theme (v2)', 'cubedesigns-recaptcha'),
            array($this, 'theme_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_section'
        );

		add_settings_section(
            'cubedesigns_recaptcha_integrations_section',
            $icon_options_general_html . __('Integrations Settings', 'cubedesigns-recaptcha'),
            array($this, 'integrations_section_callback'),
            'cubedesigns_recaptcha_settings_page'
        );

        // Add switches for enabling/disabling on different forms
        add_settings_field(
            'cubedesigns_recaptcha_enable_login',
            __('Enable reCAPTCHA for Login Form', 'cubedesigns-recaptcha'),
            array($this, 'enable_login_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_integrations_section'
        );

        add_settings_field(
            'cubedesigns_recaptcha_enable_reset_password',
            __('Enable reCAPTCHA for Reset Password Form', 'cubedesigns-recaptcha'),
            array($this, 'enable_reset_password_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_integrations_section'
        );
        
        add_settings_field(
            'cubedesigns_recaptcha_enable_registration',
            __('Enable reCAPTCHA for Registration Form', 'cubedesigns-recaptcha'),
            array($this, 'enable_registration_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_integrations_section'
        );
        
        add_settings_field(
            'cubedesigns_recaptcha_enable_comments',
            __('Enable reCAPTCHA for Comments', 'cubedesigns-recaptcha'),
            array($this, 'enable_comments_callback'),
            'cubedesigns_recaptcha_settings_page',
            'cubedesigns_recaptcha_integrations_section'
        );
    }

	public function settings_section_callback() {
		?>
		<div class="settings_section_desc" style="display: inline-block;">
		<?php
        echo __('Configure the settings for CubeDesigns - reCAPTCHA plugin.', 'cubedesigns-recaptcha');
		?>
		<hr>
		</div>
		<?php
    }

	public function enable_recaptcha_spam_protect_callback() {
        ?>
        <input type="checkbox" class="wpcdr-ui-toggle" name="cubedesigns_recaptcha_settings[enable_recaptcha_spam_protect]" value="1" <?php checked(isset($this->options['enable_recaptcha_spam_protect']) && $this->options['enable_recaptcha_spam_protect'], 1); ?>>
        <?php
    }

    // Callback functions for other fields (public and private reCAPTCHA keys, switches for forms)

    public function public_key_callback() {
        ?>
        <input type="text" name="cubedesigns_recaptcha_settings[recaptcha_public_key_v2]" value="<?php echo isset($this->options['recaptcha_public_key_v2']) ? esc_attr($this->options['recaptcha_public_key_v2']) : ''; ?>">
        <?php
    }

    public function private_key_callback() {
        ?>
        <input type="text" name="cubedesigns_recaptcha_settings[recaptcha_private_key_v2]" value="<?php echo isset($this->options['recaptcha_private_key_v2']) ? esc_attr($this->options['recaptcha_private_key_v2']) : ''; ?>">
        <?php
    }

    public function theme_callback() {
        ?>
        <select name="cubedesigns_recaptcha_settings[recaptcha_theme_v2]">
            <option value="light" <?php selected(isset($this->options['recaptcha_theme_v2']) && $this->options['recaptcha_theme_v2'] === 'light'); ?>><?php esc_html_e('Light', 'cubedesigns-recaptcha'); ?></option>
            <option value="dark" <?php selected(isset($this->options['recaptcha_theme_v2']) && $this->options['recaptcha_theme_v2'] === 'dark'); ?>><?php esc_html_e('Dark', 'cubedesigns-recaptcha'); ?></option>
        </select>
        <?php
    }

	public function integrations_section_callback() {
		?>
		<div class="integrations_section_desc" style="display: inline-block;">
		<?php
        echo __('Enable or Disable reCAPTCHA on default WordPress forms.', 'cubedesigns-recaptcha');
		?>
		<hr>
		</div>
		<?php
    }

    // Callback functions for enabling/disabling reCAPTCHA on different forms
    public function enable_login_callback() {
        ?>
        <input type="checkbox" class="wpcdr-ui-toggle" name="cubedesigns_recaptcha_settings[enable_on_login]" value="1" <?php checked(isset($this->options['enable_on_login']) && $this->options['enable_on_login'], 1); ?>>
        <?php
    }
    
    public function enable_reset_password_callback() {
        ?>
        <input type="checkbox" class="wpcdr-ui-toggle" name="cubedesigns_recaptcha_settings[enable_on_reset_password]" value="1" <?php checked(isset($this->options['enable_on_reset_password']) && $this->options['enable_on_reset_password'], 1); ?>>
        <?php
    }
    
    public function enable_registration_callback() {
        ?>
        <input type="checkbox" class="wpcdr-ui-toggle" name="cubedesigns_recaptcha_settings[enable_on_registration]" value="1" <?php checked(isset($this->options['enable_on_registration']) && $this->options['enable_on_registration'], 1); ?>>
        <?php
    }
    
    public function enable_comments_callback() {
        ?>
        <input type="checkbox" class="wpcdr-ui-toggle" name="cubedesigns_recaptcha_settings[enable_on_comments]" value="1" <?php checked(isset($this->options['enable_on_comments']) && $this->options['enable_on_comments'], 1); ?>>
        <?php
    }
    
}
