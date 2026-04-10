<?php
/**
 * Plugin Name:       ApnaFund / Crowdfunding — Posts JSON API
 * Description:       REST endpoint compatible with ApnaCrowdfunding business-resources: published posts as JSON (title, excerpt, url, image_url). GET /wp-json/custom/posts
 * Version:           1.0.0
 * Author:            ApnaFund
 * License:           GPL-2.0-or-later
 * Text Domain:       apnafund-crowdfunding-posts
 *
 * @package Apnafund_Crowdfunding_Posts
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * CORS: allow browser fetch from your main site (adjust filter if you need a single origin).
 */
add_filter('rest_pre_serve_request', 'apnafund_cfp_rest_cors', 10, 4);

/**
 * @param bool             $served  Whether the request has already been served.
 * @param WP_HTTP_Response $result  Result to send to the client.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @param WP_REST_Server   $server  Server instance.
 */
function apnafund_cfp_rest_cors($served, $result, $request, $server)
{
    $route = $request->get_route();
    if ($route !== '/custom/posts' && strpos($route, 'custom/posts') === false) {
        return $served;
    }
    if (! headers_sent()) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    return $served;
}

add_action('rest_api_init', function () {
    register_rest_route(
        'custom',
        '/posts',
        [
            'methods'             => 'GET',
            'callback'            => 'apnafund_cfp_posts_callback',
            'permission_callback' => '__return_true',
            'args'                => [
                'count'    => [
                    'description'       => 'Number of posts (1–50).',
                    'type'              => 'integer',
                    'default'           => 10,
                    'minimum'           => 1,
                    'maximum'           => 50,
                    'sanitize_callback' => 'absint',
                ],
                'offset'   => [
                    'type'              => 'integer',
                    'default'           => 0,
                    'sanitize_callback' => 'absint',
                ],
                'category' => [
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'search'   => [
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'wrapped'  => [
                    'description' => 'If 1, response is { success, data } instead of a raw array.',
                    'type'        => 'integer',
                    'default'     => 0,
                    'enum'        => [0, 1],
                ],
            ],
        ]
    );
});

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function apnafund_cfp_posts_callback(WP_REST_Request $request)
{
    $count    = max(1, min(50, (int) $request->get_param('count')));
    $offset   = max(0, (int) $request->get_param('offset'));
    $category = $request->get_param('category');
    $search   = $request->get_param('search');
    $wrapped  = (int) $request->get_param('wrapped') === 1;

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

    if (! empty($search)) {
        $args['s'] = $search;
    }

    if (! empty($category)) {
        if (is_numeric($category)) {
            $args['cat'] = (int) $category;
        } else {
            $args['category_name'] = sanitize_title($category);
        }
    }

    $query = new WP_Query($args);
    $items = [];

    foreach ($query->posts as $post) {
        if (! $post instanceof WP_Post || $post->post_status !== 'publish') {
            continue;
        }
        $items[] = apnafund_cfp_format_post($post);
    }

    if ($wrapped) {
        return new WP_REST_Response(
            [
                'success' => true,
                'data'    => $items,
            ],
            200
        );
    }

    // Raw JSON array — works with fetch() when client expects an array.
    return new WP_REST_Response($items, 200);
}

/**
 * Shape matches ApnaCrowdfunding business-resources cards:
 * title / title.rendered, excerpt / excerpt.rendered, url, image_url.
 *
 * @param WP_Post $post Post.
 * @return array<string, mixed>
 */
function apnafund_cfp_format_post(WP_Post $post)
{
    $post_id = (int) $post->ID;

    $title_raw = get_the_title($post_id);
    $title_raw = html_entity_decode(wp_strip_all_tags($title_raw), ENT_QUOTES, 'UTF-8');

    $permalink = get_permalink($post_id);
    $permalink = $permalink ? esc_url_raw($permalink) : '';

    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    if (! $image_url) {
        $image_url = get_the_post_thumbnail_url($post_id, 'full');
    }
    if (! $image_url) {
        $image_url = apnafund_cfp_first_image_from_content($post->post_content);
    }
    $image_url = $image_url ? esc_url_raw($image_url) : '';

    $excerpt_text = get_the_excerpt($post_id);
    if ($excerpt_text === '') {
        $excerpt_text = wp_strip_all_tags($post->post_content);
        $excerpt_text = wp_trim_words($excerpt_text, 40, '…');
    }
    $excerpt_text = html_entity_decode(wp_strip_all_tags($excerpt_text), ENT_QUOTES, 'UTF-8');

    // HTML snippets for .rendered (frontend strips tags for card text; links allowed minimally).
    $excerpt_html = wpautop(esc_html($excerpt_text));
    $title_html   = esc_html($title_raw);

    return [
        'id'      => $post_id,
        'date'    => get_post_time(DATE_ATOM, true, $post_id),
        'title'   => [
            'rendered' => $title_html,
        ],
        'excerpt' => [
            'rendered' => $excerpt_html,
        ],
        // Flat duplicates for older clients.
        'title_plain'   => $title_raw,
        'short_description' => $excerpt_text,
        'url'           => $permalink,
        'link'          => $permalink,
        'image_url'     => $image_url,
    ];
}

/**
 * First <img src> or core/image block URL from post content.
 *
 * @param string $content HTML.
 * @return string Empty or absolute URL.
 */
function apnafund_cfp_first_image_from_content($content)
{
    if ($content === '') {
        return '';
    }

    if (function_exists('parse_blocks')) {
        $blocks = parse_blocks($content);
        foreach ($blocks as $block) {
            if (isset($block['blockName']) && $block['blockName'] === 'core/image') {
                $attrs = isset($block['attrs']) ? $block['attrs'] : [];
                if (! empty($attrs['url'])) {
                    return esc_url_raw($attrs['url']);
                }
            }
        }
    }

    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m)) {
        return esc_url_raw($m[1]);
    }

    return '';
}
