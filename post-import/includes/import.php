<?php
function cpi_import_posts_from_api()
{
    $response = wp_remote_get('https://my.api.mockaroo.com/posts.json', [
        'headers' => ['X-API-Key' => '413dfbf0']
    ]);

    if (is_wp_error($response)) return;

    $posts = json_decode(wp_remote_retrieve_body($response), true);
    if (!$posts) return;

    $admin_user = get_users(['role' => 'administrator', 'number' => 1]);
    if (empty($admin_user)) return;
    $author_id = $admin_user[0]->ID;

    foreach ($posts as $post_data) {
        if (get_page_by_title($post_data['title'], OBJECT, 'post')) continue;

        $category_id = get_cat_ID($post_data['category']);

        if ($category_id === 0) {
            $new_category = wp_insert_category([
                'cat_name' => $post_data['category'],
                'category_nicename' => sanitize_title($post_data['category'])
            ]);
            $category_id = $new_category['term_id'] ?? 0;
        }


        $post_id = wp_insert_post([
            'post_title' => sanitize_text_field($post_data['title']),
            'post_content' => wp_kses_post($post_data['content']),
            'post_status' => 'publish',
            'post_author' => $author_id,
            'post_category' => [$category_id],
            'post_date' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days')),
        ]);

        if (!$post_id) continue;

        if (!empty($post_data['site_link'])) {
            update_post_meta($post_id, 'site_link', esc_url_raw($post_data['site_link']));
        }

        if (!empty($post_data['rating'])) {
            update_post_meta($post_id, 'rating', floatval($post_data['rating']));
        }

        if (!empty($post_data['image'])) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $image_url = esc_url_raw($post_data['image']);
            media_sideload_image($image_url, $post_id, null, 'src');

            $attachments = get_attached_media('image', $post_id);
            if (!empty($attachments)) {
                $attachment = array_shift($attachments);
                set_post_thumbnail($post_id, $attachment->ID);
            }
        }
    }
}
