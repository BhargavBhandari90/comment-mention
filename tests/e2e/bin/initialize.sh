#!/bin/bash

echo "Initializing WordPress environment for E2E tests..."
# Enable pretty permalinks.
wp-env run tests-wordpress chmod -c ugo+w /var/www/html
wp-env run tests-cli wp rewrite structure '/%postname%/' --hard
wp-env run tests-cli wp user create testuser testuser@example.com --role=subscriber --user_pass=testuser