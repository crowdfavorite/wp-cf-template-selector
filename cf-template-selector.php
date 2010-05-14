<?php 
/*
Plugin Name: CF Template Selector
Plugin URI: http://crowdfavorite.com
Description: Custom Template Selector for the Page edit screen.  Adds the ability to see screenshots and descriptions of page template files.
Version: 1.0
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

// ini_set('display_errors', '1'); ini_set('error_reporting', E_ALL);

## Constants
define('CFTS_VERSION', '1.0');
define('CFTS_DIR', trailingslashit(realpath(dirname(__FILE__))));

if (!defined('PLUGINDIR')) {
	define('PLUGINDIR','wp-content/plugins');
}

## Includes

load_plugin_textdomain('cfts');

## Init Functionality

function cfts_resources() {
	if (!empty($_GET['cf_action'])) {
		switch ($_GET['cf_action']) {
			case 'cfts_admin_js':
				cfts_admin_js();
				die();
				break;
			case 'cfts_admin_css':
				cfts_admin_css();
				die();
				break;
		}
	}
}
add_action('init', 'cfts_resources', 1);


## JS/CSS

function cfts_admin_js() {
	header('Content-type: text/javascript');
	?>
	;(function($) {
		$(function() {
			$(".cfts-option-radio").live('change', function() {
				var _this = $(this);
				var id = _this.attr('id');
				var file = $("#cfts-option-file-"+id.replace('cfts-option-radio-', '')).val();
				$("#page_template").val(file);
			});
		});
	})(jQuery);
	<?php
	die();
}

if (is_admin()) {
	wp_enqueue_script('cfts_admin_js', admin_url('?cf_action=cfts_admin_js'), array('jquery'), CFTS_VERSION);
}

function cfts_admin_css() {
	header('Content-type: text/css');
	?>
	.cfts-select {
		display: -moz-inline-box;
		display: inline-block;
		/**
		 * @bugfix inline-block fix
		 * @affected	IE6, IE7
		 * @valid		no
		 */
		*zoom: 1;
		*display: inline;
		position: relative;
		min-width: 90%;
	}
	.cfts-select .cfts-value {
		border: 1px solid #dfdfdf;
		-moz-border-radius: 4px; /* FF1+ */
		-webkit-border-radius: 4px; /* Saf3+, Chrome */
		-khtml-border-radius: 4px; /* Konqueror */
		border-radius: 4px; /* Standard. IE9 */
		display: block;
		padding: 3px 6px;
		position: relative;
		z-index: 100;
	}
	.cfts-select:hover .cfts-value {
		border-bottom-color: #fff;
		-moz-border-radius-bottomleft: 0; /* FF1+ */
		-webkit-border-bottom-left-radius: 0; /* Saf3+, Chrome */
		-khtml-border-bottom-left-radius: 0; /* Konqueror */
		border-bottom-left-radius: 0; /* Standard. IE9 */
		-moz-border-radius-bottomright: 0; /* FF1+ */
		-webkit-border-bottom-right-radius: 0; /* Saf3+, Chrome */
		-khtml-border-bottom-right-radius: 0; /* Konqueror */
		border-bottom-right-radius: 0; /* Standard. IE9 */
	}
	.cfts-select .cfts-options {
		background: #fff;
		border: 1px solid #dfdfdf;
		-moz-box-shadow: 0 3px 5px rgba(0, 0, 0, .5); /* FF3.5+ */
		-webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, .5); /* Saf3+, Chrome */
		box-shadow: 0 3px 5px rgba(0, 0, 0, .5); /* Standard. Opera 10.5, IE9 */
		margin-top: -1px;
		max-height: 400px;
		min-width: 300px;
		overflow: auto;
		padding: 3px 6px;
		position: absolute;
		right: 0;
		z-index: 99;
	}
	<?php
	die();
}

if (is_admin()) {
	wp_enqueue_style('cfts_admin_css', admin_url('?cf_action=cfts_admin_css'), array(), CFTS_VERSION, 'screen');
}


## Page Display Functionality

/**
 * Function to display individual items
 */
function _cfts_page_template_selector_item($title, $description, $image_url) {
	
}

