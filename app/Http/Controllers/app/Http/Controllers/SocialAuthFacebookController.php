<?php

namespace App\Http\Controllers;

use Facebook\Facebook;
use App\Services\SocialFacebookAccountService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Facebook\Exceptions\FacebookSDKException;
use Session;

class SocialAuthFacebookController extends Controller
{

    /**
     * Create a redirect method to facebook api.
     *
     * @return void
     */
    public function redirect()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['manage_pages', 'publish_pages']; // Optional permissions
        $loginUrl = $helper->getLoginUrl(env('FACEBOOK_REDIRECT'), $permissions);

        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';

    }

    /**
     * Return a callback method from facebook api.
     * @return callback URL from facebook
     */
    public function callback()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }
        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }
        }

        // save pages token to session
        $pages = SocialFacebookAccountService::getFanPages($accessToken);
        session(['adminPages' => $pages]);
        // get fb email
        $user = SocialFacebookAccountService::getEmail($accessToken);
        // create new user with that email
        $modelUser = User::createOrGetByFbId($user['id'], $user['name'], $accessToken);
        // login user with that email
        Auth::login($modelUser, true);

        // redirect
        return redirect('/home');
    }

}
