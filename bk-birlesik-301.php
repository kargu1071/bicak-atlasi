<?php
/**
 * Plugin Name: BıçakKurdu - Birleşik 301 ve Arşiv Yönetimi
 * Description: Eski Makaleler kategori yolları, tarihli 404 yazılar, eski taksonomi alt yolları, yazar, rehber ve ek arşiv kurallarını tek noktadan yönetir. Canlı /kategori/ ana arşivleri korunur.
 * Version: 2.5.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

defined('ABSPATH') || exit;

/** Canonical map: only exact incoming paths, never a broad prefix catch-all. */
function bk_consolidated_exact_map_20261123() {
    return array(
        '/2026/10/24/kershaw-livewire-tanto-incelemesi' => '/bicak-incelemeleri/kershaw-livewire-tanto-incelemesi/',
        '/2026/10/23/crkt-5500-kiss-tasarim-incelemesi' => '/bicak-incelemeleri/crkt-5500-kiss-tasarim-incelemesi/',
        '/2026/10/23/bicak-kilifi-rehberi' => '/bicak-kilifi-rehberi/',
        '/2026/10/23/bicak-paslanmasi-korozyon-rehberi' => '/bicak-paslanmasi-korozyon-rehberi/',
        '/2026/10/23/bicak-olculeri-nasil-okunur' => '/bicak-olculeri-nasil-okunur/',
        '/2026/10/24/av-bicagi-secim-rehberi' => '/av-bicagi-secim-rehberi/',
        '/2026/10/24/sabit-bicak-secim-rehberi' => '/sabit-bicak-secim-rehberi/',
        '/2026/10/23/g10-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/ahsap-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/aluminyum-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/karbon-fiber-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/frn-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/paslanmaz-celik-kabze-nedir' => '/bicak-kabze-malzemeleri/',
        '/category/bicak-celikleri' => '/bicak-celikleri/',
        '/kategori/geleneksel-bicaklar' => '/geleneksel-bicaklar/',
        '/rehber-konulari/bakim-ve-bileme' => '/bileme/',
        '/rehber-konulari/bicak-celikleri-rehber' => '/bicak-celikleri/',
        '/rehber-konulari/kilit-ve-mekanizmalar' => '/kilit-sistemleri/',
        '/rehber-konulari/marka-rehberleri' => '/markalar/',
        '/author' => '/yazar/ozgur-firat/',
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
    $destination = bk_consolidated_exact_map_20261123()[$path] ?? null;
    // Old WordPress category pagination should resolve to the final clean hub.
    if (!is_string($destination) && strpos($path, '/category/') === 0 &&
        preg_match('#^(.*)/page/[1-9][0-9]*$#', $path, $page_match)) {
        $destination = bk_consolidated_exact_map_20261123()[$page_match[1]] ?? null;
    }
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


/** Previously separate verified, remaining-404 and obsolete sub-category redirects.
 * Trigger only for genuinely missing pages to preserve valid custom taxonomy archives. */
add_action('template_redirect', function() {
    if (is_admin() || !is_404() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return;
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET' && $method !== 'HEAD') return;
    $raw = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!is_string($raw)) return;
    $path = '/' . trim(rawurldecode($raw), '/') . '/';
    $map = array(
        '/2026/10/23/bicak-markasi-nasil-degerlendirilir/' => '/markalar/',
        '/2026/10/23/bicak-urun-incelemesi-nasil-okunur/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/2026/10/23/hrc-nedir-bicak-rockwell-sertligi/' => '/celik-puanlama-metodolojisi/',
        '/2026/10/24/50-dolar-alti-en-iyi-katlanir-cakilar/comment-page-1/' => '/cakilar/',
        '/2026/10/24/sabit-bicak-secim-rehberi/' => '/bicaklar/',
        '/2026/10/24/uygun-fiyatli-bicak-markalari/' => '/markalar/',
        '/author/gerberbicak/page/15/' => '/yazar/ozgur-firat/',
        '/author/gerberbicak/page/7/' => '/yazar/ozgur-firat/',
        '/author/gerberbicak/page/8/' => '/yazar/ozgur-firat/',
        '/bakim-bileme-guvenli-saklama/' => '/bileme/',
        '/benchmade-logo-transparent/' => '/markalar/benchmade/',
        '/bicak-celikleri-kapak-gorseli/' => '/bicak-celikleri/',
        '/bicak-kurdu-logo-transparent/' => '/',
        '/bicak-kurdu-yeni-logo/' => '/',
        '/bicak-markalari-kapak-gorseli/' => '/markalar/',
        '/buck-knives-logo/' => '/markalar/buck/',
        '/chris-reeve-knives-logo/' => '/markalar/chris-reeve-knives/',
        '/crkt-ceo-flipper-7097-webp/' => '/markalar/crkt/',
        '/crkt-m16-03ks/' => '/markalar/crkt/',
        '/crkt-m16-model/' => '/markalar/crkt/',
        '/crkt-minimalist-bowie/' => '/markalar/crkt/',
        '/crkt-pilar-iii-5317d2/' => '/markalar/crkt/',
        '/crkt-pilar-iii-karsilastirma/' => '/markalar/crkt/',
        '/crkt-squid-2490ks-jpg/' => '/markalar/crkt/',
        '/editor-secimi-cold-steel-bushman-outdoor/' => '/markalar/cold-steel/',
        '/editor-secimi-crkt-5500-edc-dogal/' => '/2026/10/23/crkt-5500-kiss-tasarim-incelemesi/',
        '/emerson-knives-logo-fixed/' => '/markalar/emerson/',
        '/extrema-ratio-logo/' => '/markalar/extrema-ratio/',
        '/frank-beltrame-logo-bicakkurdu/' => '/markalar/frank-beltrame/',
        '/gerber-logo-bicakkurdu/' => '/markalar/gerber/',
        '/haps-bugs-pm2-karbon-fp/' => '/markalar/haps-bugs/',
        '/hogue-logo-bicakkurdu/' => '/markalar/hogue/',
        '/ka-bar-dozier-kabze-detayi/' => '/markalar/ka-bar/',
        '/ka-bar-logo/' => '/markalar/ka-bar/',
        '/kershaw-iridium-2038-gercek-urun-dogal/' => '/bicak-incelemeleri/kershaw-iridium-incelemesi/',
        '/kershaw-iridium-duralock-detay/' => '/bicak-incelemeleri/kershaw-iridium-incelemesi/',
        '/kilit-mekanizmalari-kapak-gorseli/' => '/kilit-sistemleri/',
        '/logo-cold-steel/' => '/markalar/cold-steel/',
        '/logo-kershaw/' => '/markalar/kershaw/',
        '/menu-cakilar/' => '/cakilar/',
        '/menu-celikler/' => '/bicak-celikleri/',
        '/menu-incelemeler/' => '/bicaklar/',
        '/morakniv-logo/' => '/markalar/morakniv/',
        '/muela-logo/' => '/markalar/muela-knives/',
        '/ontario-knife-company-logo-bicakkurdu/' => '/markalar/ontario-knife-company/',
        '/opinel-logo/' => '/markalar/opinel/',
        '/pro-tech-logo/' => '/markalar/pro-tech/',
        '/sibirya-pm2-orange-fp/' => '/markalar/sibirya/',
        '/sog-logo/' => '/markalar/sog/',
        '/sog-terminus-xr-gercek-urun/' => '/markalar/sog/',
        '/spyderco_logo-s1280/' => '/markalar/spyderco/',
        '/tag/arti-guclu-kilit-sistemi/' => '/kilit-sistemleri/',
        '/tag/arti-paslanmazlik-avantaji/' => '/celik-puanlama-metodolojisi/',
        '/tag/ozellik-420j2-celik/' => '/bicak-celikleri/sus420j2-420j2/',
        '/tag/ozellik-cpm-magnacut/' => '/bicak-celikleri/cpm-magnacut/',
        '/tag/ozellik-frame-lock/' => '/kilit-sistemleri/',
        '/tag/ozellik-tri-ad-lock/' => '/kilit-sistemleri/',
        '/urun-incelemeleri-kapak-gorseli/' => '/bicaklar/',
        '/we-knife-logo/' => '/markalar/we-knife/',
        '/yeni-yayinlar-bicak-bileme-dogal/' => '/bileme/',
        '/yeni-yayinlar-outdoor-bicak-dogal/' => '/bicaklar/',
        '/zero-tolerance-logo/' => '/markalar/zero-tolerance/',
        '/2026/10/23/bicak-agiz-formlari-rehberi/' => '/bicak-agiz-formlari/',
        '/2026/10/23/bicak-guvenli-saklama-rehberi/' => '/bicak-guvenli-saklama-rehberi/',
        '/2026/10/23/bicak-kilifi-rehberi/' => '/bicaklar/',
        '/2026/10/23/bicak-olculeri-nasil-okunur/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/2026/10/23/bicak-paslanmasi-korozyon-rehberi/' => '/celik-puanlama-metodolojisi/',
        '/2026/10/23/drop-point-nedir/' => '/bicak-agiz-formlari/',
        '/2026/10/23/full-tang-nedir-bicak-govde-yapilari/' => '/full-tang-nedir/',
        '/2026/10/23/g10-kabze-nedir/' => '/bicaklar/',
        '/2026/10/23/mutfak-bicaklari-rehberi/' => '/bicaklar/',
        '/2026/10/23/paslanmaz-celik-kabze-nedir/' => '/bicaklar/',
        '/2026/10/23/saber-grind-nedir/' => '/bicaklar/',
        '/2026/10/23/sabit-bicak-mi-katlanir-caki-mi/' => '/bicaklar/',
        '/2026/10/23/tanto-bicak-nedir/' => '/bicak-agiz-formlari/',
        '/2026/10/23/wharncliffe-nedir/' => '/bicak-agiz-formlari/',
        '/2026/10/24/av-bicagi-secim-rehberi/' => '/bicaklar/',
        '/2026/10/24/biber-gazi-secerken-6-guvenlik-kontrolu/' => '/',
        '/2026/10/24/ilk-kamp-bicaginda-7-secim-kriteri/' => '/bicaklar/',
        '/2026/10/24/ilk-kamp-bicagini-secerken-yapilan-5-yaygin-hata/' => '/kamp-bicagi-secim-rehberi/',
        '/2026/10/28/case-bicak-rehberi/' => '/markalar/',
        '/2026/10/28/glock-bicak-rehberi/' => '/markalar/',
        '/2026/10/28/kai-bicak-rehberi/' => '/markalar/',
        '/2026/10/28/marttiini-bicak-rehberi/' => '/markalar/',
        '/2026/10/28/united-cutlery-bicak-rehberi/' => '/markalar/',
        '/2026/11/03/hello-world/' => '/',
        '/a_crisp_cinematic_high_resolution_outdoor_produc_4_batch_3/' => '/',
        '/a_high_resolution_professional_studio_style_close_1/' => '/',
        '/bicak-agiz-formlari-kapak-gorseli/' => '/bicaklar/',
        '/crkt-counterpart-crossbar-12c27-png/' => '/markalar/crkt/',
        '/dpx-gear-logo-fixed/' => '/markalar/',
        '/editor-secimi-mutfak-bicagi-dogal/' => '/bicaklar/',
        '/halmak-salur-n690-tactical/' => '/bicaklar/',
        '/kabze-malzemeleri-kapak-gorseli/' => '/bicaklar/',
        '/kai-logo-fixed/' => '/markalar/',
        '/kamp-editor-secimi-dogal-gorsel/' => '/bicaklar/',
        '/katlanir-caki-pivot-bakimi/' => '/bileme/',
        '/mantis-knives-logo-fixed/' => '/markalar/',
        '/marfione-custom-knives-logo-bicakkurdu/' => '/markalar/microtech/',
        '/marttiini-logo/' => '/markalar/',
        '/mutfak-bicaklari-banner/' => '/bicaklar/',
        '/ontario-rat-ii-gercek-urun/' => '/markalar/ontario-knife-company/',
        '/sample-page/' => '/',
        '/spyderco-pm2-k390-gercek-urun/' => '/bicak-incelemeleri/spyderco-para-military-2-incelemesi/',
        '/spyderco-shaman-gercek-urun/' => '/markalar/spyderco/',
        '/tag/arti-cift-tarafli-kullanim/' => '/kilit-sistemleri/',
        '/tag/arti-damascus-gorsel-karakteri/' => '/bicak-celikleri/',
        '/tag/arti-hafif-tasima/' => '/cakilar/',
        '/tag/arti-mirror-polish-yuzey/' => '/bicaklar/',
        '/tag/eksi-celik-seviyesi-modele-gore-degisiyor/' => '/bicak-celikleri/',
        '/tag/eksi-ince-uc-daha-hassas/' => '/bicaklar/',
        '/tag/eksi-iscilik-toleransi-degisebilir/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/eksi-kucuk-kabze-herkese-uygun-degil/' => '/cakilar/',
        '/tag/eksi-puruzsuz-sap-tutusu-sinirli/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/eksi-set-hacmi-yuksek/' => '/bicaklar/',
        '/tag/eksi-teknik-veri-sinirli/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/eksi-urun-kimligi-ayrica-dogrulanmali/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/ozellik-21-cm-toplam/' => '/bicaklar/',
        '/tag/ozellik-3-5-mm-celik-kalinligi/' => '/bicaklar/',
        '/tag/ozellik-4034-celik/' => '/bicak-celikleri/',
        '/tag/ozellik-57-mm-bicak/' => '/cakilar/',
        '/tag/ozellik-aus-8-d2-secenekleri/' => '/celik-karsilastirma/',
        '/tag/ozellik-cift-hareketli-otf/' => '/cakilar/',
        '/tag/ozellik-kiritsuke-form/' => '/bicaklar/',
        '/tag/ozellik-kompakt-edc/' => '/cakilar/',
        '/tag/ozellik-kvt-rulman/' => '/markalar/kershaw/',
        '/tag/ozellik-verzalit-kabze/' => '/bicaklar/',
        '/tag/puan-7-8/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/puan-8-6/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/tag/puan-9-2/' => '/kaynak-ve-inceleme-metodolojisi/',
        '/walther-logo-bicakkurdu/' => '/markalar/',
        '/zt-0556-dogru-model-kapak/' => '/markalar/zero-tolerance/',
        '/2026/10/23/columbia-skeleton-tactical-bicak-incelemesi/' => '/markalar/columbia/',
        '/2026/10/23/microtech-hera-incelemesi/' => '/microtech-hera-incelemesi/',
        '/2026/10/23/spyderco-para-military-2-k390-incelemesi/' => '/bicak-incelemeleri/spyderco-para-military-2-incelemesi/',
        '/2026/10/23/spyderco-shaman-vs-para-military-2/' => '/markalar/spyderco/',
        '/2026/10/24/crkt-counterpart-crossbar-incelemesi/' => '/markalar/crkt/',
        '/2026/10/24/crkt-counterpart-vs-benchmade-bugout-vs-kershaw-iridium/' => '/cakilar/',
        '/2026/10/24/kershaw-leek-1660-incelemesi/' => '/markalar/kershaw/',
        '/2026/10/24/ontario-rat-ii-incelemesi/' => '/markalar/ontario-knife-company/',
        '/2026/10/26/tsukishiro-damascus-kiritsuke-sef-bicagi-incelemesi/' => '/bicaklar/',
        '/2026/10/23/spyderco-shaman-vs-para-military-2/comment-page-1/' => '/markalar/spyderco/',
        '/2026/10/24/kershaw-leek-1660-incelemesi/comment-page-1/' => '/markalar/kershaw/',
        '/2026/10/24/spyderco-tenacious-incelemesi/comment-page-1/' => '/markalar/spyderco/',
        '/2026/10/23/1095-karbon-celik-nedir/' => '/bicak-celikleri/1095/',
        '/2026/10/23/4116-celik-nedir/' => '/bicak-celikleri/1-4116/',
        '/2026/10/23/420hc-celik-nedir/' => '/bicak-celikleri/420hc/',
        '/2026/10/23/aus-8-celik-nedir/' => '/bicak-celikleri/aus-8/',
        '/2026/10/23/back-lock-nedir/comment-page-1/' => '/kilit-sistemleri/',
        '/2026/10/23/benchmade-bicak-rehberi/' => '/markalar/benchmade/',
        '/2026/10/23/benchmade-bugout-535-incelemesi/' => '/bicak-incelemeleri/benchmade-bugout-535-incelemesi/',
        '/2026/10/23/button-lock-nedir/' => '/kilit-sistemleri/',
        '/2026/10/23/caki-kilit-mekanizmalari-rehberi/comment-page-1/' => '/kilit-sistemleri/',
        '/2026/10/23/cold-steel-ad-15-incelemesi/' => '/bicak-incelemeleri/cold-steel-ad15-incelemesi/',
        '/2026/10/23/cpm-magnacut-celik-nedir/' => '/bicak-celikleri/cpm-magnacut/',
        '/2026/10/23/cpm-s35vn-celik-nedir/' => '/bicak-celikleri/cpm-s35vn/',
        '/2026/10/23/crossbar-lock-nedir/' => '/kilit-sistemleri/',
        '/2026/10/23/kershaw-iridium-incelemesi/comment-page-1/' => '/bicak-incelemeleri/kershaw-iridium-incelemesi/',
        '/2026/10/23/m390-celik-nedir/' => '/bicak-celikleri/m390/',
        '/2026/10/23/microtech-bicak-rehberi/' => '/markalar/microtech/',
        '/2026/10/23/o1-celik-nedir/' => '/bicak-celikleri/o1/',
        '/2026/10/23/slipjoint-nedir/' => '/kilit-sistemleri/',
        '/2026/10/23/sog-bicak-rehberi/' => '/markalar/sog/',
        '/2026/10/23/zero-tolerance-bicak-rehberi/' => '/markalar/zero-tolerance/',
        '/2026/10/28/fallkniven-bicak-rehberi/' => '/markalar/fallkniven/',
        '/2026/10/28/ganzo-bicak-rehberi/' => '/markalar/ganzo/',
        '/2026/10/28/hogue-bicak-rehberi/' => '/markalar/hogue/',
        '/2026/10/28/kizer-bicak-rehberi/' => '/markalar/kizer/',
        '/2026/10/28/lionsteel-bicak-rehberi/' => '/markalar/lionsteel/',
        '/bicak-celikleri-a-z/' => '/bicak-celikleri/',
        '/bicak-markalari-a-z/' => '/markalar/',
        '/bicak-turleri-a-z/' => '/bicaklar/',
        '/bicak_celikleri_karsilastirma_rehberi_2026/' => '/celik-karsilastirma/',
        '/category/bicak-celikleri/' => '/bicak-celikleri/',
        '/category/urun-incelemeleri/' => '/bicaklar/',
        '/kategori/av-bicaklari/av-bicagi-bakimi/' => '/bileme/',
        '/kategori/av-bicaklari/av-cakilari/' => '/kategori/av-bicaklari/',
        '/kategori/av-bicaklari/sabit-av-bicaklari/' => '/kategori/av-bicaklari/',
        '/kategori/av-bicaklari/yuzme-bicaklari/' => '/kategori/av-bicaklari/',
        '/kategori/bushcraft-bicaklari/bushcraft-baslangic/' => '/kategori/bushcraft-bicaklari/',
        '/kategori/bushcraft-bicaklari/el-yapimi-bushcraft/' => '/kategori/bushcraft-bicaklari/',
        '/kategori/bushcraft-bicaklari/full-tang-bicaklar/' => '/full-tang-nedir/',
        '/kategori/bushcraft-bicaklari/odun-isleme-bicaklari/' => '/kategori/bushcraft-bicaklari/',
        '/kategori/edc-bicaklari/gunluk-kullanim-bicaklari/' => '/kategori/edc-bicaklari/',
        '/kategori/edc-bicaklari/hafif-edc-bicaklari/' => '/kategori/edc-bicaklari/',
        '/kategori/edc-bicaklari/katlanir-cakilar/' => '/kategori/edc-bicaklari/',
        '/kategori/edc-bicaklari/mini-cakilar/' => '/kategori/edc-bicaklari/',
        '/kategori/geleneksel-bicaklar/bolgesel-bicaklar/' => '/geleneksel-bicaklar/',
        '/kategori/geleneksel-bicaklar/el-yapimi-bicaklar/' => '/geleneksel-bicaklar/',
        '/kategori/kamp-bicaklari/cok-amacli-kamp-bicaklari/' => '/kategori/kamp-bicaklari/',
        '/kategori/kamp-bicaklari/kamp-cakilari/' => '/kategori/kamp-bicaklari/',
        '/kategori/kamp-bicaklari/kompakt-kamp-bicaklari/' => '/kategori/kamp-bicaklari/',
        '/kategori/kamp-bicaklari/sabit-kamp-bicaklari/' => '/kategori/kamp-bicaklari/',
        '/kategori/koleksiyon-bicaklari/geleneksel-cakilar/' => '/kategori/koleksiyon-bicaklari/',
        '/kategori/koleksiyon-bicaklari/klasik-koleksiyon/' => '/kategori/koleksiyon-bicaklari/',
        '/kategori/koleksiyon-bicaklari/limited-edition-bicaklar/' => '/kategori/koleksiyon-bicaklari/',
        '/kategori/taktik-bicaklar/sabit-taktik-bicaklar/' => '/kategori/taktik-bicaklar/',
        '/kategori/taktik-bicaklar/taktik-bicak-mekanizmalari/' => '/kilit-sistemleri/',
        '/kategori/taktik-bicaklar/taktik-cakilar/' => '/kategori/taktik-bicaklar/',
        '/kategori/taktik-bicaklar/tanto-bicaklar/' => '/bicak-agiz-formlari/',
        '/2026/10/23/bicak-kabze-malzemeleri-rehberi/' => '/bicak-kabze-malzemeleri/',
        '/2026/10/23/damascus-sam-celigi-nedir/' => '/damascus-celigi-nedir/',
    );
    $target = $map[$path] ?? null;
    if (!is_string($target) || $target === $path) return;
    wp_safe_redirect(home_url($target), 301, 'BicakKurdu-Birlesik-404');
    exit;
}, -9998);

/* Preserved rules for dated GSC writing aliases and old image attachment paths. */
/**
 * Plugin Name: Bıçak Kurdu – GSC Eski Yazı 404 Kurtarma
 * Description: GSC'de görülen eski 2026 tarihli bıçak rehberleri, çelik yazıları, markalar ve incelemeler için yalnızca 404 olduğunda ilgili yayındaki hedefe 301 gönderir. Eski 301 eklentilerini değiştirmez.
 * Version: 1.0.4
 * Author: Bıçak Kurdu
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'template_redirect', function () {
    if ( is_admin() || ! is_404() ) { return; }
    $request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path = wp_parse_url( $request, PHP_URL_PATH );
    if ( ! is_string( $path ) ) { return; }
    $path = trailingslashit( '/' . ltrim( $path, '/' ) );
    // Eski tarihli, kaldırılmış yazı yolları dışında kalan hiçbir URL değiştirilmez.
    if ( ! preg_match( '#^/2026/(?:10/(?:23|24|25|26|28|29)|11/01)/([a-z0-9-]+)/(?:comment-page-[0-9]+/)?$#', $path, $m ) ) { return; }
    $slug = $m[1];
    $target = '';

    // Aynı konuda var olan güncel hedefler. Mümkünse kategori yerine birebir eşleşme kullan.
    $exact = array(
        '8cr13mov-celik-nedir' => '/bicak-celikleri/8cr13mov/',
        'damascus-sam-celigi-nedir' => '/bicak-celikleri/',
        '14c28n-celik-nedir' => '/bicak-celikleri/14c28n/',
        '12c27-celik-nedir' => '/bicak-celikleri/alleima-12c27/',
        'vg10-celik-nedir' => '/bicak-celikleri/vg-10/',
        'n690-celik-nedir' => '/bicak-celikleri/bohler-n690/',
        '4116-celik-nedir' => '/bicak-celikleri/1-4116/',
        '1095-karbon-celik-nedir' => '/bicak-celikleri/1095/',
        'bicak-celikleri-karsilastirma-tablosu' => '/celik-karsilastirma/',
        'bicak-celikleri-karsilastirma-rehberi-2026' => '/celik-karsilastirma/',
        'd2-mi-n690-mi-bicak-celigi-secimi' => '/celik-karsilastirma/',
        'bicak-celigi-secerken-nelere-bakilmali-d2-n690-vg10-440c' => '/celik-karsilastirma/',
        'bicak-celikleri-sozlugu' => '/bicak-celikleri/',
        'bicak-bileme-sistemleri-karsilastirmasi' => '/bileme/',
        'bicak-bileme-acisi-rehberi' => '/bileme/',
        'bileme-tasi-grit-rehberi' => '/bileme/',
        'bicak-yagi-rehberi' => '/bileme/',
        'bicak-bakimi-bileme-rehberi' => '/bileme/',
        'deri-strop-nedir' => '/bileme/',
        'katlanir-caki-pivot-bakimi' => '/bileme/',
        'caki-kilit-sistemleri-karsilastirma-tablosu' => '/kilit-sistemleri/',
        'liner-lock-mu-frame-lock-mu' => '/kilit-sistemleri/',
        'frame-lock-nedir' => '/kilit-sistemleri/',
        'liner-lock-nedir' => '/kilit-sistemleri/',
        'back-lock-nedir' => '/kilit-sistemleri/',
        'caki-kilit-mekanizmalari-rehberi' => '/kilit-sistemleri/',
        'bicak-terimleri-sozlugu' => '/bicaklar/',
        'bicagin-kisa-tarihi' => '/bicaklar/',
        'sahte-klon-replika-bicak-nasil-anlasilir' => '/kaynak-ve-inceleme-metodolojisi/',
        'turkiyede-bicak-tasima-bulundurma-satis-rehberi-2026' => '/bicaklar/',
        '50-dolar-alti-en-iyi-katlanir-cakilar' => '/cakilar/',
        'edc-caki-secme-rehberi-2026' => '/cakilar/',
        'bicak-satin-alma-kontrol-listesi' => '/kaynak-ve-inceleme-metodolojisi/',
        'kamp-edc-bicak-celigi-nasil-secilir' => '/bicak-celikleri/',
        'bicak-taslama-geometrileri-rehberi' => '/bicaklar/',
        'kydex-mi-deri-kilif-mi' => '/bicaklar/',
        'leatherman-wave-plus-incelemesi' => '/bicak-incelemeleri/leatherman-wave-plus-sog-powerlock-gerber-center-drive-karsilastirma/',
        'ontario-rat-ii-incelemesi' => '/markalar/ontario-knife-company/',
        'cold-steel-bushman-incelemesi' => '/markalar/cold-steel/',
        'cold-steel-micro-recon-incelemesi' => '/markalar/cold-steel/',
        'ka-bar-dozier-folding-hunter-incelemesi' => '/markalar/ka-bar/',
        'sog-terminus-xr-incelemesi' => '/markalar/sog/',
        'zero-tolerance-zt-0556-incelemesi' => '/markalar/zero-tolerance/',
        'edc-titan-nester-cep-cakisi-incelemesi' => '/cakilar/',
        'akc-leverletto-lever-lock-stiletto-caki-klon-incelemesi' => '/markalar/akc/',
        'en-iyi-crkt-bicak-modelleri-karsilastirmasi' => '/markalar/crkt/',
        'marfione-custom-knives-rehberi' => '/markalar/microtech/',
        'smith-wesson-bicak-rehberi' => '/markalar/smith-and-wesson/',
        'muela-bicak-rehberi' => '/markalar/muela-knives/',
        'mikov-bicak-rehberi' => '/markalar/mikov-knives/',
        'mikov-knives-rehberi' => '/markalar/mikov-knives/',
        'swiza-bicak-rehberi' => '/markalar/',
        'browning-bicak-rehberi' => '/markalar/',
        'blackhawk-bicak-rehberi' => '/markalar/',
        'walther-bicak-rehberi' => '/markalar/',
        'heckler-koch-bicak-rehberi' => '/markalar/',
        'kai-bicak-rehberi' => '/markalar/',
        'case-bicak-rehberi' => '/markalar/',
        'maserin-bicak-rehberi' => '/markalar/',
        'dpx-gear-bicak-rehberi' => '/markalar/',
        'mantis-knives-rehberi' => '/markalar/',
        'united-cutlery-bicak-rehberi' => '/markalar/',
        'bicak-kabze-malzemeleri-rehberi' => '/bicaklar/',
        'ahsap-kabze-nedir' => '/bicaklar/',
        'aluminyum-kabze-nedir' => '/bicaklar/',
        'karbon-fiber-kabze-nedir' => '/bicaklar/',
        'frn-kabze-nedir' => '/bicaklar/',
        'micarta-kabze-nedir' => '/bicaklar/',
        'titanyum-kabze-nedir' => '/bicaklar/',
        'clip-point-nedir' => '/bicaklar/',
        'convex-grind-nedir' => '/bicaklar/',
        'full-flat-grind-nedir' => '/bicaklar/',
        'hollow-grind-nedir' => '/bicaklar/',
        'scandi-grind-nedir' => '/bicaklar/',
        'sheepsfoot-nedir' => '/bicaklar/',
        'spear-point-nedir' => '/bicaklar/',
        'tanto-mu-drop-point-mi' => '/bicaklar/',
        'sef-bicagi-mi-santoku-mu' => '/bicaklar/',
    );
    if ( isset( $exact[$slug] ) ) {
        $target = $exact[$slug];
    }

    // Çelik yazıları: yalnızca mevcut, yayımlanmış çelik profiline gönder.
    if ( ! $target && preg_match( '/^(.+)-celik-nedir$/', $slug, $steel_match ) ) {
        $steel_slug = $steel_match[1];
        $aliases = array( 'vg10' => 'vg-10', 'n690' => 'bohler-n690', '12c27' => 'alleima-12c27', '4116' => '1-4116' );
        if ( isset( $aliases[$steel_slug] ) ) { $steel_slug = $aliases[$steel_slug]; }
        $post = get_page_by_path( $steel_slug, OBJECT, 'celik' );
        if ( $post && 'publish' === $post->post_status ) { $target = get_permalink( $post ); }
    }

    // Marka rehberi: aynı markanın yayımlanmış gerçek sayfasına gönder.
    if ( ! $target && preg_match( '/^(.+?)(?:-bicak-rehberi|-rehberi)$/', $slug, $brand_match ) ) {
        $brand_slug = $brand_match[1];
        $brand_aliases = array(
            'muela' => 'muela-knives', 'smith-wesson' => 'smith-and-wesson',
            'mikov' => 'mikov-knives', 'ka-bar' => 'ka-bar', 'leatherman' => 'leatherman',
            'we-knife' => 'we-knife', 'aga-campolin' => 'aga-campolin',
            'frank-beltrame' => 'frank-beltrame', 'chris-reeve-knives' => 'chris-reeve-knives',
            'victorinox' => 'victorinox', 'heretic-knives' => 'heretic-knives',
        );
        if ( isset( $brand_aliases[$brand_slug] ) ) { $brand_slug = $brand_aliases[$brand_slug]; }
        $brand = get_page_by_path( 'markalar/' . $brand_slug, OBJECT, 'page' );
        if ( $brand && 'publish' === $brand->post_status ) { $target = get_permalink( $brand ); }
    }

    // Aynı modelin taşınmış incelemesi varsa en doğru hedef odur.
    if ( ! $target && ( substr( $slug, -11 ) === '-incelemesi' || strpos( $slug, '-karsilastirmasi' ) !== false ) ) {
        $review = get_page_by_path( $slug, OBJECT, 'inceleme' );
        if ( $review && 'publish' === $review->post_status ) { $target = get_permalink( $review ); }
    }

    // Geriye kalan eski yazı başlıkları için konuya en yakın yayımlanan merkez.
    if ( ! $target ) {
        if ( preg_match( '/(?:lock|kilit|mekanizma|crossbar|axis)/', $slug ) ) { $target = '/kilit-sistemleri/'; }
        elseif ( preg_match( '/(?:bileme|bilem|masat|grit|strop|pivot|bicak-yagi|paslanma)/', $slug ) ) { $target = '/bileme/'; }
        elseif ( preg_match( '/(?:celik|hrc|rockwell)/', $slug ) ) { $target = '/bicak-celikleri/'; }
        elseif ( preg_match( '/(?:marka|ureticiler)/', $slug ) ) { $target = '/markalar/'; }
        elseif ( preg_match( '/(?:edc|caki|katlanir|slipjoint)/', $slug ) ) { $target = '/cakilar/'; }
        elseif ( preg_match( '/(?:bicak|kamp|bushcraft|taslama|kabze|grind|tanto|clip-point|drop-point|wharncliffe|sheepsfoot)/', $slug ) ) { $target = '/bicaklar/'; }
    }
    if ( ! $target ) { return; }

    $dest = ( substr( $target, 0, 1 ) === '/' ) ? home_url( $target ) : $target;
    if ( trailingslashit( untrailingslashit( $dest ) ) === home_url( $path ) ) { return; }
    // İlgisiz dış alan adına veya hareketli sorguya yönlendirme yapılmaz.
    if ( wp_parse_url( $dest, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { return; }
    if ( wp_safe_redirect( $dest, 301, 'Bicak Kurdu GSC 404 Rescue' ) ) { exit; }
}, 8 );

// Arama Konsolu'nda saptanan, testi 404 dönen eski arşiv ve görsel eki URL'leri.
add_action( 'template_redirect', function () {
    if ( is_admin() || ! is_404() ) { return; }
    $request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path = wp_parse_url( $request, PHP_URL_PATH );
    if ( ! is_string( $path ) ) { return; }
    $path = trailingslashit( '/' . ltrim( $path, '/' ) );
    $rules = array(
        '/bicak-kabze-malzemeleri-a-z/' => '/bicaklar/',
        '/bicak-agiz-formlari-a-z/' => '/bicaklar/',
        '/bicak-bilgi-haritasi/' => '/bicaklar/',
        '/bicak-kurdu/' => '/',
        '/category/bicak-markalari/' => '/markalar/',
        '/category/edc-cakilar/page/2/' => '/cakilar/',
        '/category/edc-cakilar/page/3/' => '/cakilar/',
        '/category/kamp-ve-bushcraft/' => '/bicaklar/',
        '/sabit-bicaklar/' => '/bicaklar/',
        '/tag/ozellik-d2-celik/' => '/bicak-celikleri/d2/',
        '/tag/ozellik-speedsafe/' => '/markalar/kershaw/',
        '/tag/ozellik-ed-halligan-tasarimi/' => '/2026/10/23/crkt-5500-kiss-tasarim-incelemesi/',
        '/tag/arti-ergonomik-kabze/' => '/bicaklar/',
        '/author/gerberbicak/page/12/' => '/yazar/ozgur-firat/',
        '/benchmade-bugout-535-bicak-detayi/' => '/bicak-incelemeleri/benchmade-bugout-535-incelemesi/',
        '/benchmade-bugout-535-inceleme-kapak/' => '/bicak-incelemeleri/benchmade-bugout-535-incelemesi/',
        '/sog-terminus-xr-bicak-detayi/' => '/markalar/sog/',
        '/crkt-minimalist-bowie-2387/' => '/markalar/crkt/',
        '/glock-logo/' => '/markalar/',
        '/heretic-knives-logo/' => '/markalar/heretic-knives/',
        '/victorinox-logo/' => '/markalar/victorinox/',
        '/akc-logo/' => '/markalar/akc/',
        '/columbia-tiger-strongarm/' => '/markalar/columbia/',
        '/crkt-logo/' => '/markalar/crkt/',
        '/demko-knives-logo/' => '/markalar/demko-knives/',
        '/maserin-logo/' => '/markalar/',
        '/viper-knives-logo/' => '/markalar/viper-knives/',
        '/mutfak-bicaklari-gercek-sef-bicagi/' => '/bicaklar/',
        '/haps-bugs-pm2-tanto-fp/' => '/markalar/haps-bugs/',
        '/haps-bugs-treminus-ankaoutdoor/' => '/markalar/haps-bugs/',
    );
    if ( ! isset( $rules[$path] ) ) { return; }
    $target = home_url( $rules[$path] );
    if ( wp_safe_redirect( $target, 301, 'Bicak Kurdu GSC Legacy Archive Recovery' ) ) { exit; }
}, 9 );


/* Preserved legacy author and four verified redirect behaviors. */
/**
 * Plugin Name: BıçakKurdu - Eski Adresler 301
 * Description: Onaylanan marka arşivi, yazar ve dört eski içerik adresini kalıcı olarak yönlendirir.
 * Version: 1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_action( 'template_redirect', function () {
    if ( is_admin() || ! in_array( $_SERVER['REQUEST_METHOD'] ?? '', array( 'GET', 'HEAD' ), true ) ) { return; }
    $uri = wp_unslash( $_SERVER['REQUEST_URI'] ?? '' );
    $path = wp_parse_url( $uri, PHP_URL_PATH );
    $query = wp_parse_url( $uri, PHP_URL_QUERY );
    $target = '';
    if ( '/author/' === $path && ( null === $query || '' === $query ) ) {
        $target = '/yazar/ozgur-firat/';
    } elseif ( '/category/bicak-markalari/' === $path ) {
        $params = array();
        parse_str( (string) $query, $params );
        ksort( $params );
        $allowed = array(
            array( 'cst' => '' ),
            array( 'cst' => '', 'query-page' => '2' ),
            array( 'cst' => '', 'query-page' => '3' ),
        );
        if ( in_array( $params, $allowed, true ) ) { $target = '/markalar/'; }
    }
    $content_redirects = array(
        '/2026/10/23/cpm-magnacut-celik-nedir/' => '/bicak-celikleri/cpm-magnacut/',
        '/2026/10/23/14c28n-celik-nedir/' => '/bicak-celikleri/14c28n/',
        '/2026/10/23/8cr13mov-celik-nedir/' => '/bicak-celikleri/8cr13mov/',
        '/2026/10/23/zero-tolerance-bicak-rehberi/' => '/markalar/zero-tolerance/',
    );
    if ( '' === $target && ( null === $query || '' === $query ) && isset( $content_redirects[$path] ) ) {
        $target = $content_redirects[$path];
    }
    if ( '' !== $target ) {
        wp_safe_redirect( home_url( $target ), 301, 'BK Four Redirects' );
        exit;
    }
}, -1000 );


/* Preserved legacy archive noindex and image attachment file redirects. */
/**
 * Plugin Name: Bıçak Kurdu – İnceleme ve Arşiv URL Düzeltmeleri
 * Description: Yanlış hedefe giden üç eski adresi ilgili inceleme merkezlerine taşır ve içeriği olmayan dört eski rehber arşivini aramadan çıkarır. Ürün incelemelerinin eski URL'lerine dokunmaz.
 * Version: 1.2.0
 * Author: Bıçak Kurdu
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Only handle exactly these archived paths. Published model review paths are untouched. */
function bk_consolidated_archive_fix_current_path() {
    $request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path = wp_parse_url( $request, PHP_URL_PATH );
    if ( ! is_string( $path ) ) { return ''; }
    return trailingslashit( '/' . ltrim( $path, '/' ) );
}

add_action( 'template_redirect', function () {
    if ( is_admin() || wp_doing_ajax() ) { return; }
    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper( sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) ) : 'GET';
    if ( $method !== 'GET' && $method !== 'HEAD' ) { return; }
    $redirects = array(
        '/bicak-incelemeleri/' => '/incelemeler/',
        '/category/urun-incelemeleri/' => '/incelemeler/',
        '/kategori/geleneksel-bicaklar/' => '/geleneksel-bicaklar/',
    );
    $path = bk_consolidated_archive_fix_current_path();
    if ( ! isset( $redirects[ $path ] ) ) { return; }
    $target = home_url( $redirects[$path] );
    if ( wp_safe_redirect( $target, 301, 'Bicak Kurdu Related Archive Fix' ) ) { exit; }
}, -100 );

add_filter( 'wp_robots', function ( $robots ) {
    $no_content_archives = array(
       '/rehber-konulari/hukuk-ve-guvenlik/' => true,
       '/rehber-konulari/koleksiyonculuk-ve-tarih/' => true,
       '/rehber-konulari/satin-alma-rehberleri/' => true,
       '/rehber-konulari/teknik-terimler/' => true,
    );
    if ( isset( $no_content_archives[ bk_consolidated_archive_fix_current_path() ] ) ) {
        // Re-enable normal indexability automatically when this guide taxonomy gains published items.
        if ( is_tax( 'bk_rehber_konu' ) ) {
            $term = get_queried_object();
            if ( $term instanceof WP_Term && (int) $term->count > 0 ) { return $robots; }
        }
        unset( $robots['index'] );
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }
    return $robots;
}, 99 );


/** Send publicly visible image attachment permalink pages to their original image file. */
add_action( 'template_redirect', function () {
    if ( is_admin() || ! is_attachment() ) { return; }
    $post_id = get_queried_object_id();
    if ( ! $post_id ) { return; }
    $mime = (string) get_post_mime_type( $post_id );
    if ( strpos( $mime, 'image/' ) !== 0 ) { return; }
    $file_url = wp_get_attachment_url( $post_id );
    if ( ! $file_url || ! wp_http_validate_url( $file_url ) ) { return; }
    if ( wp_safe_redirect( $file_url, 301, 'Bicak Kurdu Image Attachment' ) ) { exit; }
}, -90 );


/* BK-uncategorized-410: Do not expose the empty built-in default category as an indexable page. */
add_action('template_redirect', function() {
    if (is_admin() || wp_doing_ajax()) return;
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET' && $method !== 'HEAD') return;
    $path = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!is_string($path) || '/' . trim($path, '/') !== '/category/uncategorized') return;
    status_header(410);
    nocache_headers();
    header('X-Robots-Tag: noindex, follow', true);
    $tpl = get_404_template();
    if ($tpl && is_readable($tpl)) {
        include $tpl;
    } else {
        echo '<h1>Bu kategori kaldırıldı</h1><p>İçerik kategorileri için <a href="' . esc_url(home_url('/makaleler/')) . '">Makaleler</a> sayfasını ziyaret edin.</p>';
    }
    exit;
}, -9999);

