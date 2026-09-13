<?php
/**
 * WPCleanAdmin Login Two-Factor Tasks Trait
 *
 * 两步验证（secret 生成、OTP 校验、表单渲染与登录拦截），
 * 从 class-wpca-login.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 登录两步验证任务 trait
 */
trait LoginTwoFactorTasks {

    /**
     * Initialize two-factor authentication
     */
    private function init_two_factor_auth() {
        // Load settings
        $settings = \wpca_get_settings();

        // Check if two-factor authentication is enabled
        if ( isset( $settings['security'] ) && isset( $settings['security']['two_factor_auth'] ) && $settings['security']['two_factor_auth'] ) {
            if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) ) {
                // Add two-factor authentication hooks
                \add_action( 'wp_authenticate_user', array( $this, 'check_two_factor_auth' ), 20, 2 );
                \add_action( 'login_form', array( $this, 'render_two_factor_form' ) );
                \add_action( 'wp_login_failed', array( $this, 'handle_two_factor_failure' ) );
                \add_action( 'user_register', array( $this, 'generate_two_factor_secret' ) );
                \add_action( 'profile_update', array( $this, 'update_two_factor_secret' ) );
            }
        }
    }

    /**
     * Generate two-factor authentication secret for user
     *
     * @param int $user_id User ID
     */
    public function generate_two_factor_secret( $user_id ) {
        // Generate random secret
        $secret = $this->create_random_secret();

        // Save secret to user meta
        if ( function_exists( '\update_user_meta' ) ) {
            \update_user_meta( $user_id, 'wpca_two_factor_secret', $secret );
            \update_user_meta( $user_id, 'wpca_two_factor_enabled', 1 );
        }
    }

    /**
     * Update two-factor authentication secret
     *
     * @param int $user_id User ID
     */
    public function update_two_factor_secret( $user_id ) {
        // Check if secret already exists
        if ( function_exists( '\get_user_meta' ) && ! \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) ) {
            // Generate new secret if it doesn't exist
            $this->generate_two_factor_secret( $user_id );
        }
    }

    /**
     * Create random secret for two-factor authentication
     *
     * @return string Random secret
     * @noinspection PhpUnusedPrivateMethodInspection Used by test validation script
     */
    public function create_random_secret() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ( $i = 0; $i < 16; $i++ ) {
            $secret .= $chars[rand( 0, strlen( $chars ) - 1 )];
        }

        return $secret;
    }

    /**
     * Generate QR code URL for two-factor authentication
     *
     * @param int $user_id User ID
     * @return string QR code URL
     */
    public function get_qr_code_url( $user_id ) {
        if ( function_exists( '\get_userdata' ) ) {
            $user = \get_userdata( $user_id );
        } else {
            $user = false;
        }
        if ( ! $user ) {
            return '';
        }

        // Get secret
        $secret = function_exists( '\get_user_meta' ) ? \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) : '';
        if ( ! $secret ) {
            return '';
        }

        // Get site name
        $site_name = function_exists( '\get_bloginfo' ) ? \get_bloginfo( 'name' ) : 'WordPress';

        // Generate otpauth URL
        $otpauth_url = 'otpauth://totp/' . urlencode( $site_name . ':' . $user->user_email ) . '?secret=' . $secret . '&issuer=' . urlencode( $site_name );

        // Return QR code URL
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode( $otpauth_url );
    }

    /**
     * Check two-factor authentication code
     *
     * @param string $code Authentication code
     * @param int $user_id User ID
     * @return bool Whether the code is valid
     */
    private function verify_two_factor_code( $code, $user_id ) {
        // Get secret
        $secret = function_exists( '\get_user_meta' ) ? \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) : '';
        if ( ! $secret ) {
            return false;
        }

        // Get current timestamp
        $timestamp = time();

        // Check code with time tolerance
        for ( $offset = -1; $offset <= 1; $offset++ ) {
            $time = floor( ( $timestamp + ( $offset * 30 ) ) / 30 );
            $otp = $this->generate_otp( $secret, $time );

            if ( $code === $otp ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate OTP code from secret and counter
     *
     * @param string $secret Secret key
     * @param int $counter Counter
     * @return string OTP code
     */
    private function generate_otp( $secret, $counter ) {
        // Convert secret to binary
        $secret_bin = $this->base32_decode( $secret );

        // Pack counter into binary
        $counter_bin = str_pad( pack( 'N', $counter ), 8, chr( 0 ), STR_PAD_LEFT );

        // Calculate HMAC-SHA1
        $hash = hash_hmac( 'sha1', $counter_bin, $secret_bin, true );

        // Get offset
        $offset = ord( substr( $hash, -1 ) ) & 0x0F;

        // Get 4 bytes from hash
        $code = unpack( 'N', substr( $hash, $offset, 4 ) )[1];
        $code &= 0x7FFFFFFF;
        $code %= 1000000;

        // Format code to 6 digits
        return str_pad( $code, 6, '0', STR_PAD_LEFT );
    }

    /**
     * Base32 decode function
     *
     * @param string $str Base32 encoded string
     * @return string Decoded binary string
     */
    private function base32_decode( $str ) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $str = strtoupper( $str );
        $str = str_replace( array( ' ', '\r', '\n', '\t' ), '', $str );

        $length = strlen( $str );
        $result = '';
        $buffer = 0;
        $buffer_length = 0;

        for ( $i = 0; $i < $length; $i++ ) {
            $char = $str[$i];
            $index = strpos( $chars, $char );

            if ( $index === false ) {
                continue;
            }

            $buffer = ( $buffer << 5 ) | $index;
            $buffer_length += 5;

            if ( $buffer_length >= 8 ) {
                $result .= chr( ( $buffer >> ( $buffer_length - 8 ) ) & 0xFF );
                $buffer_length -= 8;
            }
        }

        return $result;
    }

    /**
     * Render two-factor authentication form
     */
    public function render_two_factor_form() {
        if ( isset( $_REQUEST['wpca_two_factor'] ) && $_REQUEST['wpca_two_factor'] === '1' ) {
            ?>
            <div class="wpca-two-factor-form">
                <h2><?php echo \esc_html( \__( 'Two-Factor Authentication', \WPCA_TEXT_DOMAIN ) ); ?></h2>
                <p><?php echo \esc_html( \__( 'Please enter the 6-digit code from your authenticator app.', \WPCA_TEXT_DOMAIN ) ); ?></p>

                <label for="wpca_two_factor_code"><?php echo \esc_html( \__( 'Authentication Code', \WPCA_TEXT_DOMAIN ) ); ?></label>
                <input type="text" name="wpca_two_factor_code" id="wpca_two_factor_code" class="input" value="" size="20" maxlength="6" autocomplete="off" placeholder="123456" />

                <input type="hidden" name="wpca_user_id" value="<?php echo \esc_attr( $_REQUEST['wpca_user_id'] ); ?>" />
                <input type="hidden" name="wpca_nonce" value="<?php echo \esc_attr( \wp_create_nonce( 'wpca_two_factor' ) ); ?>" />
            </div>
            <?php
        }
    }

    /**
     * Check two-factor authentication during login
     *
     * @param object $user User object
     * @param string $password Password
     * @return object Modified user or error
     */
    public function check_two_factor_auth( $user, $password ) {
        // Check if this is a two-factor authentication request
        if ( isset( $_POST['wpca_two_factor_code'] ) && isset( $_POST['wpca_user_id'] ) && isset( $_POST['wpca_nonce'] ) ) {
            // Verify nonce
            if ( ! function_exists( '\wp_verify_nonce' ) || ! \wp_verify_nonce( $_POST['wpca_nonce'], 'wpca_two_factor' ) ) {
                return new \WP_Error( 'invalid_nonce', \__( 'Invalid nonce.', \WPCA_TEXT_DOMAIN ) );
            }

            // Get user ID
            $user_id = intval( $_POST['wpca_user_id'] );

            // Get user object
            if ( ! function_exists( '\get_userdata' ) ) {
                return new \WP_Error( 'no_user_data', \__( 'No user data function available.', \WPCA_TEXT_DOMAIN ) );
            }

            $user = \get_userdata( $user_id );
            if ( ! $user ) {
                return new \WP_Error( 'invalid_user', \__( 'Invalid user.', \WPCA_TEXT_DOMAIN ) );
            }

            // Get two-factor code
            $code = isset( $_POST['wpca_two_factor_code'] ) ? trim( $_POST['wpca_two_factor_code'] ) : '';

            // Verify code
            if ( ! $this->verify_two_factor_code( $code, $user_id ) ) {
                return new \WP_Error( 'invalid_code', \__( 'Invalid authentication code.', \WPCA_TEXT_DOMAIN ) );
            }

            // Code is valid, allow login
            return $user;
        } elseif ( isset( $user->ID ) ) {
            // Check if two-factor is enabled for this user
            $two_factor_enabled = function_exists( '\get_user_meta' ) ? \get_user_meta( $user->ID, 'wpca_two_factor_enabled', true ) : false;

            if ( $two_factor_enabled ) {
                // Redirect to two-factor form
                if ( function_exists( '\add_filter' ) ) {
                    \add_filter( 'login_redirect', function( $redirect_to, $requested_redirect_to, $user ) {
                        return \add_query_arg( array(
                            'wpca_two_factor' => '1',
                            'wpca_user_id' => $user->ID,
                            'wpca_nonce' => \wp_create_nonce( 'wpca_two_factor' )
                        ), \wp_login_url( $redirect_to ) );
                    }, 10, 3 );
                }
            }
        }

        return $user;
    }

    /**
     * Handle two-factor authentication failure
     *
     * @param string $username Username
     */
    public function handle_two_factor_failure( $username ) {
        // Check if this is a two-factor authentication failure
        if ( isset( $_POST['wpca_two_factor_code'] ) ) {
            // Add error message
            if ( function_exists( '\add_action' ) ) {
                \add_action( 'login_message', function() {
                    return '<div id="login_error">' . \__( '<strong>ERROR:</strong> Invalid authentication code.', \WPCA_TEXT_DOMAIN ) . '</div>';
                } );
            }
        }
    }
}
