<?php
/** SEO-SD11: one server-rendered JSON-LD graph, shared by existing and new pages. */
function foods_schema_add($node) {
    if (!empty($node['@id']) && !empty($node['@type'])) {
        $GLOBALS['foods_schema_nodes'][$node['@id']] = $node;
    }
}

function foods_schema_enabled() {
    return !is_admin() && !is_feed() && !is_404() && !is_preview()
        && !is_page_template(['page-contact-confirm.php', 'page-contact-thanks.php', 'page-application-result.php', 'page-company-business-contact-result.php']);
}

/** Verified company identity, also consumed by the visible company profile. */
function foods_company_identity() {
    return [
        'name' => '株式会社 セレクション',
        'address_text' => '〒272-0132 千葉県市川市湊新田1丁目6番8号',
        'address' => ['@type' => 'PostalAddress', 'postalCode' => '272-0132', 'addressRegion' => '千葉県', 'addressLocality' => '市川市', 'streetAddress' => '湊新田1丁目6番8号', 'addressCountry' => 'JP'],
    ];
}

/** Capture only a breadcrumb fragment; return its original HTML unchanged. */
function foods_schema_breadcrumb_html($html) {
    if (!foods_schema_enabled() || is_front_page() || !class_exists('DOMDocument')) {
        return $html;
    }
    $doc = new DOMDocument();
    if (!$doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
        return $html;
    }
    $xpath = new DOMXPath($doc);
    $parts = [];
    foreach ($xpath->query('//nav//a | //nav//span[not(descendant::a) and not(descendant::span)]') as $element) {
        $text = foods_seo_text($element->textContent);
        if ($element->getAttribute('aria-hidden') === 'true' || $text === '' || in_array($text, ['›', '>', '＞'], true)) {
            continue;
        }
        $url = $element->tagName === 'a' ? esc_url_raw($element->getAttribute('href'), ['http', 'https']) : '';
        $parts[] = ['name' => $text, 'url' => $url];
    }
    if (count($parts) < 2) {
        return $html;
    }
    $items = [];
    foreach ($parts as $index => $part) {
        $item = ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $part['name']];
        if ($part['url'] !== '') {
            $item['item'] = $part['url'];
        } elseif ($index !== count($parts) - 1) {
            // This intermediate label is visibly unlinked on the company profile.
            if ($part['name'] !== '企業情報') {
                return $html; // Do not invent unknown intermediate destinations.
            }
            $item['item'] = home_url('/company/');
        }
        $items[] = $item;
    }
    $url = is_singular() ? get_permalink() : get_post_type_archive_link(get_query_var('post_type'));
    if ($url) {
        foods_schema_add(['@type' => 'BreadcrumbList', '@id' => $url . '#breadcrumb', 'itemListElement' => $items]);
    }
    return $html;
}

/** Parse a complete registered time range; never infer missing hours. */
function foods_schema_parse_hours($value) {
    $value = strtr(foods_seo_text($value), ['：' => ':', '～' => '~', '〜' => '~']);
    if (!preg_match('/^(\d{1,2}):(\d{2})\s*~\s*(\d{1,2}):(\d{2})$/u', $value, $matches)) {
        return null;
    }
    if ((int) $matches[1] > 23 || (int) $matches[3] > 23 || (int) $matches[2] > 59 || (int) $matches[4] > 59) {
        return null;
    }
    return [
        'opens' => sprintf('%02d:%02d', $matches[1], $matches[2]),
        'closes' => sprintf('%02d:%02d', $matches[3], $matches[4]),
    ];
}

