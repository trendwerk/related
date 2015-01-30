Related
=======

Related posts for WordPress. Solely based on taxonomies.

## Installation
If you're using Composer to manage WordPress, add this plugin to your project's dependencies. Run:
```sh
composer require trendwerk/related 1.0.0
```

Or manually add it to your `composer.json`:
```json
"require": {
	"trendwerk/related": "1.0.0"
},
```

## Usage

	$related = new TP_Related( $post_id, $args );

Use `$related` like any other custom WP_Query loop.

**$post_id** The ID of the post you want related articles from

**$args** Array of additional arguments. Same as WP_Query.
