<?php
/**
 * Plugin Name: BıçakKurdu - Geçici Tam İçerik Bağlantı Denetimi
 * Version: 1.0.1
 * Description: One-time read-only audit of ALL published pages, CPTs, taxonomies and editorial hyperlinks. Stores a private draft report and makes no public changes.
 */
defined('ABSPATH') || exit;

function bk_audit_link_from_href($href) {
    $href = trim(html_entity_decode((string)$href, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($href === '' || $href[0] === '#' || preg_match('~^(?:mailto:|tel:|javascript:|data:)~i',$href)) {
        return array('type'=>'other','value'=>$href);
    }
    $host = parse_url($href, PHP_URL_HOST);
    if ($host && !in_array(strtolower((string)$host), array('bicakkurdu.com','www.bicakkurdu.com'),true)) {
        return array('type'=>'external','value'=>$href, 'host'=>strtolower($host));
    }
    $path = parse_url($href, PHP_URL_PATH);
    if (!is_string($path) || $path === '') $path = '/';
    if ($path[0] !== '/') return array('type'=>'relative','value'=>$href);
    return array('type'=>'internal','value'=>'/'.trim(rawurldecode($path),'/').(trim($path,'/')===''?'':'/'));
}
function bk_audit_visible_words($html) {
    $html = preg_replace('~<!--[\s\S]*?-->~u',' ',$html);
    $html = preg_replace('~<(style|script|noscript)\b[^>]*>[\s\S]*?</\1>~iu',' ',$html);
    $text = trim(preg_replace('/\s+/u',' ',wp_strip_all_tags(strip_shortcodes($html))));
    return $text === '' ? 0 : count(preg_split('/\s+/u',$text,-1,PREG_SPLIT_NO_EMPTY));
}
register_activation_hook(__FILE__, function() {
    $items = get_posts(array(
        'post_type'=>array('page','post','inceleme','celik','rehber'),
        'post_status'=>'publish',
        'posts_per_page'=>-1,
        'orderby'=>'ID',
        'order'=>'ASC',
        'suppress_filters'=>false,
    ));
    $report = array('created_at'=>gmdate('c'),'host'=>home_url('/'),'items'=>array(),'taxonomy'=>array(),'errors'=>array());
    foreach ($items as $post) {
        $raw=(string)$post->post_content;
        $links=array();
        preg_match_all('~<a\b([^>]*?)\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\')([^>]*)>([\s\S]*?)</a>~iu',$raw,$matches,PREG_SET_ORDER);
        foreach ($matches as $m) {
            $href=$m[2]!=='' ? $m[2] : ($m[3]??'');
            $info=bk_audit_link_from_href($href);
            $attr=($m[1]??'').' '.($m[4]??'');
            $rel='';
            if (preg_match('~\brel\s*=\s*(?:"([^"]+)"|\'([^\']+)\')~iu',$attr,$rm)) $rel=$rm[1]??($rm[2]??'');
            $anchor=trim(preg_replace('/\s+/u',' ',wp_strip_all_tags($m[5]??'')));
            $links[]=array('type'=>$info['type'],'href'=>$href,'target'=>$info['value'],'host'=>$info['host']??null,'anchor'=>mb_substr($anchor,0,130),'rel'=>$rel);
        }
        preg_match_all('~<h([1-6])\b[^>]*>([\s\S]*?)</h\1>~iu',$raw,$hh,PREG_SET_ORDER);
        $headings=array();
        foreach($hh as $m)$headings[]=array('level'=>(int)$m[1],'text'=>mb_substr(trim(wp_strip_all_tags($m[2])),0,100));
        $canonical=get_permalink($post->ID);
        $description=get_post_meta($post->ID,'advanced_seo_description',true);
        $seo_title=get_post_meta($post->ID,'jetpack_seo_html_title',true);
        $report['items'][]=array(
            'id'=>$post->ID,'type'=>$post->post_type,'title'=>get_the_title($post->ID),
            'url'=>$canonical,'slug'=>$post->post_name,'date'=>$post->post_date,
            'words'=>bk_audit_visible_words($raw),'content_bytes'=>strlen($raw),
            'desc_length'=>mb_strlen((string)$description),
            'desc_hash'=>md5((string)$description),
            'desc_excerpt'=>mb_substr((string)$description,0,195),
            'modified'=>$post->post_modified,
            'author_id'=>$post->post_author,
            'seo_title_length'=>mb_strlen((string)$seo_title),
            'seo_title'=>mb_substr((string)$seo_title,0,155),
            'has_featured_image'=>has_post_thumbnail($post->ID),
            'headings'=>$headings,'links'=>$links,
        );
    }
    foreach (array('bicak_kategori','bk_celik_grubu','bk_rehber_konu','category','post_tag') as $tax) {
        if (!taxonomy_exists($tax)) continue;
        $terms=get_terms(array('taxonomy'=>$tax,'hide_empty'=>false));
        if (is_wp_error($terms)) continue;
        foreach($terms as $t) {
            $url=get_term_link($t);
            if (is_wp_error($url))continue;
            $report['taxonomy'][]=array('taxonomy'=>$tax,'id'=>$t->term_id,'name'=>$t->name,'url'=>$url,'count'=>$t->count,'descriptionWords'=>bk_audit_visible_words((string)$t->description),'parent'=>$t->parent);
        }
    }
    $encoded=base64_encode(wp_json_encode($report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
    $id=wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_title'=>'TEMP BK Full Internal External Link Audit 2026-11-24','post_name'=>'bk-full-link-audit-20261124','post_content'=>$encoded,'comment_status'=>'closed'),true);
    update_option('bk_full_link_audit_report_20261124',array('id'=>is_wp_error($id)?0:$id,'items'=>count($report['items']),'taxonomies'=>count($report['taxonomy']),'bytes'=>strlen($encoded)),false);
});