/** Registered hours and the annually recurring January closure confirmed by the owner. */
function foods_schema_shop_hours($fields, $statuses = []) {
    $result = [];
    $temporarily_closed = false;
    foreach ($statuses as $status) {
        if (foods_seo_text($status['name'] ?? '') === '一時休業中') {
            $temporarily_closed = true;
            break;
        }
    }
    $hours = foods_schema_parse_hours($fields['business_hours'] ?? '');
    $note = foods_seo_text($fields['business_hours_note'] ?? '');
    $days = array_map(static fn($day) => 'https://schema.org/' . $day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
    $weekend_close = null;
    if (preg_match('/^[（(]土日(\d{1,2}:\d{2})閉店[）)]$/u', $note, $matches)) {
        $weekend = foods_schema_parse_hours('00:00~' . $matches[1]);
        $weekend_close = $weekend['closes'] ?? null;
        $known_note = $weekend !== null;
    } else {
        // Hananoi's registered normal hours were explicitly reconfirmed with this notice.
        $known_note = $note === '' || $note === '※コロナウイルス感染拡大防止ならびにお客様・従業員の安全確保の為 営業時間を変更させていただく場合がございます。';
    }
    if (!$temporarily_closed && $hours && $known_note) {
        $result['openingHoursSpecification'] = [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $weekend_close !== null ? array_slice($days, 0, 5) : $days,
            'opens' => $hours['opens'],
            'closes' => $hours['closes'],
        ]];
        if ($weekend_close !== null) {
            $result['openingHoursSpecification'][] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array_slice($days, 5),
                'opens' => $hours['opens'],
                'closes' => $weekend_close,
            ];
        }
    }
    if (preg_match('/^1月1日・1月2日\s*店休$/u', foods_seo_text($fields['regular_holiday'] ?? ''))) {
        // Refresh date-specific overrides every request; cover both sides of the year boundary.
        $year = (int) current_datetime()->format('Y');
        foreach ([$year, $year + 1] as $closure_year) {
            $result['specialOpeningHoursSpecification'][] = [
                '@type' => 'OpeningHoursSpecification',
                'validFrom' => sprintf('%04d-01-01', $closure_year),
                'validThrough' => sprintf('%04d-01-02', $closure_year),
                'opens' => '00:00',
                'closes' => '00:00',
            ];
        }
    }
    return $result;
}

/** Split only municipality boundaries verified against the ten registered shop addresses. */
function foods_schema_shop_address($address, $zip = '') {
    $postal = ['@type' => 'PostalAddress', 'streetAddress' => $address];
    if ($zip !== '') { $postal['postalCode'] = $zip; }
    $municipalities = [
        '千葉県' => ['市川市', '船橋市', '柏市', '松戸市'],
        '埼玉県' => ['三郷市', '八潮市'],
        '東京都' => ['足立区'],
    ];
    foreach ($municipalities as $region => $localities) {
        foreach ($localities as $locality) {
            $prefix = $region . $locality;
            if (str_starts_with($address, $prefix)) {
                $street = substr($address, strlen($prefix));
                if (trim($street) === '') { return $postal; }
                $postal['addressCountry'] = 'JP';
                $postal['addressRegion'] = $region;
                $postal['addressLocality'] = $locality;
                $postal['streetAddress'] = $street;
                return $postal;
            }
        }
    }
    return $postal; // Preserve an unrecognized address without guessing its subdivisions.
}

/** SD04: use the exact fields and real photo selected by the shop template. */
function foods_schema_shop($shop, $photo) {
    if (!foods_schema_enabled()) {
        return;
    }
    $fields = $shop['fields'] ?? [];
    $name = foods_seo_text($shop['title'] ?? '');
    $address = foods_seo_text($fields['address'] ?? '');
    if ($name === '' || $address === '') {
        return;
    }
    $zip = foods_seo_text($fields['postal_code'] ?? '');
    $postal = foods_schema_shop_address($address, $zip);
    $node = ['@type' => 'GroceryStore', '@id' => $shop['permalink'] . '#store', 'name' => $name,
        'url' => $shop['permalink'], 'address' => $postal,
        'parentOrganization' => ['@id' => home_url('/#organization')]];
    $telephone = foods_seo_text($fields['tel'] ?? '');
    if ($telephone !== '') {
        $node['telephone'] = $telephone;
    }
    if ($photo && esc_url_raw($photo, ['http', 'https'])) {
        $node['image'] = esc_url_raw($photo, ['http', 'https']);
    }
    $node = array_merge($node, foods_schema_shop_hours($fields, $shop['shop_status'] ?? []));
    foods_schema_add($node);
}

