<?php
/**
 * Plugin Name: セレクション 店舗情報更新ツール
 * Description: 確認済み資料に基づく10店舗の差分確認・バックアップ・更新。
 * Version: 1.0.0
 */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/updater.php';
add_action('admin_menu', function () {
    add_management_page('店舗情報更新', '店舗情報更新', 'manage_options', 'foods-shop-update', 'fsu_page');
});
add_action('acf/init', function () {
    if (function_exists('acf_add_local_field_group') && !acf_get_field('field_foods_shop_atm_bank_name')) {
        acf_add_local_field_group([
            'key'=>'group_foods_shop_service_details', 'title'=>'施設・サービス補足',
            'fields'=>[['key'=>'field_foods_shop_atm_bank_name','label'=>'隣接ATMの銀行名','name'=>'atm_bank_name','type'=>'text']],
            'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'shop']]],
        ]);
    }
}, 20);
add_action('admin_post_fsu_apply', 'fsu_apply');
add_action('admin_post_fsu_backup', 'fsu_download');
