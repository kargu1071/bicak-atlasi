<?php
/** Plugin Name: BK Temporary Sitemap Verify
 * Version: 1.0.1
 * Description: One-time private audit of Jetpack XML sitemap content.
 */
defined('ABSPATH') || exit;
register_activation_hook(__FILE__,function(){
 $paths=array('/sitemap.xml','/sitemap-1.xml','/robots.txt');
 $out=array('timestamp'=>gmdate('c'),'reports'=>array());
 $out['jetpack_previous_report']=get_option('bk_jetpack_sitemap_fix_report_20261123',array());
 $out['cron_before']=array('hook_exists'=>has_action('jp_sitemap_cron_hook'),'next'=>wp_next_scheduled('jp_sitemap_cron_hook'));
 $out['filter_active']=has_filter('jetpack_page_sitemap_other_urls');
 if(has_action('jp_sitemap_cron_hook')) {
    try { do_action('jp_sitemap_cron_hook'); $out['cron_invocation']='completed'; }
    catch(Throwable $e){$out['cron_invocation']='failed';$out['cron_error']=mb_substr($e->getMessage(),0,450);}
 } else $out['cron_invocation']='missing_hook';
 $out['cron_after']=array('next'=>wp_next_scheduled('jp_sitemap_cron_hook'));

 foreach($paths as $path){
  $r=wp_remote_get(home_url($path),array('timeout'=>12,'redirection'=>2,'limit_response_size'=>400000,'sslverify'=>true,'headers'=>array('Cache-Control'=>'no-cache')));
  if(is_wp_error($r)){$out['reports'][$path]=array('error'=>$r->get_error_message());continue;}
  $body=wp_remote_retrieve_body($r);preg_match_all('~<loc>([^<]+)</loc>~i',$body,$loc);
  $urls=array_map('html_entity_decode',$loc[1]);
  $out['reports'][$path]=array(
   'status'=>(int)wp_remote_retrieve_response_code($r),
   'content_type'=>wp_remote_retrieve_header($r,'content-type'),
   'count'=>count($urls),
   'archive_urls'=>array_values(array_filter($urls,static function($u){return str_contains($u,'/kategori/')||str_contains($u,'/celik-gruplari/');})),
   'author_url_present'=>in_array(home_url('/yazar/'),$urls,true),
   'vosteed_present'=>in_array(home_url('/markalar/vosteed/'),$urls,true),
   'glock_present'=>in_array(home_url('/glock-bicak-rehberi/'),$urls,true),
   'uncategorized_present'=>in_array(home_url('/category/uncategorized/'),$urls,true),
   'preview'=>substr($body,0,1400),
  );
 }
 wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_title'=>'TEMP BK Sitemap Verification 20261124','post_name'=>'bk-sitemap-verify-20261124','post_content'=>base64_encode(wp_json_encode($out,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)),'comment_status'=>'closed'));
});
