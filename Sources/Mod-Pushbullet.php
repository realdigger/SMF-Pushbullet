<?php
/**
 * @package SMF Pushbullet Notifications
 * @file Mod-Pushbullet.php
 * @author digger <digger@mysmf.net> <https://mysmf.net>
 * @copyright Copyright (c) 2017-2022, digger
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0
 */

if (!defined('SMF')) {
    die('Hacking attempt...');
}

/**
 * Load all needed hooks
 */
function loadPushbulletHooks()
{
    add_integration_function('integrate_load_theme', 'loadPushbulletAssets', false);

    // Admin area
    add_integration_function('integrate_admin_include', '$sourcedir/Admin-Pushbullet.php', false);
    add_integration_function('integrate_admin_areas', 'addPushbulletAdminArea', false);
    add_integration_function('integrate_modify_modifications', 'addPushbulletAdminAction', false);
}


/**
 * Send request to Pushbullet API
 * @param string $type
 * @param array $fields
 * @return bool|mixed
 */
function requestPushbulletApi($type = 'test', $fields = array())
{
    global $modSettings;

    if (empty($modSettings['pushbullet_api_key']) || empty($type) || !function_exists('curl_init') || !function_exists('json_decode')) {
        return false;
    }

    $curl_defaults = array(
        CURLOPT_HTTPHEADER => array(
            'Access-Token: ' . $modSettings['pushbullet_api_key'],
            'Content-Type: application/json'
        ),
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 3,
    );

    switch ($type) {
        case 'test':
            $api_url = 'https://api.pushbullet.com/v2/users/me';
            $curl_options = array();
            break;
        case 'push':
            $api_url = 'https://api.pushbullet.com/v2/pushes';
            $curl_options = array(
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => json_encode($fields),
            );
            break;
    }

    $curl = curl_init($api_url);
    curl_setopt_array($curl, ($curl_defaults + $curl_options));

    if (!$result = curl_exec($curl)) {
        log_error('[Pushbullet] ' . curl_error($curl));
    }

    curl_close($curl);
    return json_decode($result);
}

function sendPuushbulletNotification()
{

}