<?php
add_shortcode('cpi_posts', 'cpi_render_shortcode');

function cpi_render_shortcode($atts)
{
    $atts = shortcode_atts([
        'title' => 'Latest Posts',
        'count' => 5,
        'sort' => 'date',
        'ids' => ''
    ], $atts);

    $args = [
        'post_type' => 'post',
        'posts_per_page' => intval($atts['count']),
        'orderby' => $atts['sort'],
        'order' => 'DESC'
    ];

    if (!empty($atts['ids'])) {
        $ids = array_map('intval', explode(',', $atts['ids']));
        $args['post__in'] = $ids;
        $args['orderby'] = 'post__in';
    }

    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        return '';
    }

    wp_enqueue_style('cpi-style', plugin_dir_url(__FILE__) . '../style.css');

    ob_start();
?>


    <div class="cpi-wrapper">
        <h1 class="cpi-wrapper-title"><?php echo esc_html($atts['title']); ?></h1>

        <?php while ($query->have_posts()): $query->the_post(); ?>
            <?php
            $post_id = get_the_ID();
            $rating = get_post_meta($post_id, 'rating', true);
            $site_link = get_post_meta($post_id, 'site_link', true);
            $categories = get_the_category();
            $category_name = !empty($categories) ? esc_html($categories[0]->name) : '';
            ?>

            <div class="cpi-post">
                <?php if (has_post_thumbnail()): ?>
                    <?php echo get_the_post_thumbnail($post_id, 'medium', ['class' => 'cpi-post-image']); ?>
                <?php endif; ?>

                <div class="cpi-post-info">
                    <div class="cpi-post-content">

                        <?php if ($category_name): ?>
                            <p class="cpi-post-category cpi-text"><?php echo $category_name; ?></p>
                        <?php endif; ?>
                        <h2 class="cpi-post-title"><?php echo esc_html(get_the_title()); ?></h2>
                        
                    </div>

                    <div class="cpi-post-meta">
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="cpi-post-permalink">Read more</a>
                        <div class="cpi-meta-actions">

                            <?php if ($rating): ?>
                                <p class="cpi-post-rating cpi-text">⭐ <?php echo esc_html($rating); ?></p>
                            <?php endif; ?>
                            <?php if ($site_link): ?>
                                <a href="<?php echo esc_url($site_link); ?>" target="_blank" rel="nofollow" class="cpi-post-link">Visit site</a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

<?php
    wp_reset_postdata();
    return ob_get_clean();
}
