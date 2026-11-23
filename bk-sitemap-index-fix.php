<?php
/**
 * Plugin Name: BıçakKurdu - Sitemap ve İndeksleme Düzeltmesi
 * Description: Jetpack XML sitemap'e yayınlanan inceleme, çelik ve rehber içeriklerini dahil eder. Robots.txt'de gereksiz Google News sitemap bildirimini kaldırır.
 * Version: 1.0.1
 * Author: BıçakKurdu
 */
defined('ABSPATH') || exit;

/** Include public, published custom content in Jetpack's canonical XML sitemap. */
function bk_sitemap_include_editorial_post_types($post_types) {
    if (!is_array($post_types)) $post_types = array('post','page');
    foreach (array('inceleme','celik','rehber') as $post_type) {
        if (post_type_exists($post_type) && !in_array($post_type,$post_types,true)) {
            $post_types[] = $post_type;
        }
    }
    return array_values(array_unique($post_types));
}
add_filter('jetpack_sitemap_post_types','bk_sitemap_include_editorial_post_types',99,1);

/**
 * The website is a technical publishing archive, not a Google News submission.
 * Keep robots.txt pointing to the one authoritative XML sitemap index.
 */
add_filter('robots_txt', function($output, $public) {
    if (!$public) return $output;
    $output = preg_replace('~^[ \t]*Sitemap:[ \t]*https?://[^\r\n ]*/news-sitemap\.xml[ \t]*\r?\n?~mi','',$output);
    return $output;
}, PHP_INT_MAX, 2);

register_activation_hook(__FILE__, function() {
    $report = array('date'=>gmdate('c'));
    $types = array('post','page');
    $report['included_types'] = bk_sitemap_include_editorial_post_types($types);
    // Jetpack persists the post-type list in this option. Refresh it before regeneration.
    $report['previous_cached_types'] = get_option('jetpack_sitemap_post_types', array());
    update_option('jetpack_sitemap_post_types', $report['included_types'], false);
    $report['stored_types'] = get_option('jetpack_sitemap_post_types', array());
    if (class_exists('Jetpack_Sitemap_Builder') && class_exists('Jetpack_Sitemap_Librarian')) {
        try {
            $librarian = new Jetpack_Sitemap_Librarian();
            $librarian->delete_all_stored_sitemap_data();
            delete_transient('jetpack_news_sitemap_xml');
            delete_transient('jetpack-sitemap-state-lock');
            $builder = new Jetpack_Sitemap_Builder();
            $builder->update_sitemap();
            $report['rebuild'] = 'completed';
        } catch (Throwable $e) {
            $report['rebuild'] = 'failed';
            $report['error'] = substr($e->getMessage(),0,600);
        }
    } else {
        $report['rebuild'] = 'jetpack_builder_unavailable';
    }
    update_option('bk_jetpack_sitemap_fix_report_20261123', $report, false);
});
