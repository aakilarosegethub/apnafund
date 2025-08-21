<?php
/**
 * Plugin Name: Custom Posts API
 * Description: REST route to fetch recent posts (title, image URL, short description) OR a single post via ?url=... (permalink). No version in route.
 * Version:     1.0.0
 * Author:      Your Name
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * Register REST route:
 * GET /wp-json/custom/posts
 *   ?count=10&offset=0&category=news|12&search=keyword
 *   ?url=https://your-site.com/sample-post/
 */
add_action('rest_api_init', function () {
    register_rest_route('custom', '/posts', [
        'methods'  => 'GET',
        'callback' => 'custom_posts_api_handler',
        'permission_callback' => '__return_true',
        'args' => [
            'count' => [
                'description' => 'Number of posts to return (1-100).',
                'type'        => 'integer',
                'default'     => 10,
                'minimum'     => 1,
                'maximum'     => 100,
                'sanitize_callback' => 'absint',
            ],
            'offset' => [
                'description' => 'Offset for pagination.',
                'type'        => 'integer',
                'default'     => 0,
                'sanitize_callback' => 'absint',
            ],
            'category' => [
                'description' => 'Category slug or ID to filter posts.',
                'type'        => 'string',
                'required'    => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'search' => [
                'description' => 'Keyword to search in posts.',
                'type'        => 'string',
                'required'    => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'url' => [
                'description' => 'If provided, returns a single post that matches this permalink.',
                'type'        => 'string',
                'required'    => false,
                'sanitize_callback' => 'esc_url_raw',
            ],
        ],
    ]);
});

/**
 * Main handler for /custom/posts
 */
function custom_posts_api_handler( WP_REST_Request $request ) {
    $url      = $request->get_param('url');
    $count    = max(1, min(100, (int) $request->get_param('count')));
    $offset   = max(0, (int) $request->get_param('offset'));
    $category = $request->get_param('category');
    $search   = $request->get_param('search');

    // If URL provided: return the single matching post
    if ( ! empty($url) ) {
        $post_id = url_to_postid($url);

        // Fallback by path
        if ( ! $post_id ) {
            $path = wp_make_link_relative($url);
            $post = get_page_by_path( trim($path, '/'), OBJECT, 'post' );
            if ( $post ) $post_id = $post->ID;
        }

        if ( ! $post_id ) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No post found for provided URL.',
                'data'    => []
            ], 404);
        }

        $post = get_post($post_id);
        if ( ! $post || 'publish' !== $post->post_status ) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Post not accessible or not published.',
                'data'    => []
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'data'    => [ custom_format_post_item($post) ],
        ], 200);
    }

    // Else: recent posts list
    $args = [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => $count,
        'offset'              => $offset,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ];

    if ( ! empty($search) ) {
        $args['s'] = $search;
    }

    if ( ! empty($category) ) {
        if ( is_numeric($category) ) {
            $args['cat'] = (int) $category;                      
        } else {
            $args['category_name'] = sanitize_title($category);  
        }
    }

    $q = new WP_Query($args);
    $items = [];
    if ( $q->have_posts() ) {
        foreach ( $q->posts as $post ) {
            $items[] = custom_format_post_item($post);
        }
    }

    return new WP_REST_Response([
        'success' => true,
        'data'    => $items,
    ], 200);
}

/**
 * Format a post object into API shape
 */
function custom_format_post_item( $post ) {
    $post_id   = is_object($post) ? $post->ID : (int) $post;
    $title     = get_the_title($post_id);
    $permalink = get_permalink($post_id);

    $image_url = get_the_post_thumbnail_url($post_id, 'full');
    if ( ! $image_url ) {
        $first_img = custom_extract_first_image_from_content( get_post_field('post_content', $post_id) );
        if ( $first_img ) $image_url = $first_img;
    }

    $excerpt = get_the_excerpt($post_id);
    if ( empty($excerpt) ) {
        $content = strip_tags( get_post_field('post_content', $post_id) );
        $excerpt = wp_trim_words( $content, 30, '…' );
    }

    return [
        'id'                 => $post_id,
        'title'              => html_entity_decode( wp_strip_all_tags($title), ENT_QUOTES ),
        'image_url'          => $image_url ? esc_url_raw($image_url) : null,
        'short_description'  => html_entity_decode( wp_strip_all_tags($excerpt), ENT_QUOTES ),
        'url'                => esc_url_raw($permalink),
        'date'               => get_the_date(DATE_ATOM, $post_id),
    ];
}

/**
 * Extract first image from post content
 */
function custom_extract_first_image_from_content( $content ) {
    if ( empty($content) ) return null;

    if ( function_exists('parse_blocks') ) {
        $blocks = parse_blocks($content);
        foreach ( $blocks as $block ) {
            if ( isset($block['blockName']) && $block['blockName'] === 'core/image' ) {
                $attrs = isset($block['attrs']) ? $block['attrs'] : [];
                if ( ! empty($attrs['url']) ) return esc_url_raw($attrs['url']);
            }
        }
    }

    if ( preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m) ) {
        return esc_url_raw($m[1]);
    }

    return null;
}
