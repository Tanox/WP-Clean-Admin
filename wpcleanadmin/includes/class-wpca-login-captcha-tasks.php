<?php
/**
 * WPCleanAdmin Login CAPTCHA Tasks Trait
 *
 * 登录 CAPTCHA 生成、渲染与校验，从 class-wpca-login.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.9.0
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 登录 CAPTCHA 任务 trait
 */
trait LoginCaptchaTasks {

    /**
     * Initialize CAPTCHA
     */
    private function init_captcha() {
        // Load settings
        $settings = \wpca_get_settings();

        // Check if CAPTCHA is enabled
        if ( isset( $settings['login'] ) && isset( $settings['login']['login_captcha'] ) && $settings['login']['login_captcha'] ) {
            if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
                // Add CAPTCHA hooks
                \add_action( 'login_form', array( $this, 'render_captcha' ) );
                \add_filter( 'authenticate', array( $this, 'verify_captcha' ), 25, 3 );
            }
        }
    }

    /**
     * Generate CAPTCHA code
     *
     * @return string Generated CAPTCHA code
     */
    private function generate_captcha() {
        // Generate random code
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code       = '';
        $max_index  = strlen( $characters ) - 1;

        for ( $i = 0; $i < 6; $i++ ) {
            // 使用密码学安全的随机数生成验证码，避免 rand() 可预测导致验证码被破解
            $code .= $characters[ random_int( 0, $max_index ) ];
        }

        // Store in transient (wp_session doesn't exist in WordPress core)
        $user_ip = $this->get_captcha_user_key();
        if ( function_exists( '\set_transient' ) && '' !== $user_ip ) {
            \set_transient( 'wpca_captcha_' . $user_ip, $code, 300 ); // 5 minutes
        }

        return $code;
    }

    /**
     * Get a sanitized per-visitor key used to store the CAPTCHA code
     *
     * @return string Visitor key, or empty string when unavailable
     */
    private function get_captcha_user_key() {
        if ( ! isset( $_SERVER['REMOTE_ADDR'] ) ) {
            return '';
        }

        $raw = function_exists( '\wp_unslash' ) ? \wp_unslash( $_SERVER['REMOTE_ADDR'] ) : $_SERVER['REMOTE_ADDR'];

        return function_exists( '\sanitize_text_field' ) ? \sanitize_text_field( $raw ) : $raw;
    }

    /**
     * Generate CAPTCHA image
     */
    public function generate_captcha_image() {
        // GD 扩展缺失时直接返回 500，避免调用未定义函数导致致命错误
        if ( ! function_exists( 'imagecreatetruecolor' ) ) {
            if ( function_exists( '\status_header' ) ) {
                \status_header( 500 );
            }
            exit;
        }

        // Generate CAPTCHA code
        $code = $this->generate_captcha();

        // Create image
        $width = 120;
        $height = 40;

        $image = imagecreatetruecolor( $width, $height );

        // Set colors
        $bg_color = imagecolorallocate( $image, 240, 240, 240 );
        $text_color = imagecolorallocate( $image, 30, 30, 30 );
        $line_color = imagecolorallocate( $image, 150, 150, 150 );

        // Fill background
        imagefilledrectangle( $image, 0, 0, $width, $height, $bg_color );

        // Add noise lines
        for ( $i = 0; $i < 5; $i++ ) {
            imageline( $image, rand( 0, $width ), rand( 0, $height ), rand( 0, $width ), rand( 0, $height ), $line_color );
        }

        // Add text
        $font = 5;
        $text_width = imagefontwidth( $font ) * strlen( $code );
        $text_height = imagefontheight( $font );
        $x = ( $width - $text_width ) / 2;
        $y = ( $height - $text_height ) / 2;

        imagestring( $image, $font, $x, $y, $code, $text_color );

        // Output image
        header( 'Content-type: image/png' );
        imagepng( $image );
        imagedestroy( $image );
        exit;
    }

    /**
     * Render CAPTCHA form
     */
    public function render_captcha() {
        // Get current URL
        $current_url = ( function_exists( '\is_ssl' ) && \is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // Generate CAPTCHA image URL
        $captcha_url = function_exists( '\add_query_arg' ) ? \add_query_arg( array( 'wpca_captcha' => '1' ), $current_url ) : $current_url;

        // Add cache buster
        $captcha_url = function_exists( '\add_query_arg' ) ? \add_query_arg( array( 't' => time() ), $captcha_url ) : $captcha_url;

        ?>
        <div class="wpca-captcha-form">
            <p>
                <label for="wpca_captcha"><?php echo \esc_html( \__( 'CAPTCHA', \WPCA_TEXT_DOMAIN ) ); ?></label>
                <br />
                <img src="<?php echo \esc_url( $captcha_url ); ?>" alt="CAPTCHA" class="wpca-captcha-image" />
                <br />
                <input type="text" name="wpca_captcha" id="wpca_captcha" class="input" value="" size="20" maxlength="6" autocomplete="off" placeholder="<?php echo \esc_attr( \__( 'Enter CAPTCHA code', \WPCA_TEXT_DOMAIN ) ); ?>" />
                <br />
                <small><a href="<?php echo \esc_url( $current_url ); ?>" class="wpca-refresh-captcha"><?php echo \esc_html( \__( 'Refresh CAPTCHA', \WPCA_TEXT_DOMAIN ) ); ?></a></small>
            </p>
        </div>
        <?php
    }

    /**
     * Verify CAPTCHA
     *
     * @param object $user User object or error
     * @param string $username Username
     * @param string $password Password
     * @return object Modified user or error
     */
    public function verify_captcha( $user, $username, $password ) {
        // Skip if user is already an error
        if ( function_exists( '\is_wp_error' ) && \is_wp_error( $user ) ) {
            return $user;
        }

        // 该过滤器仅在验证码启用时注册，因此此处必须校验验证码：
        // 若请求未携带 wpca_captcha 字段，直接拒绝，避免“不提交字段即绕过验证码”。
        if ( ! isset( $_POST['wpca_captcha'] ) ) {
            return new \WP_Error( 'invalid_captcha', \__( 'Invalid CAPTCHA code. Please try again.', \WPCA_TEXT_DOMAIN ) );
        }

        $raw_code       = function_exists( '\wp_unslash' ) ? \wp_unslash( $_POST['wpca_captcha'] ) : $_POST['wpca_captcha'];
        $submitted_code = function_exists( '\sanitize_text_field' ) ? \sanitize_text_field( $raw_code ) : $raw_code;

        // Get stored CAPTCHA code
        $stored_code = '';
        $user_ip     = $this->get_captcha_user_key();

        // Use transient for storage (wp_session doesn't exist in WordPress core)
        if ( function_exists( '\get_transient' ) && '' !== $user_ip ) {
            $stored_code = \get_transient( 'wpca_captcha_' . $user_ip );
        }

        // Verify CAPTCHA code with a constant-time comparison
        if (
            '' === (string) $submitted_code
            || '' === (string) $stored_code
            || ! hash_equals( strtoupper( (string) $stored_code ), strtoupper( (string) $submitted_code ) )
        ) {
            return new \WP_Error( 'invalid_captcha', \__( 'Invalid CAPTCHA code. Please try again.', \WPCA_TEXT_DOMAIN ) );
        }

        // 校验通过后立即失效，防止同一验证码被重放
        if ( function_exists( '\delete_transient' ) && '' !== $user_ip ) {
            \delete_transient( 'wpca_captcha_' . $user_ip );
        }

        return $user;
    }
}
