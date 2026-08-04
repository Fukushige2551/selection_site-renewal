<?php
/*
Template Name: Select Meat
*/

require_once get_template_directory() . '/inc/select-detail-config.php';

$select_detail_config = foods_get_select_detail_config('meat');

get_template_part(
    'template-parts/select-detail/content',
    null,
    ['config' => $select_detail_config]
);
