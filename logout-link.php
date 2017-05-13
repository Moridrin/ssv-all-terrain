<?php

use mp_ssv_users\SSV_Users;

/**
 * @param string $items
 * @param mixed  $args
 *
 * @return string
 */
function mp_ssv_logout_menu_link(
    $items,
    /** @noinspection PhpUnusedParameterInspection */
    $args
) {
    if (strpos($items, 'href="[logout]"')
        || strpos($items, 'href="http://[logout]"')
        || strpos($items, 'href="https://[logout]"')
    ) {
        if (\mp_ssv_general\SSV_General::usersPluginActive()) {
            $loginPage = SSV_Users::getPagesWithTag(SSV_Users::TAG_LOGIN_FIELDS)[0];
            $url       = get_permalink($loginPage) . '?logout=success';
        } else {
            $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?logout=success';
        }
        $items = str_replace('href="https://[logout]"', 'href="' . wp_logout_url($url) . '"', $items);
        $items = str_replace('href="http://[logout]"', 'href="' . wp_logout_url($url) . '"', $items);
        $items = str_replace('href="[logout]"', 'href="' . wp_logout_url($url) . '"', $items);
    }
    return $items;
}

add_filter('wp_nav_menu_items', 'mp_ssv_logout_menu_link', 10, 2);
