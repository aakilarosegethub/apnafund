#!/bin/bash
# Start Laravel dev server with increased upload limits for video uploads
exec php -d post_max_size=500M -d upload_max_filesize=500M artisan serve "$@"
