# CF Page Template Selector

Ever wish you could add more than just a title to your WordPress custom pages? CF Page Template Selector lets you add an informative description and screenshot to each of your custom page templates, taking all the guess-work out of selecting a template.

## Usage Example

An ordinary custom page comment header looks like this:

	/**
	 * Template Name: Archives
	 */

To add a description and screenshot, just add something like this:

	/**
	 * Template Name: Archives
	 * Description: A compact list of all previous posts, just showing titles
	 * Screenshot: relative/path/to/screenshot.png
	 */

Both the description and screenshot parameters are optional.

## New Comment Header Parameters

### Description

Optional. A short description of the page template. HTML can be used, but be careful!

### Screenshot

Optional, strongly suggested. A relative path to the screenshot file.

- The url to the theme directory is automatically prepended to your relative path and has a trailing slash (ie, don't add a slash at the beginning of your relative path).
- Screenshots must be `180px` wide by `120px` high.
- Screenshots must be `.png` files.