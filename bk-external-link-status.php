<?php
/**
 * Plugin Name: BK Temporary Outbound Link Checker
 * Version: 1.0.0
 * Description: One-time read-only HEAD check of editorial outbound links, with strict domain allowlist. Writes a private draft only.
 */
defined('ABSPATH') || exit;
register_activation_hook(__FILE__, function() {
    $report=array('created_at'=>gmdate('c'),'links'=>array(),'error'=>null);
    $allow=array('www.ankaoutdoor.com','knifesteelnerds.com','www.linkedin.com','www.bohler-edelstahl.com','www.instagram.com');
    $posts=get_posts(array('post_type'=>array('page','post','inceleme','celik','rehber'),'post_status'=>'publish','posts_per_page'=>-1,'suppress_filters'=>false));
    $unique=array();
    foreach($posts as $p) {
        preg_match_all('~<a\b[^>]*href\s*=\s*(?:"([^"]+)"|\'([^\']+)\')~iu',(string)$p->post_content,$matches,PREG_SET_ORDER);
        foreach($matches as $m) {
            $url=html_entity_decode($m[1]!==''?$m[1]:($m[2]??''),ENT_QUOTES|ENT_HTML5,'UTF-8');
            $host=strtolower((string)parse_url($url,PHP_URL_HOST));
            if(!in_array($host,$allow,true)||!str_starts_with($url,'https://'))continue;
            if(!isset($unique[$url]))$unique[$url]=array('sources'=>array(),'url'=>$url);
            $unique[$url]['sources'][$p->post_name]=true;
        }
    }
    if (!function_exists('curl_multi_init')) $report['error']='curl_multi_unavailable';
    else {
        $batches=array_chunk(array_values($unique),8);
        foreach($batches as $batch) {
            $mh=curl_multi_init();
            $handles=array();
            foreach($batch as $entry) {
                $ch=curl_init($entry['url']);
                curl_setopt_array($ch,array(
                    CURLOPT_NOBODY=>true,
                    CURLOPT_FOLLOWLOCATION=>true,
                    CURLOPT_MAXREDIRS=>4,
                    CURLOPT_CONNECTTIMEOUT=>3,
                    CURLOPT_TIMEOUT=>7,
                    CURLOPT_RETURNTRANSFER=>true,
                    CURLOPT_USERAGENT=>'BicakKurdu-Link-Audit/1.0',
                    CURLOPT_SSL_VERIFYPEER=>true,
                ));
                curl_multi_add_handle($mh,$ch);
                $handles[]=array('handle'=>$ch,'entry'=>$entry);
            }
            $active=null;$cycles=0;
            do {
                $status=curl_multi_exec($mh,$active);
                if ($active && $status===CURLM_OK) curl_multi_select($mh,0.5);
                $cycles++;
            } while ($active && $status===CURLM_OK && $cycles<150);
            foreach($handles as $rec) {
                $ch=$rec['handle'];$entry=$rec['entry'];
                $report['links'][]=array(
                    'url'=>$entry['url'],'sources'=>array_keys($entry['sources']),
                    'status'=>(int)curl_getinfo($ch,CURLINFO_HTTP_CODE),
                    'effective'=>(string)curl_getinfo($ch,CURLINFO_EFFECTIVE_URL),
                    'error'=>mb_substr((string)curl_error($ch),0,180),
                    'duration_ms'=>(int)round(curl_getinfo($ch,CURLINFO_TOTAL_TIME)*1000),
                );
                curl_multi_remove_handle($mh,$ch);curl_close($ch);
            }
            curl_multi_close($mh);
        }
    }
    $report['found_urls']=count($unique);
    $encoded=base64_encode(wp_json_encode($report,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE));
    wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_title'=>'TEMP BK External Link HTTP Status 2026-11-24','post_name'=>'bk-external-http-audit-20261124','post_content'=>$encoded,'comment_status'=>'closed'));
});
