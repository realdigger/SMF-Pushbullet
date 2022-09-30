<?php
/**
 * @package SMF Pushbullet Notifications
 * @file Admin-Pushbullet.php
 * @author digger <digger@mysmf.net> <https://mysmf.net>
 * @copyright Copyright (c) 2017, digger
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0
 */

if (!defined('SMF')) {
    die('Hacking attempt...');
}

// TODO: check for curl installed

/**
 * Add mod admin action
 * @param $subActions
 */
function addPushbulletAdminAction(&$subActions)
{
    $subActions['pushbullet'] = 'addPushbulletAdminSettings';
}

/**
 * Add mod admin area
 * @param $admin_areas
 */
function addPushbulletAdminArea(&$admin_areas)
{
    global $txt;
    loadLanguage('Pushbullet/Pushbullet');

    $admin_areas['config']['areas']['modsettings']['subsections']['pushbullet'] = array($txt['pushbullet']);
}

/**
 * Add mod settings area
 * @param bool $return_config
 * @return array
 */
function addPushbulletAdminSettings($return_config = false)
{
    global $txt, $scripturl, $context;
    loadLanguage('Pushbullet/Pushbullet');

    $context['page_title'] = $context['settings_title'] = $txt['pushbullet'];
    $context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=pushbullet';
    $context['settings_message'] = $txt['pushbullet_description'];

    $pushbullet_disabled = false;

    if (function_exists('curl_init')) {
        $curl_enabled = true;
    } else {
        $pushbullet_disabled = true;
    }

    if (function_exists('json_decode')) {
        $json_enabled = true;
    } else {
        $pushbullet_disabled = true;
    }

    $config_vars = array(
        empty($curl_enabled) ? array('warning', 'pushbullet_nocurl') : array(),
        empty($json_enabled) ? array('warning', 'pushbullet_nojson') : array(),
        array('check', 'pushbullet_enabled', 'disabled' => $pushbullet_disabled),
        array('text', 'pushbullet_api_key', 'subtext' => $txt['pushbullet_api_key_help']),
        empty(requestPushbulletApi()->active) ? array('warning', 'pushbullet_test_false') : array(
            'message',
            'pushbullet_test_ok',
        ),

    );

    if ($return_config) {
        return $config_vars;
    }

    if (isset($_GET['save'])) {
        checkSession();
        saveDBSettings($config_vars);
        redirectexit('action=admin;area=modsettings;sa=pushbullet');
    }

    prepareDBSettingContext($config_vars);
}