/* BK-retired-model-410: Old single-model reviews with no corresponding published article.
 * Do not falsely send searches for these models to unrelated brand or general landing pages.
 * Restore a 301 when the matching genuine article is published. */
add_action('template_redirect', function() {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return;
    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET' && $method !== 'HEAD') return;
    $incoming = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!is_string($incoming)) return;
    $path = '/' . trim(rawurldecode($incoming), '/');
    $retired = array(
        '/edc-titan-nester-cep-cakisi-incelemesi' => true,
        '/2026/10/23/columbia-skeleton-tactical-bicak-incelemesi' => true,
        '/2026/10/24/ontario-rat-ii-incelemesi' => true,
        '/2026/10/24/crkt-counterpart-crossbar-incelemesi' => true,
        '/2026/10/24/crkt-counterpart-vs-benchmade-bugout-vs-kershaw-iridium' => true,
        '/2026/10/24/kershaw-leek-1660-incelemesi' => true,
        '/2026/10/23/spyderco-shaman-vs-para-military-2' => true,
        '/2026/10/26/tsukishiro-damascus-kiritsuke-sef-bicagi-incelemesi' => true,
        '/2026/10/24/kershaw-leek-1660-incelemesi/comment-page-1' => true,
        '/2026/10/23/spyderco-shaman-vs-para-military-2/comment-page-1' => true,
    );
    if (!isset($retired[$path])) return;
    status_header(410);
    nocache_headers();
    header('X-Robots-Tag: noindex, follow', true);
    $template = get_404_template();
    if ($template && is_readable($template)) {
        include $template;
    } else {
        echo '<h1>İçerik kaldırıldı</h1><p>Yayımlanmış içerikler için <a href="' . esc_url(home_url('/incelemeler/')) . '">İncelemeler</a> sayfasını açın.</p>';
    }
    exit;
}, -10000);
