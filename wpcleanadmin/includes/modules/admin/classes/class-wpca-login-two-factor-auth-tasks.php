<?php
/**
 * WPCleanAdmin Login Two-Factor Auth Tasks
 *
 * 双因素认证表单渲染、登录校验与失败处理，公开 API 不变。
 * 主类 `Login_TwoFactor` 通过 `use LoginTwoFactorAuthTasks;` 组合。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Login two-factor auth tasks
 */
trait LoginTwoFactorAuthTasks {

    /**
     * Render two-factor authentication form
     */
    public function render_two_factor_form(): void {
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
     * @param object $user
     * @param string $password
     * @return object
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

            // Get secret and verify code via TOTP helper
            $secret = function_exists( '\get_user_meta' ) ? \get_user_meta( $user_id, 'wpca_two_factor_secret', true ) : '';
            if ( ! $this->totp->verify( $code, $secret ) ) {
                return new \WP_Error( 'invalid_code', \__( 'Invalid authentication code.', \WPCA_TEXT_DOMAIN ) );
            }

            // Code is valid, allow login
            return $user;
        } elseif ( isset( $user->ID ) ) {
            // Check if two-factor is enabled for this user
            $two_factor_enabled = function_exists( '\get_user_meta' ) ? \get_user_meta( $user->ID, 'wpca_two_factor_enabled', true ) : false;

            if ( $two_factor_enabled ) {
                // Block authentication until a valid code is submitted. The 2FA
                // form is rendered by render_two_factor_form() when the login
                // screen is requested with wpca_two_factor=1.
                $message = \__( 'Two-factor authentication is required for this account.', \WPCA_TEXT_DOMAIN );

                if ( function_exists( '\add_query_arg' ) && function_exists( '\wp_login_url' ) && function_exists( '\wp_create_nonce' ) && function_exists( '\esc_url' ) ) {
                    $form_url = \add_query_arg(
                        array(
                            'wpca_two_factor' => '1',
                            'wpca_user_id'    => $user->ID,
                            'wpca_nonce'      => \wp_create_nonce( 'wpca_two_factor' )
                        ),
                        \wp_login_url()
                    );
                    $message .= ' <a href="' . \esc_url( $form_url ) . '">' . \__( 'Enter your authentication code.', \WPCA_TEXT_DOMAIN ) . '</a>';
                }

                return new \WP_Error( 'wpca_two_factor_required', $message );
            }
        }

        return $user;
    }

    /**
     * Handle two-factor authentication failure
     *
     * @param string $username
     */
    public function handle_two_factor_failure( $username ): void {
        // Check if this is a two-factor authentication failure
        if ( isset( $_POST['wpca_two_factor_code'] ) ) {
            // Add error message
            if ( function_exists( '\add_action' ) ) {
                \add_action(
                    'login_message',
                    function() {
                        return '<div id="login_error">' . \__( '<strong>ERROR:</strong> Invalid authentication code.', \WPCA_TEXT_DOMAIN ) . '</div>';
                    }
                );
            }
        }
    }
}
