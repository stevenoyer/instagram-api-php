# instagram-api-php

## Installation
Include the InstaAPI.php file in your project
```php
require 'InstaAPI.php';
````

## Documentation

Some useful links from the Facebook documentation :
- [Getting started guide on creating the app and exchanging the code for a token](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started#)
- [Guide to Retrieving User Information](https://developers.facebook.com/docs/instagram-basic-display-api/reference/user)
- [Get user profiles and user media](https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-profiles-and-media)
- [Data recovery from media](https://developers.facebook.com/docs/instagram-basic-display-api/reference/media)
- [Long-lived access tokens](https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens)
- [Overview of Instagram's Basic Display API](https://developers.facebook.com/docs/instagram-basic-display-api/overview)
- [User media content](https://developers.facebook.com/docs/instagram-basic-display-api/reference/user/media)
- [Meta for developers](https://developers.facebook.com/)

Start by creating a basic Facebook app and include in "Instagram basic display" products, then configure the settings. You will find the documentation [here](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started)

For Authentication URL you can use this one => **https://api.stevenoyer.fr/auth**

For the proper functioning of the application, bring this information :
- Instagram App ID
- Instagram App Secret
- Valid OAuth redirect URIs (for example : https://api.stevenoyer.fr/auth)

Instantiate the PHP class and paste info.

```php
$insta_api = new InstaApi('Your APP ID', 'Your APP SECRET', 'Your REDIRECT URL');
```

Now we will indicate an instagram user id.

```php
// Set user id or me 
$insta_api->setUserId('me');
```

To retrieve the id of a user use this url :
[https://www.instagram.com/steven_oyer/?__a=1](https://www.instagram.com/steven_oyer/?__a=1)

Replace 'steven_oyer' with whatever username you want.
Then retrieve the user id in the JSON code.

Then we will call a function to authenticate ourselves on instagram and retrieve a special code to have a short-lived token.

```php
echo '<a href="' . $insta_api->getUserAuthUrl() . '" target="_blank">Get code for access token</a>'
```

Click on the link and get the code in the URL.

We will use a function to exchange the code we have retrieved by a short-lived token.
The short-lived token is valid for 1 hour.

```php
$exchange = $insta_api->exchangeCodeInShortToken('PASTE YOUR CODE');

var_dump($exchange);
```

Retrieve the new token and paste it into the function that will transform the short-lived token into a long-lived one.
The long-lived token is valid for 60 days.

```php
$long_token = $insta_api->longAccessToken('PASTE SHORT-LIVED TOKEN');

var_dump($long_token);
````

A function has been created to exchange a long-lived token that is still valid for another. Use it before the long-lived token expires.

```php
$refresh_token = $insta_api->refreshLongToken('PASTE OLD LONG-TOKEN');

var_dump($refresh_token);
```

### To retrieve user data, here are some examples :

```php
$user = $insta_api->getUserInfo('PASTE LONG-TOKEN');

var_dump($user);
```

```php
$user_media = $insta_api->getUserMedias('PASTE LONG-TOKEN');

var_dump($user_media);
```

```php
$get_media_by_id = $insta_api->getMediaInfo('PASTE MEDIA ID', false, 'PASTE LONG-TOKEN');

var_dump($get_media_by_id);
```

```php
$get_media_children_by_id = $insta_api->getMediaInfo('PASTE MEDIA ID', true, 'PASTE LONG-TOKEN');

var_dump($get_media_children_by_id);
```

I advise you to store your long-term token in a database and to change the token using the ``$insta_api->refreshLongToken()`` function every 45 days with a cron task or other. You are free to choose.

The code has been commented with links to facebook documentation.

