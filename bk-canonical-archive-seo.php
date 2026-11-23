<?php
/**
 * Plugin Name: BıçakKurdu - Canonical ve Arşiv Metadata Düzeltmeleri
 * Description: İki eski incelemenin tercih edilen temiz URL'sini kullanır; eksik arşiv canonical ve genel meta açıklamalarını hedefli olarak düzeltir.
 * Version: 1.0.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

function bk_seo_review_url_by_id($id) {
    static $review_urls = array(
        2132 => '/bicak-incelemeleri/kershaw-livewire-tanto-incelemesi/',
        2123 => '/bicak-incelemeleri/crkt-5500-kiss-tasarim-incelemesi/',
    );
    return isset($review_urls[(int)$id]) ? home_url($review_urls[(int)$id]) : null;
}
add_filter('post_type_link', function($url, $post) {
    if (!($post instanceof WP_Post) || $post->post_type !== 'inceleme') return $url;
    return bk_seo_review_url_by_id($post->ID) ?: $url;
}, PHP_INT_MAX, 2);
add_filter('get_canonical_url', function($url, $post) {
    if (!($post instanceof WP_Post) || $post->post_type !== 'inceleme') return $url;
    return bk_seo_review_url_by_id($post->ID) ?: $url;
}, PHP_INT_MAX, 2);

function bk_seo_archive_page_info() {
    $path = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!is_string($path)) return null;
    $path = '/' . trim($path, '/') . '/';
    $manual = array(
        '/bicak-celikleri/' => array(
            'Bıçak Çelikleri Sözlüğü ve Karşılaştırmaları | Bıçak Kurdu',
            'D2, N690, 14C28N, M390 ve MagnaCut gibi bıçak çeliklerinin sertlik, tokluk, korozyon direnci ve bileme özelliklerini karşılaştırın.',
        ),
        '/rehberler/' => array(
            'Bıçak, Çakı ve Outdoor Rehberleri | Bıçak Kurdu',
            'Bıçak ve çakı seçimi, güvenli muhafaza, teknik bilgiler, kamp ve bakım konularındaki kapsamlı rehberleri bir arada keşfedin.',
        ),
        '/celik-karsilastirma/' => array(
            'Bıçak Çelikleri Karşılaştırma Tablosu | Bıçak Kurdu',
            'Bıçak çeliklerini sertlik, korozyon direnci, tokluk, ağız tutma ve bakım kriterlerine göre karşılaştırma tablosunda inceleyin.',
        ),
    );
    if (isset($manual[$path])) {
        return array('canonical' => home_url($path), 'title' => $manual[$path][0], 'description' => $manual[$path][1]);
    }
    if (is_tax(array('bicak_kategori', 'bk_celik_grubu', 'bk_rehber_konu'))) {
        $term = get_queried_object();
        if (!($term instanceof WP_Term) || (int)$term->count < 1) return null;
        $link = get_term_link($term);
        if (is_wp_error($link) || !is_string($link)) return null;
        $term_path = wp_parse_url($link, PHP_URL_PATH);
        if (untrailingslashit($term_path) !== untrailingslashit($path)) return null;
        return array('canonical' => $link);
    }
    return null;
}

function bk_seo_head_repair($html) {
    if (!is_string($html) || stripos($html, '</head>') === false) return $html;
    return preg_replace_callback('~<head\b[^>]*>[\s\S]*?</head>~iu', function($match) {
        $head = $match[0];
        $item = get_queried_object();
        $review_url = ($item instanceof WP_Post && is_singular('inceleme')) ? bk_seo_review_url_by_id($item->ID) : null;
        if ($review_url) {
            $before = array(
                'https://bicakkurdu.com/2026/10/24/kershaw-livewire-tanto-incelemesi/',
                'https://bicakkurdu.com/2026/10/23/crkt-5500-kiss-tasarim-incelemesi/',
            );
            $head = str_replace($before, array(
                home_url('/bicak-incelemeleri/kershaw-livewire-tanto-incelemesi/'),
                home_url('/bicak-incelemeleri/crkt-5500-kiss-tasarim-incelemesi/'),
            ), $head);
            return $head;
        }
        $details = bk_seo_archive_page_info();
        if (!$details) return $head;
        if (!empty($details['title'])) {
            $title = '<title>' . esc_html($details['title']) . '</title>';
            $head = preg_replace('~<title\b[^>]*>[\s\S]*?</title>~iu', $title, $head, 1);
        }
        if (!empty($details['description'])) {
            $description = esc_attr($details['description']);
            $found = 0;
            $head = preg_replace_callback('~<meta\b[^>]*>~iu', function($t) use ($description, &$found) {
                if (preg_match('~\bname\s*=\s*["\x27]description["\x27]~iu', $t[0])) {
                    $found++;
                    return '<meta name="description" content="' . $description . '">';
                }
                return $t[0];
            }, $head);
            if (!$found) {
                $head = str_ireplace('</head>', '<meta name="description" content="' . $description . '">' . "\n</head>", $head);
            }
        }
        if (!empty($details['canonical']) &&
            !preg_match('~<link\b[^>]*\brel\s*=\s*["\x27]canonical["\x27][^>]*>~iu', $head)) {
            $canonical = '<link rel="canonical" href="' . esc_url($details['canonical']) . '">';
            $head = str_ireplace('</head>', $canonical . "\n</head>", $head);
        }
        return $head;
    }, $html, 1);
}
add_action('template_redirect', function() {
    if (is_admin() || wp_doing_ajax() || is_feed() || is_404() ||
        (defined('REST_REQUEST') && REST_REQUEST)) return;
    $request_method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($request_method !== 'GET' && $request_method !== 'HEAD') return;
    $post = get_queried_object();
    if (($post instanceof WP_Post && is_singular('inceleme') && bk_seo_review_url_by_id($post->ID)) ||
        bk_seo_archive_page_info()) {
        ob_start('bk_seo_head_repair');
    }
}, 2);
