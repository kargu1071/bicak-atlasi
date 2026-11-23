<?php
/**
 * Plugin Name: BıçakKurdu - Merkezi 301 Yönetimi
 * Description: Yeni Makaleler kategori URL'leri, eski inceleme kök adresi ve içerik odaklı kesin 301 eşleştirmeleri. Sadece tam eşleşen yol yönlendirilir; tekil inceleme URL'leri korunur.
 * Version: 1.0.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

/** Canonical map: only exact incoming paths, never a broad prefix catch-all. */
function bk_merged_redirect_map_20261123() {
    return array(
        '/bicak-incelemeleri' => '/incelemeler/',
        '/category/makaleler' => '/makaleler/',
        '/category/makaleler/bicak-ve-caki-makaleleri' => '/incelemeler/',
        '/category/makaleler/havali-tufek-ve-bb-tabanca' => '/havali-tufek-ve-bb-tabanca/',
        '/category/makaleler/nasil-yapilir' => '/nasil-yapilir/',
        '/category/makaleler/nasil-yapilir/bileme' => '/bileme/',
        '/category/makaleler/bileme' => '/bileme/',
        '/category/makaleler/maceralar' => '/maceralar/',
        '/category/makaleler/tarih-ve-hikayeler' => '/tarih-ve-hikayeler/',
        '/category/makaleler/kamp' => '/kamp/',
        '/category/makaleler/balikcilik' => '/balikcilik/',
        '/category/bicak-markalari' => '/markalar/',
        '/category/urun-incelemeleri' => '/incelemeler/',
        '/category/edc-cakilar' => '/cakilar/',
        '/category/edc-cakilar/page/2' => '/cakilar/',
        '/category/edc-cakilar/page/3' => '/cakilar/',
        '/category/kamp-ve-bushcraft' => '/kategori/kamp-bicaklari/',
        '/2026/10/23/bicak-kabze-malzemeleri-rehberi' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/bicak-agiz-formlari-rehberi' => '/bicak-agiz-formlari/',
        '/2026/10/23/full-tang-nedir-bicak-govde-yapilari' => '/full-tang-nedir/',
        '/2026/10/23/damascus-sam-celigi-nedir' => '/damascus-celigi-nedir/',
        '/2026/10/23/microtech-hera-incelemesi' => '/microtech-hera-incelemesi/',
        '/2026/10/24/ilk-kamp-bicagini-secerken-yapilan-5-yaygin-hata' => '/kamp-bicagi-secim-rehberi/',
        '/2026/10/24/ilk-kamp-bicaginda-7-secim-kriteri' => '/kamp-bicagi-secim-rehberi/',
        '/2026/10/23/bicak-guvenli-saklama-rehberi' => '/bicak-guvenli-saklama-rehberi/',
        '/2026/10/23/wharncliffe-nedir' => '/bicak-agiz-formlari/',
        '/2026/10/23/tanto-bicak-nedir' => '/bicak-agiz-formlari/',
        '/2026/10/23/drop-point-nedir' => '/bicak-agiz-formlari/',
        '/2026/10/23/bileme-tasi-grit-rehberi' => '/bileme/',
        '/2026/10/23/bicak-urun-incelemesi-nasil-okunur' => '/kaynak-ve-inceleme-metodolojisi/',
        '/2026/10/24/spyderco-tenacious-incelemesi' => '/bicak-incelemeleri/spyderco-tenacious-c122gp-incelemesi/',
        '/contact' => '/iletisim/',
        '/contact-us' => '/iletisim/',
    );
}
add_action('template_redirect', function() {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || is_feed()) {
        return;
    }
    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }
    $raw = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
    $path = wp_parse_url($raw, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return;
    }
    $path = '/' . trim(rawurldecode($path), '/');
    $destination = bk_merged_redirect_map_20261123()[$path] ?? null;
    if (!is_string($destination)) {
        return;
    }
    $url = home_url($destination);
    if (untrailingslashit($url) === untrailingslashit(home_url($path))) {
        return;
    }
    wp_safe_redirect($url, 301, 'BicakKurdu-Merkezi-301');
    exit;
}, -9999);
