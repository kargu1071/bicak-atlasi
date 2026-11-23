<?php
/**
 * Plugin Name: BıçakKurdu - Theme Primary Menu Native Migration
 * Description: One-time migration of the active theme's hardcoded header navigation to the registered WordPress primary menu. Stores the original header for rollback.
 * Version: 1.0.0
 */
defined('ABSPATH') || exit;

register_activation_hook(__FILE__, function () {
    $report = array('when' => gmdate('c'), 'theme' => get_stylesheet());
    if (get_stylesheet() !== 'bicakkurdu-premium' || !has_nav_menu('primary')) {
        $report['status'] = 'blocked_wrong_theme_or_menu';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }

    $path = get_stylesheet_directory() . '/header.php';
    $old = is_readable($path) ? file_get_contents($path) : false;
    if (!is_string($old)) {
        $report['status'] = 'blocked_unreadable';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }
    if (strpos($old, 'bkp_native_primary_menu_20261123') !== false) {
        $report['status'] = 'already_native';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }
    if (strpos($old, '<span>Bileme</span>') === false || strpos($old, 'id="bkp-main-nav"') === false) {
        $report['status'] = 'blocked_original_header_not_matched';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }

    $native = <<<'BKP_NATIVE_HEADER'
<nav class="bkp-navigation" id="bkp-main-nav" aria-label="Ana gezinme">
  <?php /* bkp_native_primary_menu_20261123: WordPress primary menu; native dropdown CSS and JS. */
  $bkp_native_primary_icons = array(
      'Bıçaklar'        => 'sabit',
      'Çakılar'         => 'caki',
      'Bıçak Çelikleri' => 'steel',
      'Markalar'        => 'brand',
      'Kilit Sistemleri'=> 'lock',
      'Makaleler'       => 'sharpen',
  );
  $bkp_native_primary_icon_filter = static function($item_output, $item, $depth, $args) use ($bkp_native_primary_icons) {
      if ((int)$depth !== 0 || !is_object($args) || ($args->theme_location ?? '') !== 'primary') {
          return $item_output;
      }
      $title = trim(wp_strip_all_tags((string)$item->title));
      $key = $bkp_native_primary_icons[$title] ?? null;
      if (!$key || !function_exists('bkp_nav_icon')) {
          return $item_output;
      }
      $icon = bkp_nav_icon($key);
      if ($icon === '') return $item_output;
      return preg_replace('~(<a\b[^>]*>)~iu', '$1' . $icon, $item_output, 1);
  };
  add_filter('walker_nav_menu_start_el', $bkp_native_primary_icon_filter, 10, 4);
  wp_nav_menu(array(
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'bkp-menu bkp-menu--simple',
      'menu_id'        => 'bkp-native-primary-menu',
      'depth'          => 2,
      'fallback_cb'    => false,
      'link_before'    => '<span>',
      'link_after'     => '</span>',
      'item_spacing'   => 'discard',
  ));
  remove_filter('walker_nav_menu_start_el', $bkp_native_primary_icon_filter, 10);
  ?>
</nav>
BKP_NATIVE_HEADER;

    $matches = 0;
    $pattern = '~<nav class="bkp-navigation" id="bkp-main-nav" aria-label="Ana gezinme">[\s\S]*?</nav>~u';
    $new = preg_replace_callback($pattern, static function() use ($native) { return $native; }, $old, 1, $matches);
    if ($matches !== 1 || !is_string($new) || strpos($new, 'bkp_native_primary_menu_20261123') === false || strpos($new, '<span>Bileme</span>') !== false) {
        $report['status'] = 'blocked_patch_validation';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }
    if (!is_writable($path)) {
        $report['status'] = 'blocked_theme_header_not_writable';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }
    // Retain a database backup even after the migration plugin is deactivated.
    update_option('bk_native_primary_menu_header_backup_20261123', base64_encode($old), false);
    $bytes = @file_put_contents($path, $new, LOCK_EX);
    if ($bytes !== strlen($new)) {
        @file_put_contents($path, $old, LOCK_EX);
        $report['status'] = 'failed_write_reverted';
        update_option('bk_native_primary_menu_migration_20261123', $report, false);
        return;
    }
    if (function_exists('opcache_invalidate')) @opcache_invalidate($path, true);

    // The previous HTML-buffer menu shim would rewrite the Bileme child item.
    // Disable it ONLY after the native header was safely installed.
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    foreach (get_plugins() as $filename => $item_info) {
        if (basename($filename) === 'bk-makaleler-nav.php' && is_plugin_active($filename)) {
            deactivate_plugins($filename, true);
            $report['retired_navigation_plugin'] = $filename;
        }
    }
    $report['status'] = 'success_native_primary';
    $report['old_sha256'] = hash('sha256', $old);
    $report['new_sha256'] = hash('sha256', $new);
    $report['bytes'] = $bytes;
    update_option('bk_native_primary_menu_migration_20261123', $report, false);
});
