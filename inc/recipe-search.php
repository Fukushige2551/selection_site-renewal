<?php
/**
 * Search only the recipe archive query, including its registered classifications.
 */
function foods_recipe_search_where($where, $query) {
    $keyword = $query->get('foods_recipe_search');
    if (!is_string($keyword) || $keyword === '' || $query->get('post_type') !== 'recipe') {
        return $where;
    }

    global $wpdb;
    $like = '%' . $wpdb->esc_like($keyword) . '%';

    // EXISTS avoids duplicate recipes when several terms or ingredients match.
    return $where . $wpdb->prepare(
        " AND (
            {$wpdb->posts}.post_title LIKE %s
            OR EXISTS (
                SELECT 1 FROM {$wpdb->term_relationships} AS recipe_relationship
                INNER JOIN {$wpdb->term_taxonomy} AS recipe_taxonomy
                    ON recipe_taxonomy.term_taxonomy_id = recipe_relationship.term_taxonomy_id
                INNER JOIN {$wpdb->terms} AS recipe_term
                    ON recipe_term.term_id = recipe_taxonomy.term_id
                WHERE recipe_relationship.object_id = {$wpdb->posts}.ID
                    AND recipe_taxonomy.taxonomy IN ('recipe_category', 'recipe_main_ingredient', 'recipe_tag')
                    AND recipe_term.name LIKE %s
            )
            OR EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} AS recipe_ingredient
                WHERE recipe_ingredient.post_id = {$wpdb->posts}.ID
                    AND recipe_ingredient.meta_key REGEXP %s
                    AND recipe_ingredient.meta_value LIKE %s
            )
        )",
        $like,
        $like,
        '^recipe_ingredients_[0-9]+_items_[0-9]+_(compound_items_[0-9]+_)?ingredient_name$',
        $like
    );
}
