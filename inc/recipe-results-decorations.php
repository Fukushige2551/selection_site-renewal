<?php
/** Decorative assets follow rendered cards, never a fixed page height. */
function foods_recipe_results_decoration($asset, $placement) {
    $class = 'p-recipe-archive__decoration p-recipe-archive__decoration--' . $asset
        . ' p-recipe-archive__decoration--' . $placement;
    echo '<span class="' . esc_attr($class) . '" aria-hidden="true"></span>';
}

function foods_recipe_results_card_decorations($position) {
    // Positions are per page. Each breakpoint uses its own outer edge or gap.
    static $placements = [
        'mobile' => [2 => ['cup'], 4 => ['spoon'], 5 => ['bowl'], 7 => ['glove'], 8 => ['strainer', 'small-01', 'small-02']],
        'tablet' => [2 => ['cup'], 3 => ['glove'], 8 => ['bowl'], 9 => ['cutter']],
        'tab-design' => [2 => ['cup'], 3 => ['glove'], 7 => ['strainer', 'small-01'], 12 => ['bowl']],
        'desktop' => [3 => ['cup'], 4 => ['glove'], 9 => ['bowl'], 10 => ['strainer', 'small-01', 'small-02']],
    ];

    foreach ($placements as $viewport => $cards) {
        foreach ($cards[$position] ?? [] as $asset) {
            foods_recipe_results_decoration($asset, $viewport);
        }
    }
}
