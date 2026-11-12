<?php
/** Shared publication rules for flyer images and their recommended products. */
function foods_flyer_is_publication_active($fields, $today) {
    foreach (['start', 'end'] as $edge) {
        $value = ($fields['publish_' . $edge . '_date'] ?? '') ?: ($fields['flyer_' . $edge . '_date'] ?? '');
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        if (strlen($value) !== 8) { continue; }
        $date = DateTimeImmutable::createFromFormat('!Ymd', $value);
        if (!$date) { continue; }
        $day = $date->format('Ymd');
        if (($edge === 'start' && $today < $day) || ($edge === 'end' && $today > $day)) {
            return false;
        }
    }
    return true;
}

function foods_flyer_include_common_store($term_ids) {
    $common = get_term_by('name', '全店舗共通', 'flyer_store');
    if ($common && !is_wp_error($common)) {
        $term_ids[] = (int) $common->term_id;
    }
    return array_values(array_unique($term_ids));
}
