<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Facebook Class
 * 
 * Handles all facebook functions
 * 
 * @package Easy Digital Downloads - Social Login
 * @since 1.0.0
 */
if (!class_exists('EDD_Slg_Social_Facebook')) {

    class EDD_Slg_Social_Facebook {

        public $facebook;
        public $user = array();
        public $user_picture = array();

        public function __construct() {

            if (!empty($_GET['display']) && $_GET['display'] == 'popup') {
                EddSocialLoginPersistentAnonymous::set('fb_display', 'popup');
                $this->edd_slg_connect_to_facebook();
            }
            if ( !empty($_GET['code']) && !empty($_GET['state']) && isset( $_GET['eddslg'] ) && $_GET['eddslg'] == 'facebook' ) {
                $this->edd_slg_fb_redirect_login();
            }
            if( (!empty( $_GET['eddslg'] ) && $_GET['eddslg'] == 'facebook') && (!empty($_GET['error_code']) && $_GET['error_code'] == 200) && ( !empty( $_GET['error'] ) && $_GET['error'] == 'access_denied' ) ){
                $this->edd_slg_fb_user_access_denied();
            }
        }

        public function edd_slg_uniqid() {
            if (isset($_COOKIE['edd_slg_uniqid'])) {
                if (get_site_transient('n_' . $_COOKIE['edd_slg_uniqid']) !== false) {
                    return $_COOKIE['sol_uniqid'];
                }
            }
            $_COOKIE['edd_slg_uniqid'] = uniqid('eddslg', true);
            setcookie('edd_slg_uniqid', $_COOKIE['edd_slg_uniqid'], time() + 3600, '/', '', false, true);
            set_site_transient('n_' . $_COOKIE['edd_slg_uniqid'], 1, 3600);

            return $_COOKIE['edd_slg_uniqid'];
        }

        /**
         * Include Facebook Class
         * 
         * Handles to load facebook class
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.0.0
         */
        public function edd_slg_initialize_facebook() {

            global $edd_options;
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            //check facebook is enable and application id and application secret is not empty
            if (!empty($edd_options['edd_slg_enable_facebook']) && !empty($edd_options['edd_slg_fb_app_id']) && !empty($edd_options['edd_slg_fb_app_secret'])) {

                if (!class_exists('Facebook')) { // loads the facebook class
                    require_once ( EDD_SLG_SOCIAL_LIB_DIR . '/Facebook/autoload.php' );
                }
                $this->facebook = new Facebook\Facebook([
                    'app_id' => $edd_options['edd_slg_fb_app_id'],
                    'app_secret' => $edd_options['edd_slg_fb_app_secret'],
                    'persistent_data_handler' => new Facebook\PersistentData\FacebookSessionPersistentDataHandler()
                ]);
                return true;
            } else {
                return false;
            }
        }

        /**
         * Get Login URL
         */
        public function edd_slg_get_login_url() {
            global $edd_options;
            //load facebook class
            $facebook = $this->edd_slg_initialize_facebook();

            //check facebook class is exist or not
            if (!$facebook)
                return false;

            $login_redirect_url = add_query_arg(array('eddslg' => 'facebook'), site_url('/'));
            return $login_redirect_url;
        }

        public function edd_slg_connect_to_facebook() {
            global $edd_options;
            $facebook = $this->edd_slg_initialize_facebook();

            //check facebook class is exist or not
            if (!$facebook)
                return false;
            $login_redirect_url = add_query_arg(array('eddslg' => 'facebook'), site_url('/'));
            $helper = $this->facebook->getRedirectLoginHelper();
            $permissions = ['email']; // Optional permissions for more permission you need to send your application for review
            $loginUrl = $helper->getLoginUrl($login_redirect_url, $permissions);
            header("Location:" . $loginUrl);
            exit;
        }

        /**
         * Login into facebook and get data
         * @return boolean
         */
        public function edd_slg_fb_redirect_login() {
            global $edd_slg_persistant_anonymous;
            $facebook = $this->edd_slg_initialize_facebook();
            //check facebook class is exist or not
            if (!$facebook)
                return false;

            $helper = $this->facebook->getRedirectLoginHelper();
            if (isset($_GET['state'])) {
                $helper->getPersistentDataHandler()->set('state', $_GET['state']);
                $_SESSION['FBRLH_state'] = $_GET['state'];
            }
            try {
                $accessToken = $helper->getAccessToken($this->edd_slg_get_login_url());
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error  
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues  
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            $accessTokenData['access_token'] = $accessToken->getValue();
            $accessTokenData['expires_in'] = $accessToken->getExpiresAt();
            $accessTokenData['created'] = strtotime("now");
            $this->setAnonymousAccessToken(json_encode($accessTokenData));
            if( $this->getAnonymousAccessToken() ){
                $this->edd_slg_get_user_details();
            }
            if (EddSocialLoginPersistentAnonymous::get('fb_display') == 'popup') {
                EddSocialLoginPersistentAnonymous::delete('fb_display');
                ?>
                <!doctype html>
                <html lang=en>
                    <head>
                        <meta charset=utf-8>
                        <title><?php _e('Authentication successful', 'edd-social-login'); ?></title>
                        <script type="text/javascript">
                            try {
                                if (window.opener !== null) {
                                	opener.showLoaderNow();
                                    window.opener.location = <?php echo wp_json_encode($this->edd_slg_get_login_url()); ?>;
                                    window.close();
                                }
                            } catch (e) {
                            }
                            window.location.reload(true);
                        </script>
                        <meta http-equiv="refresh" content="0">
                    </head>
                </html>
                <?php
                exit;
            }
        }
        
        public function edd_slg_fb_user_access_denied(){
            if( $this->getAnonymousAccessToken() ){
                $this->deleteLoginPersistentData();
            }
            ?>
            <!doctype html>
                <html lang=en>
                    <head>
                        <meta charset=utf-8>
                        <title><?php _e('Authentication error', 'edd-social-login'); ?></title>
                        <script type="text/javascript">
                            try {
                                if (window.opener !== null) {
                                	window.onunload = function (e) {
                                    opener.hideLoaderAgain();
                                        //opener.document.getElementByClass('edd-slg-login-wrapper').style.display = 'block';
                                    };
                                    window.close();
                                }
                            } catch (e) {
                            }
                            window.close();
                        </script>
                    </head>
                </html>
                <?php
                exit;
        }

        protected function setAnonymousAccessToken($accessToken) {
            EddSocialLoginPersistentAnonymous::set('facebook_at', $accessToken);
        }

        protected function getAnonymousAccessToken() {
            return EddSocialLoginPersistentAnonymous::get('facebook_at');
        }

        protected function deleteLoginPersistentData() {
            EddSocialLoginPersistentAnonymous::delete('facebook_at');
            EddSocialLoginPersistentAnonymous::delete('facebook_display');
        }

        /**
         * Function responsible to Login/Register/Link user data from FB
         */
        public function edd_slg_live_connect_user_fb_profile() {
            $facebook = $this->edd_slg_initialize_facebook();

            //check facebook class is exist or not
            if (!$facebook)
                return false;

            if ($this->edd_slg_get_user_details()) {
                /**
                 * @todo Ask mayur about redirect if email is not received
                 */
                
                $userdata = array(
                    'first_name' => $this->edd_slg_get_user_first_name(),
                    'last_name' => $this->edd_slg_get_user_last_name(),
                    'name' => $this->edd_slg_get_user_name(),
                    'email' => $this->edd_slg_get_user_email(),
                    'picture' => $this->edd_slg_get_user_picture(),
                    'cover' => $this->edd_slg_get_user_cover_picture(),
                    'link' => $this->edd_slg_get_user_profile_link(),
                    'id' => $this->edd_slg_get_fb_user(),
                );
                return $userdata;
            }
            return FALSE;
        }

        /**
         * Get Graph User details
         * @return boolean
         */
        protected function edd_slg_get_user_details() {
            // Get access token from site transient
            $accessToken = json_decode($this->getAnonymousAccessToken());
            $facebook = $this->edd_slg_initialize_facebook();
            //check facebook class is exist or not
            if (!$facebook)
                return false;
            try {
                // Get the Facebook\GraphNodes\GraphUser object for the current user.
                // If you provided a 'default_access_token', the '{access-token}' is optional.
                $response = $this->facebook->get('/me?fields=id,name,email,first_name,last_name,picture,cover', $accessToken->access_token);
                $picture_response = $this->facebook->get('/me/picture?redirect=false&height=500', $accessToken->access_token);

            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'ERROR in: Graph ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'ERROR: validation fails ' . $e->getMessage();
                exit;
            }
            $this->user = $response->getGraphUser();
            $this->user_picture = $picture_response->getGraphUser();

            return true;
        }

        /**
         * Get Facebook User
         * 
         * Handles to return facebook user id
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_fb_user() {
            $this->edd_slg_get_user_details();
            //check facebook class is exist or not
            if (!$this->user)
                return false;
            
            return apply_filters('edd_slg_get_fb_user', $this->user->getID());
        }

        /**
         * Get Firstname
         * 
         * Handles to return facebook user's firstname
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_first_name() {
            $this->edd_slg_get_user_details();
            if (!$this->user)
                return false;
            return apply_filters('edd_slg_get_user_first_name', $this->user->getFirstName());
        }

        /**
         * Get Lastname
         * 
         * Handles to return facebook user's Lastname
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_last_name() {
            $this->edd_slg_get_user_details();
            if (!$this->user)
                return false;
            return apply_filters('edd_slg_get_user_last_name', $this->user->getLastName());
        }

        /**
         * Get Full Name
         * 
         * Handles to return facebook user's Full name
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_name() {
            $this->edd_slg_get_user_details();
            if (!$this->user)
                return FALSE;
            return apply_filters('edd_slg_get_user_name', $this->user->getName());
        }

        /**
         * Get Email
         * 
         * Handles to return facebook user's Email
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_email() {
            if (!$this->user)
                return FALSE;
            return apply_filters('edd_slg_get_user_email', $this->user->getEmail());
        }

        /**
         * Get profile picture
         * 
         * Handles to return facebook user's profile picture
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_picture() {
            
           if ( !isset( $this->user_picture['url'] ) || empty( $this->user_picture['url'] ) )
                return FALSE;

            return apply_filters('edd_slg_get_user_picture', $this->user_picture['url']);
        }

        /**
         * Get cover picture
         * 
         * Handles to return facebook user's cover picture
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_cover_picture() {
            if (!$this->user)
                return FALSE;

            $cover_field_source = '';
            $cover_field = $this->user->getField('cover');

            if( !empty( $cover_field ) ) {

            	$cover_field_source = $this->user->getField('cover')->getField('source');
            }

            return apply_filters('edd_slg_get_user_cover_picture', $cover_field_source);
        }

        /**
         * Get Profile Link
         * 
         * Handles to get User's profile Link
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.6.4
         */
        public function edd_slg_get_user_profile_link() {
            if (!$this->user)
                return FALSE;
            $link = 'https://www.facebook.com' . $this->edd_slg_get_fb_user();

            return apply_filters('edd_slg_get_user_profile_link', $link);
        }

        /**
         * Check Application Permission
         * 
         * Handles to check facebook application
         * permission is given by user or not
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.0.0
         */
        public function edd_slg_check_fb_app_permission($perm = '') {
            $facebook = $this->edd_slg_initialize_facebook();
            if (!$this->facebook)
                return FALSE;
            $data = 0;

            if (!empty($perm)) {
                $accessToken = json_decode($this->getAnonymousAccessToken());
                $userID = $this->edd_slg_get_fb_user();
                try {
                    // Returns a `Facebook\FacebookResponse` object
                    $response = $this->facebook->get('/' . $userID . '/permissions', $accessToken->access_token);
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }

                $permissions = $response->getDecodedBody();

                $permission_data = isset($permissions['data']) ? $permissions['data'] : array();

                if (!empty($permission_data)) {

                    foreach ($permission_data as $permission_field) {

                        $field_name = isset($permission_field['permission']) ? $permission_field['permission'] : '';
                        $field_status = isset($permission_field['status']) ? $permission_field['status'] : '';

                        if ($field_name == 'email' && $field_status == 'granted') {
                            $data = 1;
                            break;
                        }
                    }
                }
            }

            return apply_filters('edd_slg_check_fb_app_permission', $data);
        }

        /**
         * User Image
         * 
         * Getting the the profile image of the connected Facebook user.
         * 
         * @package Easy Digital Downloads - Social Login
         * @since 1.0.0
         */
        public function edd_slg_fb_get_profile_picture($args = array(), $user) {

            if (isset($args['type']) && !empty($args['type'])) {
                $type = $args['type'];
            } else {
                $type = 'normal';
            }

            $type = apply_filters('edd_slg_fb_profile_picture_type', $type, $user);
            $url = 'https://graph.facebook.com/' . $user . '/picture?type=' . $type;

            return apply_filters('edd_slg_fb_get_profile_picture', $url);
        }

    }

}