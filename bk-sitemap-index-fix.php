<?php
/**
 * Plugin Name: BıçakKurdu - Sitemap ve İndeksleme Düzeltmesi
 * Description: Jetpack XML sitemap'e yayınlanan inceleme, çelik ve rehber içeriklerini dahil eder. Robots.txt'de gereksiz Google News sitemap bildirimini kaldırır.
 * Version: 1.1.0
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

/** Include only real, non-redirecting category hubs in Jetpack's existing page sitemap. */
add_filter('jetpack_page_sitemap_other_urls', function($urls) {
    if (!is_array($urls)) $urls=array();
    $seen=array();
    foreach($urls as $item) {
        if (isset($item['loc'])) $seen[untrailingslashit((string)$item['loc'])]=true;
    }
    $taxonomies=array('bicak_kategori','bk_celik_grubu');
    foreach($taxonomies as $tax) {
        if (!taxonomy_exists($tax)) continue;
        $terms=get_terms(array('taxonomy'=>$tax,'hide_empty'=>true));
        if (is_wp_error($terms) || !is_array($terms)) continue;
        foreach($terms as $term) {
            if ($tax==='bicak_kategori' && $term->slug==='geleneksel-bicaklar') continue; // 301 to real /geleneksel-bicaklar/ page.
            $url=get_term_link($term);
            if (is_wp_error($url) || !is_string($url) || !str_starts_with($url,home_url('/'))) continue;
            $key=untrailingslashit($url);
            if(isset($seen[$key])) continue;
            $entry=array('loc'=>$url);
            $last=get_posts(array('post_type'=>'any','post_status'=>'publish','numberposts'=>1,'orderby'=>'modified','order'=>'DESC','tax_query'=>array(array('taxonomy'=>$tax,'field'=>'term_id','terms'=>array($term->term_id))),'fields'=>'ids','suppress_filters'=>true));
            if($last){
                $modified=get_post_modified_time('Y-m-d\TH:i:s\Z',true,(int)$last[0]);
                if(is_string($modified) && $modified) $entry['lastmod']=$modified;
            }
            $urls[]=$entry;
            $seen[$key]=true;
        }
    }
    $author=home_url('/yazar/');
    if(!isset($seen[untrailingslashit($author)])) {
        $urls[]=array('loc'=>$author);
    }
    return $urls;
}, 90);

/** Avoid advertising a Google News feed for this reference-style publication. */
add_filter('jetpack_news_sitemap_include_in_robotstxt', '__return_false', 100);

/** Use Turkish as the feed language, consistent with the site's tr_TR locale. */
add_action('init', function() {
    if (get_option('rss_language') !== 'tr') {
        update_option('rss_language', 'tr', false);
    }
}, 100);


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
