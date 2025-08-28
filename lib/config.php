<?php
// ===============================================
// Change this
// ===============================================

$sub_folder_name = 'ads';
$site_name = 'ADS-Server';
$delete_warning = 'Are you sure you want to delete? If you do not, click cancel.';
$item_per_page = 6;
$app_secret_key = "kj9djd76278Hkl3jsg4js";

// =========================================

// =========================================
// Don't change this
// =========================================
$encryption_method = 'AES-256-CBC';

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = "https://";
} else {
    $protocol = "http://";
}

$siteHost = $_SERVER['HTTP_HOST'];

if ($sub_folder_name == '') {
    $siteDomain = $protocol . $siteHost;
} else {
    $siteDomain = $protocol . $siteHost . '/' . $sub_folder_name;
}


if (!defined('SITE_URL')) {
    define('SITE_URL', $siteDomain);
}
if (!defined('SITE_PATH')) {
    define('SITE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/' . $sub_folder_name);
}
if (!defined('SITE_NAME')) {
    define('SITE_NAME', $site_name);
}
if (!defined('DELETE_WARNING')) {
    define('DELETE_WARNING', $delete_warning);
}
if (!defined('TOTAL_ITEM_PER_PAGE')) {
    define('TOTAL_ITEM_PER_PAGE', $item_per_page);
}
if (!defined('ENCRYPTION_KEY')) {
    define('ENCRYPTION_KEY', $app_secret_key);
}
if (!defined('ENCRYPTION_METHOD')) {
    define('ENCRYPTION_METHOD', $encryption_method);
}

// =========================================