/** SD06: visible recipe data and explicitly confirmed provenance only. */
function foods_schema_recipe($recipe, $summary, $servings, $sections, $preparation, $steps) {
    if (!foods_schema_enabled()) {
        return;
    }
    $name = foods_seo_text($recipe['title'] ?? '');
    $image = esc_url_raw($recipe['image']['url'] ?? '', ['http', 'https']);
    if ($name === '' || $image === '') {
        return; // A recipe without its name or actual dish image is incomplete.
    }
    $node = ['@type' => 'Recipe', '@id' => $recipe['permalink'] . '#recipe', 'url' => $recipe['permalink'], 'name' => $name, 'image' => $image];
    $author = foods_seo_text($recipe['author_organization'] ?? '');
    if ($author !== '') {
        $node['author'] = ['@type' => 'Organization', 'name' => $author];
        $author_url = esc_url_raw($recipe['author_url'] ?? '', ['http', 'https']);
        if ($author_url !== '') { $node['author']['url'] = $author_url; }
    }
    if (($recipe['time_scope'] ?? '') === 'total' && preg_match('/^([1-9][0-9]*)分$/u', foods_seo_text($recipe['cooking_time'] ?? ''), $duration)) {
        $node['totalTime'] = 'PT' . $duration[1] . 'M';
    }
    $publication = foods_recipe_confirmed_date($recipe['confirmed_publication'] ?? '');
    if ($publication !== '') { $node['datePublished'] = $publication; }
    if (foods_seo_text($summary) !== '') {
        $node['description'] = foods_seo_text($summary);
    }
    if (foods_seo_text($servings) !== '') {
        $node['recipeYield'] = foods_seo_text($servings);
    }
    // Registered dish categories, not the ingredient label used above the title.
    $categories = [];
    foreach ($recipe['terms']['recipe_categories'] ?? [] as $term) {
        $label = foods_seo_text($term['name'] ?? '');
        if ($label !== '') { $categories[$label] = $label; }
    }
    if ($categories) {
        $node['recipeCategory'] = implode(', ', $categories);
    }
    // Reuse displayed tags and ingredients; do not duplicate category tags as keywords.
    $keywords = [];
    foreach (['recipe_tags', 'recipe_main_ingredients', 'tags'] as $source) {
        foreach ($recipe['terms'][$source] ?? [] as $term) {
            $label = foods_seo_text($term['name'] ?? '');
            if ($label !== '' && !isset($categories[$label])) { $keywords[$label] = $label; }
        }
    }
    if ($keywords) {
        $node['keywords'] = implode(', ', $keywords);
    }
    $ingredients = [];
    foreach ($sections as $section) {
        foreach ($section['items'] ?? [] as $item) {
            $is_compound = ($item['type'] ?? '') === 'compound';
            $rows = $is_compound ? ($item['items'] ?? []) : (($item['type'] ?? '') === 'item' ? [$item] : []);
            foreach ($rows as $row) {
                $line = foods_seo_text(implode(' ', array_filter([$row['name'] ?? '', $row['amount'] ?? '', $row['note'] ?? ''], static fn($v) => $v !== '')));
                if ($line !== '') {
                    $group = $is_compound ? foods_seo_text($item['group_name'] ?? '') : '';
                    $ingredients[] = $group !== '' ? $group . ' ' . $line : $line;
                }
            }
        }
    }
    if ($ingredients) {
        $node['recipeIngredient'] = $ingredients;
    }
    $instructions = [];
    if (foods_seo_text($preparation) !== '') {
        $instructions[] = ['@type' => 'HowToStep', 'text' => foods_seo_text($preparation),
            'url' => $recipe['permalink'] . '#recipe-preparation'];
    }
    foreach ($steps as $index => $step) {
        $text = foods_seo_text($step['text'] ?? '');
        if ($text !== '') {
            $instructions[] = ['@type' => 'HowToStep', 'text' => $text,
                'url' => $recipe['permalink'] . '#recipe-step-' . ($index + 1)];
        }
    }
    if ($instructions) {
        // A changed instruction must never inherit an old summary by its row number.
        $names = [];
        foreach ((array) ($recipe['instruction_names'] ?? []) as $row) {
            if (!is_array($row)) { continue; }
            $source_text = foods_seo_text($row['source_text'] ?? '');
            $step_name = foods_seo_text($row['step_name'] ?? '');
            if ($source_text !== '' && $step_name !== '') { $names[$source_text] = $step_name; }
        }
        foreach ($instructions as &$instruction) {
            if (isset($names[$instruction['text']])) {
                $instruction['name'] = $names[$instruction['text']];
            }
        }
        unset($instruction);
        $node['recipeInstructions'] = $instructions;
    }
    $source = esc_url_raw($recipe['source_url'] ?? '', ['http', 'https']);
    if ($source !== '') {
        $node['citation'] = $source;
    }
    foods_schema_add($node);
}

