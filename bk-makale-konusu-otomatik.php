<?php
/**
 * Plugin Name: BıçakKurdu - Makale Konusu Otomatik Kartlar
 * Description: Yalnızca konuya uygun yeni yazıları Makaleler alt merkezlerinde listeler. Model incelemelerini sırf kamp/tarih kelimesi geçtiği için bu merkezlere eklemez. URL'leri değiştirmez.
 * Version: 1.0.0
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

/** Allow thematic editorial category terms on existing review and guide post types too. */
function bk_makale_konusu_register_categories() {
    foreach (array('inceleme', 'rehber') as $type) {
        if (post_type_exists($type)) {
            register_taxonomy_for_object_type('category', $type);
        }
    }
}
add_action('init', 'bk_makale_konusu_register_categories', 110);

/**
 * Intentionally classify from the title, not body: model review text regularly
 * mentions camping, sharpening, or history even when that is not the article's topic.
 */
function bk_makale_konusu_from_title($title) {
    $title = wp_strip_all_tags(html_entity_decode((string) $title, ENT_QUOTES, 'UTF-8'));
    $model_review = (bool) preg_match('/inceleme(?:si|leri)?|karşılaştırma(?:sı)?|\bvs\b/iu', $title);

    // An explicitly fishing-oriented knife or fileto knife is relevant even if reviewed.
    if (preg_match('/balıkçılık|balıkçı|balık tut|olta|fileto\s+(?:bıçağı|çakısı|bıçak|çakı)|(?:balık|fileto)\s+ekipman/iu', $title)) {
        return 64; // Balıkçılık
    }

    // Keep normal product reviews in the /incelemeler/ directory.
    if ($model_review) {
        return 0;
    }

    if (preg_match('/bileme|bilenir|bilenmesi|biley|masat|keskinleştir/iu', $title)) {
        return 57; // Bileme, independent from Nasıl Yapılır
    }

    if (preg_match('/hayatta kalma.*(?:film|dizi)|(?:film|dizi).*hayatta kalma|macera\s+(?:film|dizi)|(?:film|dizi)\s+öneri|kamp alanlar[ıi]|kamp yerler[iı]|kamp yeri|(?:gezi|doğa|kamp)\s+rota(?:sı|ları)?\s+öner/i', $title)) {
        return 61; // Maceralar
    }

    if (preg_match('/tarih|hik[aâ]ye|öykü|köken|geçmişi|geleneksel\s+(?:ustalar|yapım|kültür)/iu', $title)) {
        return 62; // Tarih ve Hikayeler
    }

    if (preg_match('/nasıl|nasıl\s+yapılır|yapılışı|yapımı|hazırlanır|kurulur|adım\s+adım|tarifi|tarifleri/iu', $title)) {
        return 56; // Nasıl Yapılır, e.g. Kamp Yaparken Kahve Nasıl Yapılır
    }

    if (preg_match('/kamp|çadır|kampçı|kampçılık|kamp mutfağı|doğada yaşam/iu', $title)) {
        return 63; // Kamp articles, NOT generic camping-knife reviews
    }

    if (preg_match('/havalı\s+tüfek|bb\s+tabanca|airsoft|pellet\s+tabanca/iu', $title)) {
        return 60; // Havalı Tüfek ve BB Tabanca
    }

    return 0;
}

/** Never overwrite an editor's chosen editorial topic. */
function bk_makale_konusu_assign($post_id, $post = null) {
    $post_id = (int) $post_id;
    $post = $post instanceof WP_Post ? $post : get_post($post_id);
    if (!$post || $post->post_status !== 'publish' || wp_is_post_revision($post_id)) {
        return;
    }
    if (!in_array($post->post_type, array('post', 'inceleme', 'rehber'), true)) {
        return;
    }
    $editorial_topics = array(56, 57, 60, 61, 62, 63, 64);
    $current = wp_get_post_categories($post_id);
    if (array_intersect($editorial_topics, array_map('intval', $current))) {
        return;
    }
    $target = bk_makale_konusu_from_title($post->post_title);
    if ($target > 0 && term_exists($target, 'category')) {
        wp_set_post_terms($post_id, array($target), 'category', true);
    }
}
add_action('save_post', function($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    bk_makale_konusu_assign($post_id, $post);
}, 30, 2);

/** The six specific Gutenberg query loops should search article + guide post types. */
add_filter('query_loop_block_query_vars', function($query, $block) {
    $query_id = (int) ($block->attributes['queryId'] ?? 0);
    $approved = array(
        292902 => 2929, // Kamp
        292802 => 2928, // Tarih
        292701 => 2927, // Maceralar
        293001 => 2930, // Balıkçılık
        290501 => 2905, // Nasıl Yapılır
        45001  => 450,  // Bileme
    );
    if (isset($approved[$query_id]) && is_page($approved[$query_id])) {
        $query['post_type'] = array('post', 'inceleme', 'rehber');
        $query['ignore_sticky_posts'] = true;
    }
    return $query;
}, 30, 2);

/** One-time precise filing for existing title-matched articles, not unrelated reviews. */
register_activation_hook(__FILE__, function() {
    bk_makale_konusu_register_categories();
    foreach (array('post', 'inceleme', 'rehber') as $type) {
        $ids = get_posts(array(
            'post_type' => $type,
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'suppress_filters' => true,
        ));
        foreach ($ids as $id) {
            bk_makale_konusu_assign((int) $id);
        }
    }
});