function cfts_page_template_selector() {
	global $post;
	$template_info = cfts_page_template_info();
	if (!is_array($template_info) || empty($template_info)) { return; }
	
	$selected = $template_info[$post->page_template];
	?>
	<div id="cfts-page-template-selector-area" style="display:none;">
		<div id="cfts-page-template-selector" class="cfts-select">
			<span id="cfts-selected" class="cfts-value">
				<input type="radio" name="cfts-option-radio" id="cfts-option-radio-<?php echo $post->page_template; ?>" class="cfts-option-radio" checked="checked" />
				<label for="cfts-option-radio-<?php echo $post->page_template; ?>">
					<?php
					if (!empty($selected['screenshot'])) {
						echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$selected['screenshot'].'" id="cfts-selected-screenshot" class="cfts-screenshot">';
					}
					if (!empty($selected['name'])) {
						echo '<span id="cfts-selected-name" class="cfts-name">'.$selected['name'].'</span>';
					} else {
						echo '<span id="cfts-selected-name" class="cfts-name">'.__('Select a Template', 'cfts').'</span>';
					}
					if (!empty($selected['description'])) {
						echo '<span id="cfts-selected-description" class="cfts-description">'.$selected['description'].'</span>';
					}
					?>
				</label>
			</span>
			<ul class="cfts-options">
				<?php 
				foreach ($template_info as $filename => $template) { 
					if ($filename == $post->page_template) { continue; }
					$file = str_replace('.php', '', $filename);
					?>
					<li id="cfts-option-<?php echo $file; ?>" class="cfts-option">
						<input type="radio" name="cfts-option-radio" id="cfts-option-radio-<?php echo $file; ?>" class="cfts-option-radio"<?php checked($filename, $post->page_template); ?> />
						<input type="hidden" id="cfts-option-file-<?php echo $file; ?>" value="<?php echo $filename; ?>" />
						<label for="cfts-option-radio-<?php echo $file; ?>">
							<?php
							if (!empty($template['screenshot'])) {
								echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$template['screenshot'].'" id="cfts-option-screenshot-'.$file.'" class="cfts-screenshot">';
							}
							if (!empty($template['name'])) {
								echo '<span id="cfts-option-name-'.$file.'" class="cfts-name">'.$template['name'].'</span>';
							}
							if (!empty($template['description'])) {
								echo '<span id="cfts-option-description-'.$file.'" class="cfts-description">Description: </span>'.$template['description'].'</span>';
							}
							?>
						</label>
					</li>
				<?php 
				} 
				?>
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		jQuery("#page_template").hide();
		jQuery("#page_template").after(jQuery("#cfts-page-template-selector-area").html());
	</script>
	<?php
}
add_action('admin_footer', 'cfts_page_template_selector');


## Page Template Info Gathering Functionality

function cfts_page_template_info() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array();

	if (is_array($templates) && !empty($templates)) {
		$base = array(trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()));
		
		if (file_exists(trailingslashit(get_stylesheet_directory()).'page.php')) {
			$basename = 'page.php';
			$template_data = implode('', file(trailingslashit(get_stylesheet_directory()).'page.php'));
			$name = '';
			$description = '';
			$screenshot = '';
			
			if (preg_match('|Template Name:(.*)$|mi', $template_data, $name)) {
				$name = _cleanup_header_comment($name[1]);
			}
			else {
				$name = 'Default';
				$basename = 'default';
			}
			if (preg_match('|Description:(.*)$|mi', $template_data, $description)) {
				$description = _cleanup_header_comment($description[1]);
			}
			if (preg_match('|Screenshot:(.*)$|mi', $template_data, $screenshot)) {
				$screenshot = _cleanup_header_comment($screenshot[1]);
			}
			
			if (!empty($name)) {
				$page_templates[$basename] = array(
					'name' => $name,
					'description' => $description,
					'screenshot' => $screenshot
				);
			}
		}
		
		foreach ($templates as $template) {
			$basename = str_replace($base, '', $template);
			
			// Don't allow template files in subdirectories???
			if (strpos($basename, '/') !== false) {
				continue;
			}
			
			$template_data = implode('', file($template));
			$name = '';
			$description = '';
			$screenshot = '';
			if (preg_match('|Template Name:(.*)$|mi', $template_data, $name)) {
				$name = _cleanup_header_comment($name[1]);
			}
			if (preg_match('|Description:(.*)$|mi', $template_data, $description)) {
				$description = _cleanup_header_comment($description[1]);
			}
			if (preg_match('|Screenshot:(.*)$|mi', $template_data, $screenshot)) {
				$screenshot = _cleanup_header_comment($screenshot[1]);
			}
			
			if (!empty($name)) {
				$page_templates[$basename] = array(
					'name' => $name,
					'description' => $description,
					'screenshot' => $screenshot
				);
			}
		}
	}
	ksort($page_templates);
	return $page_templates;
}

?>