/** SD07: only recipes actually shown on this ordinary archive page. */
function foods_schema_recipe_list($recipes, $is_search, $page_url) {
    if (!foods_schema_enabled() || $is_search || !is_post_type_archive('recipe')) {
        return;
    }
    $items = [];
    $seen = [];
    foreach ($recipes as $recipe) {
        $url = esc_url_raw($recipe['permalink'] ?? '', ['http', 'https']);
        if ($url === '' || isset($seen[$url])) {
            continue;
        }
        $seen[$url] = true;
        $items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'url' => $url];
    }
    $url = esc_url_raw($page_url, ['http', 'https']);
    if ($items && $url !== '') {
        foods_schema_add(['@type' => 'ItemList', '@id' => $url . '#recipe-list', 'itemListElement' => $items]);
    }
}

/** Convert the displayed calendar date without inventing a time or timezone. */
function foods_schema_article_date($display_date) {
    if (!is_string($display_date) || !preg_match('/^(\d{4})\.(\d{2})\.(\d{2})$/D', $display_date, $parts)) {
        return '';
    }
    return checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])
        ? $parts[1] . '-' . $parts[2] . '-' . $parts[3] : '';
}

/** SD08: news detail only; author and modification date intentionally omitted. */
function foods_schema_article($news, $body_html) {
    if (!foods_schema_enabled() || !is_singular('news')) {
        return;
    }
    $headline = foods_seo_text($news['title'] ?? '');
    $url = esc_url_raw($news['permalink'] ?? '', ['http', 'https']);
    if ($headline === '' || $url === '') {
        return;
    }
    $company = foods_company_identity();
    $node = ['@type' => 'Article', '@id' => $url . '#article', 'url' => $url, 'headline' => $headline,
        'publisher' => ['@type' => 'Organization', '@id' => home_url('/#organization'), 'name' => $company['name']]];
    $copy = foods_seo_current_copy();
    if ($copy !== null && foods_seo_text($copy[1]) !== '') {
        $node['description'] = foods_seo_text($copy[1]);
    }
    $body = foods_seo_text(preg_replace('~<br\s*/?>|</(?:p|h[1-6]|li|tr|div)>~i', ' ', $body_html));
    if ($body !== '') {
        $node['articleBody'] = $body;
    }
    $date = foods_schema_article_date($news['display_date'] ?? '');
    if ($date !== '') {
        $node['datePublished'] = $date;
    }
    $image = esc_url_raw($news['image']['url'] ?? '', ['http', 'https']);
    if ($image !== '') {
        $node['image'] = $image;
    }
    foods_schema_add($node);
}

function foods_schema_output() {
    if (!foods_schema_enabled()) {
        return;
    }
    // Confirmed root-page hierarchy only; these pages intentionally have no visual breadcrumbs.
    $root_breadcrumb = null;
    if (is_page() && get_page_uri(get_queried_object_id()) === 'company') {
        $root_breadcrumb = ['url' => get_permalink(), 'name' => '企業情報'];
    } elseif (is_post_type_archive('shop')) {
        $root_breadcrumb = ['url' => get_post_type_archive_link('shop'), 'name' => 'チラシ・店舗情報一覧'];
    }
    if ($root_breadcrumb && $root_breadcrumb['url']) {
        foods_schema_add(['@type' => 'BreadcrumbList', '@id' => $root_breadcrumb['url'] . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'TOP', 'item' => home_url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $root_breadcrumb['name'], 'item' => $root_breadcrumb['url']],
            ]]);
    }
    if (is_front_page()) {
        foods_schema_add(['@type' => 'WebSite', '@id' => home_url('/#website'), 'url' => home_url('/'), 'name' => 'フーズマーケットセレクション', 'publisher' => ['@id' => home_url('/#organization')]]);
    }
    if (is_front_page() || is_page_template('page-company-about.php')) {
        $company = foods_company_identity();
        foods_schema_add(['@type' => 'Organization', '@id' => home_url('/#organization'), 'name' => $company['name'], 'url' => home_url('/'), 'address' => $company['address'], 'logo' => get_theme_file_uri('/img/component/selection-logo.svg')]);
    }
    $nodes = $GLOBALS['foods_schema_nodes'] ?? [];
    if (!$nodes) {
        return;
    }
    $json = wp_json_encode(['@context' => 'https://schema.org', '@graph' => array_values($nodes)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json !== false) {
        echo '<script type="application/ld+json" id="foods-structured-data">' . $json . '</script>' . "\n";
    }
}
add_action('wp_footer', 'foods_schema_output', 30);
