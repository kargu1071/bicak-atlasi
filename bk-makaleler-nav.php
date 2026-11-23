<?php
/**
 * Plugin Name: BıçakKurdu - Üst Menü Makaleler
 * Description: Mevcut tema üst menüsünde Bileme bağlantısını Makaleler ve alt kategorileriyle değiştirir. URL yönlendirmelerine dokunmaz.
 * Version: 1.0.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

function bk_makaleler_nav_items() {
    return array(
        array('Bıçak ve Çakı Makaleleri', '/category/makaleler/bicak-ve-caki-makaleleri/', 0),
        array('Havalı Tüfek ve BB Tabanca', '/category/makaleler/havali-tufek-ve-bb-tabanca/', 0),
        array('Nasıl Yapılır', '/category/makaleler/nasil-yapilir/', 0),
        array('Bileme', '/category/makaleler/nasil-yapilir/bileme/', 1),
        array('Maceralar', '/category/makaleler/maceralar/', 0),
        array('Tarih ve Hikayeler', '/category/makaleler/tarih-ve-hikayeler/', 0),
        array('Kamp', '/category/makaleler/kamp/', 0),
        array('Balıkçılık', '/category/makaleler/balikcilik/', 0),
    );
}

function bk_makaleler_nav_submenu() {
    $html = '<span class="bk-makaleler-dropdown" id="bk-makaleler-submenu">';
    $html .= '<a class="bk-makaleler-all" href="' . esc_url(home_url('/makaleler/')) . '">Tüm Makaleler</a>';
    foreach (bk_makaleler_nav_items() as $item) {
        $class = $item[2] ? 'bk-makaleler-child' : '';
        $html .= '<a class="' . $class . '" href="' . esc_url(home_url($item[1])) . '">';
        $html .= ($item[2] ? '↳ ' : '') . esc_html($item[0]) . '</a>';
    }
    return $html . '</span>';
}

function bk_makaleler_nav_replace_in_segment($segment, &$changed) {
    return preg_replace_callback('~<a\b[^>]*>.*?</a>~isu', function ($matches) use (&$changed) {
        $anchor = $matches[0];
        if (stripos(wp_strip_all_tags($anchor), 'Bileme') === false) {
            return $anchor;
        }
        if (!preg_match('~\bhref\s*=\s*(["\'])[^"\']*bileme[^"\']*\1~iu', $anchor)) {
            return $anchor;
        }
        $link = preg_replace('~\bhref\s*=\s*(["\'])[^"\']*\1~iu', 'href="' . esc_url(home_url('/makaleler/')) . '"', $anchor, 1);
        $link = preg_replace('~(>\s*)Bileme(\s*</)~u', '$1Makaleler$2', $link, 1, $label_changed);
        if (!$label_changed) {
            $link = preg_replace('~Bileme~u', 'Makaleler', $link, 1);
        }
        $changed++;
        return '<span class="bk-makaleler-menu">' . $link .
            '<button type="button" class="bk-makaleler-toggle" aria-label="Makale alt kategorilerini aç" aria-expanded="false" aria-controls="bk-makaleler-submenu">▾</button>' .
            bk_makaleler_nav_submenu() . '</span>';
    }, $segment);
}

function bk_makaleler_nav_patch_output($html) {
    if (!is_string($html) || stripos($html, 'Bileme') === false || stripos($html, '/makaleler/') === false && stripos($html, 'bileme') === false) {
        return $html;
    }
    $changed = 0;
    $start = stripos($html, '<header');
    $end = ($start !== false) ? stripos($html, '</header>', $start) : false;
    if ($start !== false && $end !== false) {
        $end += strlen('</header>');
        $segment = substr($html, $start, $end - $start);
        $patched = bk_makaleler_nav_replace_in_segment($segment, $changed);
        $html = substr($html, 0, $start) . $patched . substr($html, $end);
    }
    if (!$changed) {
        // Fallback for classic themes that use a div rather than a header tag.
        $limit = stripos($html, '<main');
        $limit = $limit === false ? min(strlen($html), 60000) : min($limit, 60000);
        $leading = substr($html, 0, $limit);
        $patched = bk_makaleler_nav_replace_in_segment($leading, $changed);
        $html = $patched . substr($html, $limit);
    }
    return $html;
}

add_action('template_redirect', function () {
    if (is_admin() || is_feed() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return;
    if (get_stylesheet() !== 'bicakkurdu-premium') return;
    ob_start('bk_makaleler_nav_patch_output');
}, 0);

add_action('wp_head', function () {
    if (get_stylesheet() !== 'bicakkurdu-premium') return;
    echo '<style id="bk-makaleler-header-style">
.bk-makaleler-menu{position:relative;display:inline-flex;align-items:center;min-width:0;vertical-align:middle}
.bk-makaleler-menu>a{display:inline-flex;align-items:center;gap:5px}
.bk-makaleler-dropdown{display:none;position:absolute;top:100%;right:0;width:min(320px,90vw);max-height:70vh;overflow-y:auto;background:#151a1c;color:#fff;padding:9px;border:1px solid #404448;border-radius:10px;box-shadow:0 14px 32px rgba(0,0,0,.4);z-index:99999;text-align:left}
.bk-makaleler-menu:hover .bk-makaleler-dropdown,.bk-makaleler-menu:focus-within .bk-makaleler-dropdown,.bk-makaleler-menu.bk-makaleler-open .bk-makaleler-dropdown{display:block}
.bk-makaleler-dropdown>a{display:block!important;padding:9px 11px!important;line-height:1.35!important;text-decoration:none!important;font-size:13px!important;font-weight:700!important;white-space:normal!important;color:#f7f7f7!important;background:transparent!important;border:0!important;border-bottom:1px solid #343a3d!important}
.bk-makaleler-dropdown>a:hover,.bk-makaleler-dropdown>a:focus{color:#ffbf16!important;background:#242a2d!important}
.bk-makaleler-dropdown>a:last-child{border-bottom:0!important}
.bk-makaleler-dropdown .bk-makaleler-all{color:#ffbf16!important;font-weight:900!important}
.bk-makaleler-dropdown .bk-makaleler-child{padding-left:27px!important;color:#d5d8db!important}
.bk-makaleler-toggle{border:0;cursor:pointer;background:transparent;color:#ffbf16;font-size:17px;padding:4px;line-height:1;display:none}
@media (max-width:1000px){.bk-makaleler-menu{display:flex;flex-wrap:wrap;align-items:center}.bk-makaleler-toggle{display:inline-block}.bk-makaleler-dropdown{top:100%;left:0;right:auto;min-width:240px;width:min(320px,88vw);max-height:55vh}}
</style>';
}, 99);

add_action('wp_footer', function () {
    if (get_stylesheet() !== 'bicakkurdu-premium') return;
    echo '<script id="bk-makaleler-toggle-js">(function(){document.addEventListener("click",function(e){var btn=e.target.closest(".bk-makaleler-toggle");if(!btn)return; e.preventDefault();var wrap=btn.closest(".bk-makaleler-menu");if(!wrap)return;var open=wrap.classList.toggle("bk-makaleler-open");btn.setAttribute("aria-expanded",open?"true":"false");btn.setAttribute("aria-label",open?"Makale alt kategorilerini kapat":"Makale alt kategorilerini aç");});})();</script>';
}, 99);
