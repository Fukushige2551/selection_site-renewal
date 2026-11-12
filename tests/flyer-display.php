<?php
require __DIR__ . '/../inc/flyer-display.php';

function check($condition, $label) {
    if (!$condition) { throw new RuntimeException($label); }
}
$fields = ['publish_start_date' => '20261114', 'publish_end_date' => '20261115'];
foreach (['20261113' => false, '20261114' => true, '20261115' => true, '20261116' => false] as $day => $expected) {
    check(foods_flyer_is_publication_active($fields, (string) $day) === $expected, 'Publication boundary: ' . $day);
}
check(foods_flyer_is_publication_active($fields + ['flyer_start_date' => '20261101'], '20261112') === false, 'Publication dates take priority');
check(foods_flyer_is_publication_active(['flyer_start_date' => '20261114'], '20261112') === false, 'Fallback to flyer date');
check(foods_flyer_is_publication_active([], '20261112'), 'Undated flyer remains visible');
check(foods_flyer_is_publication_active(['publish_start_date' => '2026/11/14'], '20261114'), 'Formatted date');

// A scheduled newest flyer must not hide an older, currently active flyer.
$candidates = [$fields, ['publish_start_date' => '20261110', 'publish_end_date' => '20261113']];
$active = array_filter($candidates, static fn($candidate) => foods_flyer_is_publication_active($candidate, '20261112'));
check(array_key_first($active) === 1, 'Select active flyer behind scheduled newest flyer');

$common = (object) ['term_id' => 99];
function get_term_by($field, $name, $taxonomy) {
    global $common;
    check($field === 'name' && $name === '全店舗共通' && $taxonomy === 'flyer_store', 'Common classification lookup');
    return $common;
}
function is_wp_error($value) { return $value instanceof RuntimeException; }
check(foods_flyer_include_common_store([7]) === [7, 99], 'Store and common flyers included');
check(foods_flyer_include_common_store([]) === [99], 'Common flyer without store classification');
check(foods_flyer_include_common_store([99]) === [99], 'No duplicate classification');
$common = false;
check(foods_flyer_include_common_store([7]) === [7], 'Missing common classification preserves store');
$common = new RuntimeException('Lookup failed');
check(foods_flyer_include_common_store([7]) === [7], 'Lookup error preserves store');
echo "Flyer publication and common-store checks passed.\n";
