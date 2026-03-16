#!/bin/bash
# Start Laravel dev server with increased upload limits for video uploads
# --host=0.0.0.0 allows access from local network (e.g. http://192.168.1.51:8000)
exec php -d post_max_size=500M -d upload_max_filesize=500M artisan serve --host=0.0.0.0 "$@"
