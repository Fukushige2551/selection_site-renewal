<?php
$config = $args['config'];
$block_class = $args['block_class'];
$message = $config['message'];
?>
<section class="<?php echo esc_attr($block_class); ?>__message">
    <div class="<?php echo esc_attr($block_class); ?>__message__bg">
        <div class="<?php echo esc_attr($block_class); ?>__message__header">
            <div class="<?php echo esc_attr($block_class); ?>__message--bg">
                <div class="<?php echo esc_attr($block_class); ?>__message--shape" aria-hidden="true">
                    <span class="<?php echo esc_attr($block_class); ?>__message--shapeBase"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-top"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-bottom"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-l1"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-l2"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-r1"></span>
                    <span class="<?php echo esc_attr($block_class); ?>__message--blob <?php echo esc_attr($block_class); ?>__message--blob-r2"></span>
                </div>

                <img class="<?php echo esc_attr($block_class); ?>__message--bubbleSp" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['bubble_sp'])); ?>" alt="吹き出し">
                <img class="<?php echo esc_attr($block_class); ?>__message--bubblePc" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['bubble_pc'])); ?>" alt="吹き出し">
                <img class="<?php echo esc_attr($block_class); ?>__message--buyer" src="<?php echo esc_url(foods_get_select_detail_asset_url($config, $message['buyer_image'])); ?>" alt="バイヤー">
                <div class="<?php echo esc_attr($block_class); ?>__message--text">
                    <p class="<?php echo esc_attr($block_class); ?>__message--textSp"><?php echo wp_kses_post($message['text']); ?></p>
                    <p class="<?php echo esc_attr($block_class); ?>__message--textPc"><?php echo wp_kses_post($message['text_pc']); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
