# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.18] - 2026-10-03

### Fixed
- A bar set to "All Pages Except" with no page types, URL patterns or URL parameters is shown on every page again; it was hidden everywhere.
- A Target URL Parameters entry without a value (for example `gclid`) now matches only pages whose URL has that parameter. Before, such an entry matched every page.
