=== ApnaFund / Crowdfunding — Posts JSON API ===
Contributors: apnafund
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later

REST API for published posts in the JSON shape used by ApnaCrowdfunding “business-resources” success stories.

== Endpoint ==

GET /wp-json/custom/posts

Query parameters:
- count (1–50, default 10)
- offset (pagination)
- category (slug or numeric ID)
- search (keyword)
- wrapped=1 → JSON object { "success": true, "data": [ ... ] } instead of a raw array

== Install ==

1. Zip the folder `wp-plugin-apnafund-crowdfunding-posts` or copy it into `wp-content/plugins/`.
2. Activate “ApnaFund / Crowdfunding — Posts JSON API”.
3. Settings → Permalinks → Save (flush rewrite rules if needed).

== Point your site ==

Example: https://YOUR-WORDPRESS-SITE/wp-json/custom/posts?count=4

On the Laravel app, set in .env (then php artisan config:clear):

WORDPRESS_POSTS_API_URL=https://YOUR-WORDPRESS-SITE/wp-json/custom/posts
WORDPRESS_BLOG_HOME_URL=https://YOUR-WORDPRESS-SITE

No trailing slash on either value (optional; config trims them).
