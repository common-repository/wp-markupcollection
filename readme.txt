=== WP-MarkupCollection ===
Contributors: ko1nksm
Donate link: http://nksm.name/donate/
Tags: Markdown, DokuWiki, MediaWiki, reStructuredText, textile, HatenaSyntax, BBcode, MultiMarkdown, Pandoc
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to write posts using Markdown, DokuWiki, MediaWiki, reStructuredText, textile, HatenaSyntax, BBcode, etc.

== Description ==

This plugin is developed on Github. To get full source code, see Github.

<https://github.com/ko1nksm/wp-markup-collection>

= Features =

* Write your posts using many markup languages.
* Syntax highlighter plugin integration..
* Customizable architecture to use another filter. (for developer)

= Filters and supported Markup Languages =

* [Internal] PHP Markdown (Markdown)
* [Internal] PHP Markdown Extra (Markdown Extra)
* [Internal] PHP Markdown Lib (Markdown, Markdown Extra) *Requires PHP >= 5.3.0
* [Internal] PHP Textile (Textile) *Requires PHP >= 5.3.0
* [Internal] PHP reStructuredText (reStructuredText)
* [Internal] Text_Wiki (BBcode, coWiki, Creole, DokuWiki, MediaWiki, Tiki)
* [Internal] HatenaSyntax (Hatena Syntax)
* [External] MultiMarkdown  (MultiMarkdown)
* [External] Pandoc (Markdown, reStructuredText, Textile, LaTeX, MediaWiki, AsciiDoc, Org-Mode, etc)

The external filter, you need to install the command.

= Markup syntax =

Supported syntax are depend on those libraries or commands.

PHP Markdown, PHP Markdown Extra, PHP Markdown Lib  
<http://michelf.ca/projects/php-markdown/>

PHP Textile  
<https://github.com/textile/php-textile/>

PHP reStructuredText  
<https://code.google.com/p/php-restructuredtext/>

Text_Wiki  
<https://github.com/pear/Text_Wiki/>

HatenaSyntax  
<https://github.com/anatoo/HatenaSyntax>

MultiMarkdown  
<http://fletcherpenney.net/multimarkdown/>

Pandoc  
<http://johnmacfarlane.net/pandoc/>

== Installation ==

1. Install from wordpress plugins menu. Search "WP-MarkupCollection" and click install.

= Alternative =
1. Upload `wp-markup-collection` folder to the `/wp-content/plugins/` directory.
2. Add executable permissions to "wp-markup-collection/bootstrap.php" (if you want to use ExecRunner).
3. Activate the plugin through the 'Plugins' menu in WordPress.

= Optional =

* If you want to use external filters, install the command. e.g. multimarkdown, pandoc.



== Frequently Asked Questions ==

= Supported PHP version =

5.2 or later (some filters requires 5.3 or later)

= What is multimarkdown or pandoc =

This plugin supports multimarkdown and pandoc as external filters
If you want to use external filters, You need to install those command.

= Where can I get multimarkdown from? =

Please refer to <http://fletcherpenney.net/multimarkdown/>

Note: Get MultiMarkdown-4. (not MMD3 aka peg-multimarkdown)

= Where can I get pandoc from? =

Please refer to <http://johnmacfarlane.net/pandoc/>

= I want to use "sed or something" as filter, but doesn't work =

Due to security reason, Executable commands are limited. If you want to use another command as filter, need to create custom classes.

Please refer to "custom.example.php" that is included in this plugin.

= When plugin was deactivated, markup are displayed on the posts. =

Markup (wrapped in a fallback tag) stored in the post_content column. When disabling the plugin, displays content in post_content column as is as HTML.

If this behavior is unpleasant, check the box to "convert to HTML" when you save.

= Where the data is stored? =

* posts - post_content column of wp_posts table.
* cache of posts - wp_postmeta table.
* meta of posts - wp_postmeta table.
* options - wp_options table.

= Why are you store posts's cache in wp_postmeta? =

Some Markdown plugins are store the HTML to the post_content. And store the Markdown to the post_content_filtered.

This approach is superior that doesn't spew out Markdown even if you disable the plugin. But it has some problem.

* Revisions and Auto-saved posts format are HTML.
* Compare Revisions are displayed in HTML.
* Does not export post_content_filtered column.
* Bulk edit lose Markdown.

I don't want to lose markup. For that I made ​​this plugin.

= What happens if uninstall this plugin. =

Option will be removed. Posts and cache are remains.

= What is "&lt;pre class='wp-markup-collection'&gt;...&lt;/pre&gt;" =

This is fallback tag. Markup are stored by being wrapped in a fallback tag internally.

When you disable plugin, Markup displayed as HTML in a fallback tag in order to avoid the collapse of the page.



== Changelog ==

= 1.1.2 =
* Fix a bug that garbage is inserted when article is large.
* Compatibility with WordPress 3.9.

= 1.1.1 =

* Fix a no args filter call via HTTP POST.
* Fix a bug that doesn't allow to change without filter.

= 1.1.0 =

* No need for php cli.
* Fix for php 5.2.6

= 1.0.0 =

stable release
