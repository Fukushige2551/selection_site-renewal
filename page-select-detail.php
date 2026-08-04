<?php
/*
Template Name: Select Detail
*/

require_once get_template_directory() . '/inc/select-detail-config.php';

$select_detail_config = foods_get_select_detail_config_by_page_slug((string) get_post_field('post_name', get_queried_object_id()));

if (!$select_detail_config) {
    status_header(404);
    nocache_headers();
    include get_404_template();
    return;
}

get_template_part(
    'template-parts/select-detail/content',
    null,
    ['config' => $select_detail_config]
);
