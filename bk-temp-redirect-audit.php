<?php
/**
 * Plugin Name: BıçakKurdu - Geçici Yönlendirme Kural Denetimi
 * Description: Yalnızca bir kez yönlendirme eklentilerini yöneticiye özel taslak rapora alır. Ziyaretçi sayfalarını değiştirmez.
 * Version: 1.0.1
 */
defined('ABSPATH') || exit;
register_activation_hook(__FILE__, function () {
    // Runs only on installation/activation by the WordPress hosting administrator.
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $pick = array(
        'bk-four-redirects/',
        'bicakkurdu-six-category-301-exemption/',
        'bicakkurdu-dogrulanmis-301/',
        'bicakkurdu-eski-link-301/',
        'bicakkurdu-gsc-404-301/',
        'bicakkurdu-404-konuya-gore-301/',
        'bicakkurdu-arsiv-hiyerarsi-duzeltmeleri/',
    );
    $rows = array();
    foreach (get_plugins() as $file => $info) {
        $selected = false;
        foreach ($pick as $prefix) if (strpos($file, $prefix) === 0) $selected = true;
        if (!$selected) continue;
        $real = WP_PLUGIN_DIR . '/' . $file;
        $content = is_readable($real) ? file_get_contents($real) : '';
        $rows[] = array('file' => $file, 'name' => $info['Name'] ?? '', 'bytes' => strlen((string)$content), 'content_b64' => base64_encode((string)$content));
    }
    wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'draft',
        'post_title' => 'TEMP - Redirect Plugin Rules Audit (Delete After)',
        'post_name' => 'bk-temp-plugin-redirect-audit-20261123',
        'post_content' => base64_encode(wp_json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        'comment_status' => 'closed',
    ));
});
