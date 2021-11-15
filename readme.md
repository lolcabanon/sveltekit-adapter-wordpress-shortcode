# sveltekit-adapter-wordpress-shortcode

[Adapter](https://kit.svelte.dev/docs#adapters) for SvelteKit which turns your app into a wordpress shortcode.

## Usage

Install with `npm i -D sveltekit-adapter-wordpress-shortcode`, setup the adapter in `svelte.config.js`, add `index.php` to the project root, and mark the parts of your template you want to include in the shortcode.

### Example `svelte.config.js` 

It's likely you will need to set custom base paths for wordpress. e.g.

```js
// svelte.config.js
import adapter from 'sveltekit-adapter-wordpress-shortcode'

const production = process.env.NODE_ENV === "production"

const base = "/wp-content/plugins/my-shortcode-plugin"

export default {
    kit: {
        adapter: adapter({
            pages: "build",
            assets: "build",
            fallback: null,
            indexPath: "index.php"
        }),
        paths: production && {
            base,
            assets: "https://example.com" + base
        }
    }
}
```

### Example `index.php`

Add an `index.php` to the root of your project and customize accordingly. e.g.

```php
<?php
/**
 * Plugin Name: Svelte Kit Shortcode
 */

include plugin_dir_path( __FILE__ ) . 'svelte_kit_shortcode.php';

svelte_kit_shortcode("svelte-kit-shortcode");
```

You can choose the path by setting `indexPath` in the adapter config. 

### Example `app.html`

Wrap the parts of your template that you want added to the shortcode. 

```html
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- SHORTCODE HEAD START -->
		%svelte.head%
        <!-- SHORTCODE HEAD END -->
	</head>
	<body>
        <!-- SHORTCODE BODY START -->
        <div id="svelte">%svelte.body%</div>
        <!-- SHORTCODE BODY END -->
	</body>
</html>
```

## Style Isolation

The following sequence of `postcss` plugins should provide enough isolation from Wordpress styles.

Note that `postcss-autoreset` is using the fork at `tomatrow/postcss-autoreset`.

```js
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const safeImportant = require('postcss-safe-important');
const prefixer = require('postcss-prefix-selector')
const initial = require('postcss-initial')
const autoReset = require('postcss-autoreset')

const mode = process.env.NODE_ENV;
const dev = mode === 'development';

const config = {
	plugins: [
        autoReset({ reset: "revert" }),
        initial(),
        prefixer({
            prefix: "#svelte",
            transform: function (_, selector, prefixedSelector) {
                if (["html", "body"].includes(selector)) 
                    return `${selector} #svelte`
                else 
                    return prefixedSelector
            }
        }),
		autoprefixer(),
        safeImportant(),
		!dev &&
			cssnano({
				preset: 'default'
			})
	]
};

module.exports = config;
```

## License

[MIT](LICENSE)
