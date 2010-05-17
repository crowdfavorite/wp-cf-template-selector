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
define('CFTS_DIR_NAME', apply_filters('cfts_dir_name', 'cf-template-selector'));
// Used for CSS
define('CFTS_URL', trailingslashit(content_url('/plugins/' . CFTS_DIR_NAME)) );

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
			$(".cfts-option").live('click', function() {
				var _this = $(this);
				var id = _this.attr('id').replace('cfts-option-', '');
				var file = $("#cfts-option-file-"+id).val();
				var screenshot = $("#cfts-option-screenshot-"+id).attr('src');
				var name = $("#cfts-option-name-"+id).html();
				var description = $("#cfts-option-description-"+id).html();

				$("#page_template").val(file);
				$("#cfts-selected-name").html(name);
				if (screenshot != undefined) {
					$("#cfts-selected-screenshot").show();
					$("#cfts-selected-screenshot").attr('src', screenshot);
				}
				else {
					$("#cfts-selected-screenshot").hide();
				}
				if (description != null) {
					$("#cfts-selected-description").html(description);
				}
				else {
					$("#cfts-selected-description").html('');
				}
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
		background: #fff;
		border: 1px solid #dfdfdf;
		-moz-border-radius: 4px; /* FF1+ */
		-webkit-border-radius: 4px; /* Saf3+, Chrome */
		-khtml-border-radius: 4px; /* Konqueror */
		border-radius: 4px; /* Standard. IE9 */
		display: block;
		padding: 5px 6px;
		position: relative;
		z-index: 100;
	}
	.cfts-select .cfts-options {
		background: url(<?php echo CFTS_URL; ?>img/bubble-tick.png) no-repeat right center;
		left: -304px;
		margin-top: -200px;
		overflow: hidden;
		padding: 4px 12px 4px 4px;
		position: absolute;
		top: 50%;
		z-index: 101;
	}
	.cfts-select .cfts-options ul {
		background: #fff;
		height: 386px;
		margin: 3px;
		overflow: auto;
		width: 290px;
	}
	.cfts-select .cfts-option {
		border-bottom: 1px solid #ddd;
		cursor: pointer;
		margin: 0;
		height: 108px;
		padding: 6px 6px 6px 126px;
		position: relative;
	}
	.cfts-select .cfts-option:hover {
		background: #eaf2fa;
	}
	.cfts-select .cfts-option .cfts-screenshot {
		border-right: 1px solid #ddd;
		left: 0;
		position: absolute;
		top: 0;
	}
	.cfts-select .cfts-option .cfts-name {
		font-weight: bold;
	}
	.cfts-select .cfts-option .cfts-description {
		color: #777;
		font-size: 11px;
	}
	.cfts-fade-bottom {
		background: url(<?php echo CFTS_URL; ?>img/fade-bottom.png) repeat-x;
		bottom: 0;
		margin: 3px 3px 7px 3px;
		position: absolute;
		height: 20px;
		width: 276px;
		z-index: 102;
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
	$selected_template = '';
	
	if (!empty($post->page_template)) {
		$selected_template = $post->page_template;
	}
	else {
		$selected_template = 'default';
	}
	$selected = $template_info[$selected_template];
	?>
	<div id="cfts-page-template-selector-area" style="display:none;">
		<div id="cfts-page-template-selector" class="cfts-select">
			<span id="cfts-selected" class="cfts-value">
				<?php
				if (!empty($selected['screenshot'])) {
					echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$selected['screenshot'].'" id="cfts-selected-screenshot" class="cfts-screenshot">';
				}
				if (!empty($selected['name'])) {
					echo '<span id="cfts-selected-name" class="cfts-name">'.$selected['name'].'</span>';
				}
				if (!empty($selected['description'])) {
					echo '<span id="cfts-selected-description" class="cfts-description">'.$selected['description'].'</span>';
				}
				?>
			</span>
			<div class="cfts-options">
				<ul>
					<?php 
					foreach ($template_info as $filename => $template) { 
						$file = str_replace('.php', '', $filename);
						?>
						<li id="cfts-option-<?php echo $file; ?>" class="cfts-option">
							<input type="hidden" id="cfts-option-file-<?php echo $file; ?>" value="<?php echo $filename; ?>" />
							<?php
							if (!empty($template['screenshot'])) {
								echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$template['screenshot'].'" id="cfts-option-screenshot-'.$file.'" class="cfts-screenshot">';
							}
							if (!empty($template['name'])) {
								echo '<strong id="cfts-option-name-'.$file.'" class="cfts-name">'.$template['name'].'</strong>';
							}
							if (!empty($template['description'])) {
								echo '<div id="cfts-option-description-'.$file.'" class="cfts-description">'.$template['description'].'</div>';
							}
							?>
						</li>
					<?php 
					} 
					?>
				</ul>
				<div class="cfts-fade-bottom"></div>
			</div>
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