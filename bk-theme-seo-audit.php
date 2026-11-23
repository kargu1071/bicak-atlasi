<?php
/**
 * Plugin Name: BK Temporary Theme and SEO Audit
 * Version: 1.0.2
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

    $r['menu_js'] = is_readable($theme.'/assets/js/main.js') ? file_get_contents($theme.'/assets/js/main.js') : '';
    $r['icons_php'] = is_readable($theme.'/inc/icons.php') ? file_get_contents($theme.'/inc/icons.php') : '';
    $r['css_files'] = array();
    $r['css_menu_rules'] = array();
    $scan = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($theme, FilesystemIterator::SKIP_DOTS));
    foreach ($scan as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'css') continue;
        $relative = substr($file->getPathname(), strlen($theme)+1);
        $styles = @file_get_contents($file->getPathname());
        $r['css_files'][] = array('file'=>$relative,'bytes'=>strlen((string)$styles));
        if (!is_string($styles)) continue;
        preg_match_all('/.{0,120}(?:bkp-menu|bkp-navigation|sub-menu|menu-item-has-children).{0,260}/iu',$styles,$s);
        if ($s[0]) $r['css_menu_rules'][$relative]=array_slice($s[0],0,38);
    }
    foreach (array('/sitemap-1.xml','/image-sitemap-1.xml','/wp-sitemap-posts-page-1.xml') as $endpoint) {
        $response = wp_remote_get(home_url($endpoint),array('timeout'=>7,'redirection'=>3,'limit_response_size'=>240000,'sslverify'=>false));
        if (is_wp_error($response)) {
            $r['sitemap_details'][$endpoint] = array('error'=>$response->get_error_message());
            continue;
        }
        $body = wp_remote_retrieve_body($response);
        preg_match_all('~<loc>([^<]+)</loc>~i',$body,$matches);
        $urls = array_map('html_entity_decode',$matches[1]);
        $r['sitemap_details'][$endpoint] = array('status'=>wp_remote_retrieve_response_code($response),'size'=>strlen($body),'url_count'=>count($urls),'urls'=>array_slice($urls,0,600));
    }
    wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_title'=>'TEMP BK Theme and SEO Audit 2026-11-23','post_name'=>'bk-theme-seo-audit-20261123','post_content'=>base64_encode(wp_json_encode($r,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)),'comment_status'=>'closed'));
});
