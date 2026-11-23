<?php
/**
 * Plugin Name: BıçakKurdu - 404 İçerik Eşleştirmeleri
 * Description: Doğrulanan eski 404 sayfalarını yalnızca birebir ilgili, canlı sayfalara kalıcı yönlendirir. Çalışan sayfalara, category arşivlerine ve var olan yönlendirmelere dokunmaz.
 * Version: 1.0.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

add_action('template_redirect', function () {
    // Yalnızca gerçek 404 cevaplarında çalışır.
    if (is_admin() || wp_doing_ajax() || !is_404() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    $raw_path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
    if (!is_string($raw_path) || $raw_path === '') {
        return;
    }
    $path = '/' . trim(rawurldecode($raw_path), '/');
    $map = array(
        '/2026/10/24/spyderco-tenacious-incelemesi' => '/bicak-incelemeleri/spyderco-tenacious-c122gp-incelemesi/',
        '/contact' => '/iletisim/',
        '/contact-us' => '/iletisim/',
    );
    if (!array_key_exists($path, $map)) {
        return;
    }
    wp_safe_redirect(home_url($map[$path]), 301, 'BicakKurdu-404-Esleme');
    exit;
}, 20);
