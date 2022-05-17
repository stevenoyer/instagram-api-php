<?php 

/**
 * Instagram API with Facebook Developer
 * @author Steven Oyer <contact@stevenoyer.fr>
 * @version 1.0.0
 * @copyright © 2022
 */
class InstaApi
{
    
    /**
     * Constructor class
     * @param String $app_id Facebook APP ID
     * @param String $app_secret Facebook APP Secret
     * @param String $access_token User access token
     * @param String $redirect_uri Redirect URI
     */
    public function __construct(String $app_id, String $app_secret, String $redirect_uri)
    {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->redirect_uri = $redirect_uri;
        $this->insta_url = 'https://www.instagram.com/';
    }

    /**
     * Function that inserts a user id
     * 
     * @param String $ig_userid Instagram UserID
     * @return String return userID
     */
    public function setUserId(String $ig_userid)
    {
        if (!$ig_userid)
        {
            $ig_userid = 'me';
        }

        return $this->userid = $ig_userid;
    }

    /**
     * Function that exchanges a short-lived token for a long-lived token
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens#refresh-a-long-lived-token
     * 
     * @param String $short_token
     * @return String return long token
     */
    public function longAccessToken(String $short_token)
    {
        if (empty($short_token))
        {
            die('Please specify the short token.');
        }

        $params = [
            'grant_type' => 'ig_exchange_token',
            'client_secret' => $this->app_secret,
            'access_token' => $short_token
        ];

        $url = 'https://graph.instagram.com/access_token?' . http_build_query($params, '', '&');

        $headers = [
            'Content-Type: application/json'
        ];

        return $this->getCurl($url, [], false, $headers);
    }

    /**
     * Function that generates a URL to authenticate and retrieve an authorization code that will then allow us to obtain a token
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/getting-started#uri-de-redirection-oauth-valides
     * 
     * @return String return URL for auth insta and get code
     */
    public function getUserAuthUrl()
    {
        
        $params = [
            'client_id' => $this->app_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => 'user_profile,user_media',
            'response_type' => 'code'
        ];

        return 'https://api.instagram.com/oauth/authorize?' . http_build_query($params, '', '&');
    }

    /**
     * Function that exchanges an authorization code for a short-lived token
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/getting-started#-tape-5----changer-le-code-contre-un-token
     * 
     * @param String $code
     * @return Object return a Object response with the access token and user id
     */
    public function exchangeCodeInShortToken(String $code = '')
    {
        if (empty($code))
        {
            die('Please specify your code');
        }

        $url = 'https://api.instagram.com/oauth/access_token';

        $params = [
            'client_id' => $this->app_id,
            'client_secret' => $this->app_secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_uri,
            'code' => $code
        ];

        return $this->getCurl($url, $params, true);
    }

    /**
     * Function that exchanges a long-lived token for another
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens#refresh-a-long-lived-token
     * 
     * @param String $long_token
     * @return Object
     */
    public function refreshLongToken(String $long_token)
    {

        if (empty($long_token))
        {
            die('Please specify a long-lived token');
        }

        $params = [
            'grant_type' => 'ig_refresh_token',
            'access_token' => $long_token
        ];

        $url = 'https://graph.instagram.com/refresh_access_token?' . http_build_query($params, '', '&');
        
        $headers = [
            'Content-Type: application/json'
        ];
        
        return $this->getCurl($url, [], false, $headers);
    }


    /**
     * Function that allows you to make requests with curl
     * 
     * @link doc => https://www.php.net/manual/fr/ref.curl.php
     * 
     * @param String $url Retrieve data via url
     * @param Array $params Parameters for sending data
     * @param bool $curl_post If the method is post
     * @param Array $headers Specify what type of header
     * @return Object return result with curl
     */
    public function getCurl(String $url, Array $params = [], bool $curl_post = false, Array $headers = [])
    {
        $curl = curl_init($url);

        if ($curl_post)
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }

        if (!empty($headers))
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_res = curl_exec($curl);
        $res = json_decode($curl_res);

        curl_close($curl);

        return $res;
    }

    /**
     * Function that retrieves user information
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/reference/user#fields 
     * 
     * @param String $access_token
     * @return Object return result with curl
     */
    public function getUserInfo(String $access_token)
    {
        if (empty($access_token))
        {
            die('Please specify an access token');
        }

        $fields = [
            'id',
            'username',
            'account_type',
            'media_count',
            'media'
        ];

        $params = [
            'fields' => implode(',', $fields),
            'access_token' => $access_token
        ];

        $headers = [
            'Content-Type: application/json'
        ];

        $url = 'https://graph.instagram.com/' . $this->userid . '?' . http_build_query($params, '', '&');

        return $this->getCurl($url, [], false, $headers);
    }

    /**
     * Function that retrieves the user's media
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/reference/user/media#syntaxe-de-la-requ-te
     * 
     * @param String $access_token
     * @return Object return result with curl
     */
    public function getUserMedias(String $access_token)
    {

        if (empty($access_token))
        {
            die('Please specify an access token');
        }

        $fields = [
            'caption',
            'id',
            'media_type',
            'media_url',
            'permalink',
            'thumbnail_url',
            'timestamp',
            'username',
            'children'
        ];

        $params = [
            'fields' => implode(',', $fields),
            'access_token' => $access_token
        ];

        $headers = [
            'Content-Type: application/json'
        ];

        $url = 'https://graph.instagram.com/' . $this->userid . '/media?' . http_build_query($params, '', '&');

        return $this->getCurl($url, [], false, $headers);

    }

    /**
     * Function that retrieves information from a media in relation to its ID
     * 
     * @link doc => https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-profiles-and-media#obtenir-les-donn-es-de-m-dias
     * 
     * @param String $media_id
     * @param bool $childrenOnly Show only children
     * @param String $access_token
     * 
     * @return Object return result with curl
     */
    public function getMediaInfo(String $media_id, bool $childrenOnly = false, String $access_token)
    {
        if (empty($media_id))
        {
            die('Please specify a media id');
        }

        if (empty($access_token))
        {
            die('Please specify an access token');
        }

        $fields = [
            'id',
            'media_type',
            'media_url',
            'permalink',
            'thumbnail_url',
            'timestamp',
            'username',
            'children'
        ];

        $params = [
            'fields' => implode(',', $fields),
            'access_token' => $access_token
        ];

        $headers = [
            'Content-Type: application/json'
        ];

        $url = 'https://graph.instagram.com/' . $media_id . '?' . http_build_query($params);

        if ($childrenOnly)
        {
            $url = 'https://graph.instagram.com/' . $media_id . '/children?' . http_build_query($params);
        }

        return $this->getCurl($url, [], false, $headers);
    }
}
