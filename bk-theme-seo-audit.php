<?php
/**
 * Plugin Name: BK Temporary Theme and SEO Audit
 * Version: 1.0.0
 * Description: One-time administrator audit. Writes read-only findings to a private draft.
 */
defined('ABSPATH') || exit;
register_activation_hook(__FILE__, function() {
    $theme = get_stylesheet_directory();
    $header = is_readable($theme.'/header.php') ? file_get_contents($theme.'/header.php') : '';
    $functions = is_readable($theme.'/functions.php') ? file_get_contents($theme.'/functions.php') : '';
    $style = is_readable($theme.'/style.css') ? file_get_contents($theme.'/style.css') : '';
    $script = array();
    foreach (glob($theme.'/*.php') ?: array() as $f) {
        $data = is_readable($f) ? file_get_contents($f) : '';
        $script[] = array('name'=>basename($f),'bytes'=>strlen($data),'links'=>substr_count($data,'Bileme'),'menu_calls'=>substr_count($data,'wp_nav_menu'));
    }
    $r = array(
        'theme'=>get_stylesheet(),
        'stylesheet_path'=>$theme,
        'header'=>$header,
        'functions'=>$functions,
        'style_head'=>substr($style,0,40000),
        'php_files'=>$script,
        'blog_public'=>(int)get_option('blog_public',1),
        'jetpack_sitemaps_on'=>in_array('sitemaps',(array)get_option('jetpack_active_modules',array()),true),
        'jetpack_sitemap_options_keys'=>array_keys((array)get_option('jetpack_sitemap_options',array())),
        'sitemap_server_present'=>function_exists('wp_sitemaps_get_server'),
        'robots_filter_text'=>apply_filters('robots_txt',"User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n",(int)get_option('blog_public',1)),
        'timestamp'=>gmdate('c'),
    );
    foreach (array('/robots.txt','/wp-sitemap.xml','/sitemap.xml','/sitemap_index.xml','/news-sitemap.xml') as $endpoint) {
        $response = wp_remote_get(home_url($endpoint),array('timeout'=>5,'redirection'=>3,'limit_response_size'=>17000,'sslverify'=>false,'headers'=>array('User-Agent'=>'BK-Internal-Audit/1.0')));
        $r['endpoints'][$endpoint] = is_wp_error($response)
            ? array('error'=>$response->get_error_message())
            : array('status'=>wp_remote_retrieve_response_code($response),
                'type'=>wp_remote_retrieve_header($response,'content-type'),
                'body_start'=>substr(wp_remote_retrieve_body($response),0,17000));
    }
    wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_title'=>'TEMP BK Theme and SEO Audit 2026-11-23','post_name'=>'bk-theme-seo-audit-20261123','post_content'=>base64_encode(wp_json_encode($r,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)),'comment_status'=>'closed'));
});
