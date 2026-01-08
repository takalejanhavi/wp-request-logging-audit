# wp-request-logging-audit
## Request Logging Audit

A WordPress plugin that logs incoming requests and provides
an admin interface to review recent activity.

## Use Case
Built to simulate real-world enterprise needs such as
monitoring access patterns and debugging production issues.

## Features
- Logs request URI, method, user, timestamp
- Uses WordPress hooks
- Admin dashboard for viewing logs
- Minimal performance impact

## Architecture Notes
- No core modifications
- Sanitized inputs
- Admin-only visibility

## Installation
1. Upload to wp-content/plugins
2. Activate plugin
