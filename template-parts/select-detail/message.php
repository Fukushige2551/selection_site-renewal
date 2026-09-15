<?php
$config = $args['config'];
$block_class = $args['block_class'];
$message = $config['message'];
$message_background_decorations = array_values(array_filter(
    $config['about']['background_decorations'] ?? [],
    static function ($decoration) {
        return isset($decoration['key']) && 'wave' === $decoration['key'];
    }
));
?>
<section class="<?php echo esc_attr($block_class); ?>__message">
    <?php foreach ($message_background_decorations as $decoration) : ?>
        <?php if (!empty($decoration['src'])) : ?>
            <img class="<?php echo esc_attr($block_class . '__message--backgroundDecoration ' . $block_class . '__message--backgroundDecoration-' . sanitize_html_class($decoration['key']) . ' ' . $block_class . '__message--backgroundDecoration-legacyAnchor'); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="<?php echo esc_attr($block_class); ?>__message--bg">
                <div class="<?php echo esc_attr($block_class); ?>__message--shape" aria-hidden="true">
                    <span class="<?php echo esc_attr($block_class); ?>__message--shapeBase"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--shapeTop"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--shapeMiddle"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--shapeBottom"></span>
                </div>

                <img class="<?php echo esc_attr($block_class); ?>__message--bubbleSp" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['bubble_sp'])); ?>" alt="吹き出し">
                <img class="<?php echo esc_attr($block_class); ?>__message--bubblePc" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['bubble_pc'])); ?>" alt="吹き出し">
                <?php if (!empty($message['catch'])) : ?>
                    <div class="<?php echo esc_attr($block_class); ?>__message--heading">
                        <h2 class="<?php echo esc_attr($block_class); ?>__message--title">バイヤーメッセージ</h2>
                        <?php if (!empty($message['decoration'])) : ?>
                            <img class="<?php echo esc_attr($block_class); ?>__message--decoration" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['decoration'])); ?>" alt="">
                        <?php endif; ?>
                        <p class="<?php echo esc_attr($block_class); ?>__message--catch"><?php echo wp_kses_post($message['catch']); ?></p>
                    </div>
                <?php endif; ?>
                <img class="<?php echo esc_attr($block_class); ?>__message--buyer" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['buyer_image'])); ?>" alt="バイヤー">
                <div class="<?php echo esc_attr($block_class); ?>__message--text">
                    <p class="<?php echo esc_attr($block_class); ?>__message--textSp"><?php echo wp_kses_post($message['text']); ?></p>
                    <p class="<?php echo esc_attr($block_class); ?>__message--textPc"><?php echo wp_kses_post($message['text_pc']); ?></p>
                </div>
                <?php foreach (($message['foreground_decorations'] ?? []) as $decoration) : ?>
                    <?php if (!empty($decoration['key']) && !empty($decoration['src'])) : ?>
                        <?php if (!empty($decoration['src_sp'])) : ?><picture><?php endif; ?>
                            <?php if (!empty($decoration['src_sp'])) : ?>
                                <source media="(max-width: 767px)" srcset="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src_sp'])); ?>">
                            <?php endif; ?>
                            <img class="<?php echo esc_attr($block_class . '__message--foregroundDecoration ' . $block_class . '__message--foregroundDecoration-' . sanitize_html_class($decoration['key'])); ?>" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $decoration['src'])); ?>" alt="<?php echo esc_attr($decoration['alt'] ?? ''); ?>">
                        <?php if (!empty($decoration['src_sp'])) : ?></picture><?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!empty($message['corner_decoration'])) : ?>
                    <img class="<?php echo esc_attr($block_class); ?>__message--cornerDecoration" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['corner_decoration'])); ?>" alt="">
                <?php endif; ?>
    </div>
</section>
