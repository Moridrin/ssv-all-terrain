<?php
/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 10-4-17
 * Time: 10:35
 */
use mp_ssv_all_terrain\SSV_DirectDebitPDF;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;
use mp_ssv_users\SSV_Users;

/**
 * This method adds the custom Meta Boxes
 */
function mp_ssv_all_terrain_meta_boxes()
{
    global $post;
    if (!$post || !SSV_General::usersPluginActive()) {
        return;
    }
    $containsDirectDebitTag = strpos($post->post_content, '[ssv-users-direct-debit-pdf]') !== false;
    if ($containsDirectDebitTag) {
        add_meta_box('ssv_users_page_role', 'Page Role', 'ssv_users_page_role', 'page', 'side', 'default');
    }
}

add_action('add_meta_boxes', 'mp_ssv_all_terrain_meta_boxes');

function mp_ssv_all_terrain_custom_users_row_actions($actions, $user_object)
{
    /** @var WP_Post[] $pages */
    $pages = SSV_Users::getPagesWithTag('[ssv-users-direct-debit-pdf]');
    if (SSV_General::usersPluginActive()) {
        foreach ($pages as $page) {
            $pageRole = get_post_meta($page->ID, SSV_Users::PAGE_ROLE_META, true);
            if (in_array($pageRole, $user_object->roles)) {
                $url                         = get_permalink($page) . '?member=' . $user_object->ID;
                $actions['direct_debit_pdf'] = '<a href="' . esc_url($url) . '" target="_blank">PDF</a>';
            } elseif ($pageRole == -1) {
                $url                         = get_permalink($page) . '?member=' . $user_object->ID;
                $actions['direct_debit_pdf'] = '<a href="' . esc_url($url) . '" target="_blank">PDF</a>';
            }
        }
    } else {
        $url                         = get_permalink($pages[0]) . '?member=' . $user_object->ID;
        $actions['direct_debit_pdf'] = '<a href="' . esc_url($url) . '" target="_blank">PDF</a>';
    }
    return $actions;
}

add_filter('user_row_actions', 'mp_ssv_all_terrain_custom_users_row_actions', 10, 3);

require_once('include/fpdf/SSV_DirectDebitPDF.php');

function mp_ssv_all_terrain_pdf_content($content)
{
    if (strpos($content, '[ssv-users-direct-debit-pdf]') === false) {
        return $content;
    }
    $user = null;
    if (isset($_GET['member']) && current_user_can('edit_users')) {
        $user = User::getByID($_GET['member']);
    }
    if ($user == null) {
        $user = User::getCurrent();
    }
    if ($user == null) {
        SSV_General::redirect(SSV_General::getLoginURL());
        return $content;
    }

    $first_name      = $user->getMeta('first_name');
    $initials        = $user->getMeta('initials');
    $last_name       = $user->getMeta('last_name');
    $gender          = $user->getMeta('gender');
    $iban            = $user->getMeta('iban');
    $date_of_birth   = $user->getMeta('date_of_birth');
    $street          = $user->getMeta('address_street');
    $email           = $user->getMeta('email');
    $postal_code     = $user->getMeta('address_postal_code');
    $city            = $user->getMeta('address_city');
    $phone_number    = $user->getMeta('phone_number');
    $emergency_phone = $user->getMeta('emergency_phone');

    $pdf = new SSV_DirectDebitPDF();
    $pdf->build(
        $first_name,
        $initials,
        $last_name,
        $gender,
        $iban,
        $date_of_birth,
        $street,
        $email,
        $postal_code,
        $city,
        $phone_number,
        $emergency_phone
    );
    $pdf->Output('I');
    return $content;
}

add_filter('the_content', 'mp_ssv_all_terrain_pdf_content');
