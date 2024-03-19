<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://cubedesigns.gr
 * @since      1.0.0
 *
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cubedesigns_Recaptcha
 * @subpackage Cubedesigns_Recaptcha/public
 * @author     Apostolos Gouvalas <apo.gouv@gmail.com>
 */
class Cubedesigns_Recaptcha_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        // Fetch options once and save them to class property
        $this->options = get_option('cubedesigns_recaptcha_settings');

        if( CUBEDESIGNS_RECAPTCHA_DEVELOPMENT && is_user_logged_in() && current_user_can('administrator') ) {
            // $debug_data = [
            //     'enable_recaptcha_spam_protect' => $this->options['enable_recaptcha_spam_protect'],
            //     'enable_recaptcha_spam_protect_is_active' => ($this->options['enable_recaptcha_spam_protect'] === '1') ? 'YES' : 'NO',
            //     'enable_on_login' => $this->options['enable_on_login'],
            //     'enable_on_login_is_active' => ($this->options['enable_on_login'] === '1') ? 'YES' : 'NO',
            // ];

            // highlight_string("<?php\n\$this->options =\n" . var_export($debug_data, true) . ";\n");
            // die();
        }

        // Enqueue reCAPTCHA script if spam protection is enabled
        if ($this->options['enable_recaptcha_spam_protect'] === '1') {
            // add_action('wp_enqueue_scripts', array($this, 'enqueue_recaptcha_script'));

            // Add and handle captcha validation on Login form if it's setting is ON
            if ( $this->options['enable_on_login'] === '1') {
                // Add extra css styles for better vidibility of the Google reCAPTCHA field
                add_action('login_enqueue_scripts', array($this, 'enqueue_recaptcha_login_styles'));

                // Add Google reCAPTCHA field to the login form
                add_action( 'login_form', array($this, 'display_recaptcha_widget_on_login_form' ) );

                // Validate the Google reCAPTCHA field on login submit
                add_filter('wp_authenticate_user', array($this, 'validate_recaptcha_response_on_login'), 10, 2);
            }

            // Add and handle captcha validation on Reset password form if it's setting is ON
            if ( $this->options['enable_on_reset_password'] === '1') {
                // Add extra css styles for better vidibility of the Google reCAPTCHA field
                add_action('login_enqueue_scripts', array($this, 'enqueue_recaptcha_lostpassword_styles'));

                add_action( 'lostpassword_form', array($this, 'display_recaptcha_widget_on_lostpassword_form' ) );

                add_action( 'lostpassword_post', array($this, 'validate_recaptcha_response_on_lostpassword' ) );
            }
            
            // Add and handle captcha validation on Registration form if it's setting is ON
            if ( $this->options['enable_on_registration'] === '1') {
                add_action( 'register_form', array($this, 'display_recaptcha_widget_on_register_form' ) );

                add_filter( 'registration_errors', array($this, 'validate_recaptcha_response_on_register' ), 10, 3 );
            }
            
            // Add and handle captcha validation on Comments form if it's setting is ON
            if ( $this->options['enable_on_comments'] === '1') {
                add_action( 'comment_form_after_fields', array($this, 'display_recaptcha_widget_on_comment_form' ) );

                add_action( 'comment_form_logged_in_after', array($this, 'display_recaptcha_widget_on_comment_form' ) );

                add_filter( 'preprocess_comment', array($this, 'validate_recaptcha_response_on_comment' ) );
            }

        }
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cubedesigns-recaptcha-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cubedesigns-recaptcha-public.js', array( 'jquery' ), $this->version, false );

	}

    // Callback function to enqueue reCAPTCHA script
    public function enqueue_recaptcha_script() {
        $site_key = $this->options['recaptcha_public_key_v2'];
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $site_key, [], null, true);
    }
    
    public function enqueue_recaptcha_login_styles() {
        $css_recaptcha_login_version = $this->version;
    
        if (CUBEDESIGNS_RECAPTCHA_DEVELOPMENT) {
            $css_recaptcha_login_version = filemtime(plugin_dir_path( __FILE__ ) . 'css/cubedesigns-recaptcha-admin.css');
            if (! $css_recaptcha_login_version) {
                $css_recaptcha_login_version = date("Y.m.d");
            }
        }

        wp_enqueue_style( $this->plugin_name . '-login_styles', plugin_dir_url( __FILE__ ) . 'css/cubedesigns-recaptcha-login_styles.css', array(), $css_recaptcha_login_version, 'all' );
    }

    public function enqueue_recaptcha_lostpassword_styles() {
        $css_recaptcha_lostpassword_version = $this->version;
    
        if (CUBEDESIGNS_RECAPTCHA_DEVELOPMENT) {
            $css_recaptcha_lostpassword_version = filemtime(plugin_dir_path( __FILE__ ) . 'css/cubedesigns-recaptcha-admin.css');
            if (! $css_recaptcha_lostpassword_version) {
                $css_recaptcha_lostpassword_version = date("Y.m.d");
            }
        }

        wp_enqueue_style( $this->plugin_name . '-lostpassword_styles', plugin_dir_url( __FILE__ ) . 'css/cubedesigns-recaptcha-lostpassword_styles.css', array(), $css_recaptcha_lostpassword_version, 'all' );
    }

    public function display_recaptcha_widget_on_login_form() {
        $this->getRecaptchaFormField('login', true);
    }
    
    public function display_recaptcha_widget_on_lostpassword_form() {
        $this->getRecaptchaFormField('lostpassword', true);
    }
    
    public function display_recaptcha_widget_on_register_form() {
        $this->getRecaptchaFormField('register', true);
    }
    
    public function display_recaptcha_widget_on_comment_form() {
        if ( ! current_user_can('administrator') ) {
            $this->getRecaptchaFormField('comment', true);
        }
    }

    // Callback function to display reCAPTCHA widget
    public function getRecaptchaFormField($action, $echo = false) {

        $site_key = isset($this->options['recaptcha_public_key_v2']) ? $this->options['recaptcha_public_key_v2'] : '';
        $secret_key = isset($this->options['recaptcha_private_key_v2']) ? $this->options['recaptcha_private_key_v2'] : '';
        $theme = isset($this->options['recaptcha_theme_v2']) ? $this->options['recaptcha_theme_v2'] : 'light';

        if ( empty( $site_key ) || empty( $secret_key ) ) {
			return;
		}

        unset($secret_key);

        if ('comment' === $action) {
            $theme = 'dark';
        }

        // Start output buffering
        ob_start();

        $script = ( "if('function' !== typeof cdrecaptcha2) {
			function cdrecaptcha2() {
				[].forEach.call(document.querySelectorAll('.cdr-g-recaptcha2'), function(el) {
					const action = el.getAttribute('data-action');
					cdgrecaptcha2[action] = grecaptcha.render(
						el,
						{
							'sitekey': '" . esc_attr( $site_key ) . "',
							'theme': '" . esc_attr( $theme ) . "'
						}
					);
				});
			}
		}" );

        wp_enqueue_script( 'recaptcha-api-v2', 'https://www.google.com/recaptcha/api.js?onload=cdrecaptcha2', array(), null );
		wp_add_inline_script( 'recaptcha-api-v2', $script, 'before' );
		wp_localize_script( 'recaptcha-api-v2', 'cdgrecaptcha2', array() );


        ?>
        <div id="cdr-g-recaptcha2-<?php echo esc_attr( $action ); ?>" 
            class="cdr-g-recaptcha2" 
            data-action="<?php echo esc_attr( $action ); ?>"></div>
        <?php
        // End output buffering and capture the output
        $widget_html = ob_get_clean();

        if ($echo) {
            echo $widget_html; // Echo the captured HTML immediately
        } else {
            return $widget_html; // Return the captured HTML
        }
    }

    // Callback function to validate reCAPTCHA response on Login
    public function validate_recaptcha_response_on_login($username, $password) {
        $return = $this->validate_recaptcha_response();
        if( is_wp_error( $return ) ) {
            return $return;
        }

        return $username;
    }

    // Callback function to validate reCAPTCHA response on Lostpassword
    public function validate_recaptcha_response_on_lostpassword( $errors ) {
        $reCaptchaResponse = $this->validate_recaptcha_response();
        if( is_wp_error( $reCaptchaResponse ) ) {
            $errors->add('invalid_recaptcha', $reCaptchaResponse->get_error_message());
        }

        return $errors;
    }
    
    // Callback function to validate reCAPTCHA response on Register
    public function validate_recaptcha_response_on_register( $errors, $sanitized_user_login, $user_email ) {
        $reCaptchaResponse = $this->validate_recaptcha_response();
        if( is_wp_error( $reCaptchaResponse ) ) {
            $errors->add('invalid_recaptcha', $reCaptchaResponse->get_error_message());
        }

        return $errors;
    }
    
    // Callback function to validate reCAPTCHA response on Comment
    public function validate_recaptcha_response_on_comment( $commentdata ) {

        // If current user is logged in and is Admin, we do not have captcha
        if ( is_user_logged_in() && current_user_can('administrator') ) {
			return $commentdata;
		}

        $reCaptchaResponse = $this->validate_recaptcha_response();
        if( is_wp_error( $reCaptchaResponse ) ) {
            wp_die( $reCaptchaResponse, '', array( 'back_link' => false ) );
        }

        return $commentdata;
    }

    // Callback function to validate reCAPTCHA response
    public function validate_recaptcha_response() {
        $recaptcha_token = $_POST['g-recaptcha-response'];

        if (empty($recaptcha_token)) {
            // If reCAPTCHA token is empty, it means reCAPTCHA wasn't completed
            return new WP_Error('invalid_recaptcha', __('Please complete the reCAPTCHA.', 'cubedesigns-recaptcha'));
        }

        // Validate reCAPTCHA token
        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $this->options['recaptcha_private_key_v2'],
                'response' => $recaptcha_token,
            ],
        ]);

        $response_body = json_decode(wp_remote_retrieve_body($response));

        if (!$response_body->success) {
            // If reCAPTCHA validation fails, return error
            return new WP_Error('invalid_recaptcha', __('reCAPTCHA verification failed. Please try again.', 'cubedesigns-recaptcha'));
        }

        // reCAPTCHA validation successful, proceed with rest of current form
        return true;
    }
}
