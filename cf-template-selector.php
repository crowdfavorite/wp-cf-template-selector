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
		var _popover = {};
		_popover.settings = {
			selector: ".cfts-select .cfts-options"
		};
		_popover.align = function() {
			var $popover = $(_popover.settings.selector);
			var offsetY = $popover.outerHeight() / 2;
			var offsetX = $popover.outerWidth() - 9;
			$popover.css({
				"margin-top": "-" + offsetY + "px",
				"margin-left": "-" + offsetX + "px"
			});
		}
		_popover.toggle = function() {
			var $popover = $(_popover.settings.selector);
			var $popoverVisible = $(".cfts-select .cfts-options:visible");
			
			if ($popoverVisible.length > 0) {
				// Hide if visible
				$popover.fadeOut('fast');
			} else {
				// Show if hidden
				$popover.fadeIn('fast');
			}
		}
		
		$(function() {
			$(".cfts-select .cfts-value").click(function(){
				_popover.toggle();
			});
			
			_popover.align();
			
			$(".cfts-select .cfts-option").live('click', function() {
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
				
				$(".cfts-option").removeClass("cfts-selected");
				_this.addClass("cfts-selected");
				_popover.toggle();
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
		display: block;
		position: relative;
		z-index: 100;
	}
	.cfts-select .cfts-value {
		background: #fff url(<?php echo CFTS_URL; ?>img/pencil.png) no-repeat right top;
		border: 1px solid #dfdfdf;
		-moz-border-radius: 4px; /* FF1+ */
		-webkit-border-radius: 4px; /* Saf3+, Chrome */
		-khtml-border-radius: 4px; /* Konqueror */
		border-radius: 4px; /* Standard. IE9 */
		cursor: pointer;
		display: block;
		overflow: hidden;
		padding: 6px 28px 6px 6px;
		z-index: 100;
	}
	.cfts-select .cfts-options {
		left: 0;
		padding: 12px 0 12px 12px;
		position: absolute;
		top: 50%;
		width: 614px;
		z-index: 101;
	}
	.cfts-select .cfts-options.has-1 {
		width: 230px;
	}
	.cfts-select .cfts-options.has-2 {
		width: 422px;
	}
	.cfts-select .cfts-options-inside,
	.cfts-select .cfts-options .cfts-round-1,
	.cfts-select .cfts-options .cfts-round-2,
	.cfts-select .cfts-options .cfts-round-3,
	.cfts-select .cfts-options .cfts-round-4,
	.cfts-select .cfts-options .cfts-round-5 {
		background: url(<?php echo CFTS_URL; ?>img/popover.png) no-repeat 0 0;
	}
	.cfts-select .cfts-options .cfts-round-1,
	.cfts-select .cfts-options .cfts-round-2,
	.cfts-select .cfts-options .cfts-round-3,
	.cfts-select .cfts-options .cfts-round-4,
	.cfts-select .cfts-options .cfts-round-5 {
		position: absolute;
	}
	.cfts-select .cfts-options .cfts-round-1,
	.cfts-select .cfts-options .cfts-round-2,
	.cfts-select .cfts-options .cfts-round-3,
	.cfts-select .cfts-options .cfts-round-4 {
		height: 12px;
		width: 50%;	
	}
	/**
	 * Top
	 */
	.cfts-select .cfts-options .cfts-round-1,
	.cfts-select .cfts-options .cfts-round-2 {
		top: 0;
	}
	.cfts-select .cfts-options .cfts-round-1 {
		background-position: 0 0;
		left: 0;
	}
	.cfts-select .cfts-options .cfts-round-2 {
		background-position: right 0;
		right: 0;
	}
	/**
	 * Bottom
	 */
	.cfts-select .cfts-options .cfts-round-3,
	.cfts-select .cfts-options .cfts-round-4 {
		bottom: 0;
	}
	.cfts-select .cfts-options .cfts-round-3 {
		background-position: 0 bottom;
		left: 0;
	}
	.cfts-select .cfts-options .cfts-round-4 {
		background-position: right bottom;
		right: 0;
	}
	.cfts-select .cfts-options .cfts-round-5 {
		background-position: 0 center;
		bottom: 12px;
		left: 0;
		top: 12px;
		width: 12px;
	}
	.cfts-select .cfts-options-inside {
		background-position: right center;
		padding-right: 18px;
	}
	.cfts-select .cfts-options ul {
		background: #fff;
		max-height: 384px;
		overflow: auto;
	}
	.cfts-select .cfts-option {
		-moz-border-radius: 4px; /* FF1+ */
		-webkit-border-radius: 4px; /* Saf3+, Chrome */
		-khtml-border-radius: 4px; /* Konqueror */
		border-radius: 4px; /* Standard. IE9 */
		cursor: pointer;
		display: -moz-inline-box; /* FF2 */
		display: inline-block; /* Standard. IE8+, Saf, FF3+ */
		/**
		 * @bugfix inline-block fix
		 * @see http://blog.mozilla.com/webdev/2009/02/20/cross-browser-inline-block/
		 * @affected IE6, IE7
		 * @valid no
		 */
		zoom: 1;
		*display: inline;

		margin: 0;
		overflow: hidden;
		padding: 5px;
		vertical-align: top;
		width: 182px;
	}
	.cfts-select .cfts-option.cfts-selected {
		background: #eaf2fa;
	}
	.cfts-select img.cfts-screenshot {
		border: 1px solid #ddd;
		float: left;
		height: 120px;
		margin: 0 0 5px;
		width: 180px;
	}
	.cfts-select .cfts-option:hover img.cfts-screenshot {
		border-color: #56A7E5;
	}
	.cfts-select .cfts-name {
		display: block;
		font-weight: bold;
		font-size: 12px;
		line-height: 14px !important;
		margin: 0 3px 2px;
	}
	.cfts-select .cfts-description {
		color: #777;
		font-size: 11px;
		line-height: 14px !important;
		margin: 0 3px;
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
			<div id="cfts-selected" class="cfts-value">
				<?php
				if (!empty($selected['screenshot'])) {
					echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$selected['screenshot'].'" id="cfts-selected-screenshot" class="cfts-screenshot" />';
				}
				else {
					echo '<img src="'.CFTS_URL.'img/default.png" id="cfts-selected-screenshot" class="cfts-screenshot" />';
				}
				if (!empty($selected['name'])) {
					echo '<strong id="cfts-selected-name" class="cfts-name">'.$selected['name'].'</strong>';
				}
				else {
					echo '<strong id="cfts-selected-name" class="cfts-name"></strong>';
				}
				if (!empty($selected['description'])) {
					echo '<div id="cfts-selected-description" class="cfts-description">'.$selected['description'].'</div>';
				}
				else {
					echo '<div id="cfts-selected-description" class="cfts-description"></div>';
				}
				?>
			</div>
			<?php 
			// Set class depending on number of items
			$num_of_items = count($template_info);
			switch ($num_of_items) {
				case 1:
				case 2:
					$gallery_classname = ' has-' . $num_of_items;
					break;
				default:
					$gallery_classname = ' has-many';
					break;
			}
			 ?>
			<div class="cfts-options<?php echo $gallery_classname; ?>">
				<div class="cfts-options-inside">
					<ul>
						<?php
						
						// Output items
						foreach ($template_info as $filename => $template) { 
							$selected_class = '';
							if ($selected_template == $filename) {
								$selected_class = " cfts-selected";
							}
							$file = str_replace('.php', '', $filename);
							?><li id="cfts-option-<?php echo $file; ?>" class="cfts-option<?php echo $selected_class; ?>">
								<input type="hidden" id="cfts-option-file-<?php echo $file; ?>" value="<?php echo $filename; ?>" />
								<?php
								if (!empty($template['screenshot'])) {
									echo '<img src="'.trailingslashit(get_stylesheet_directory_uri()).$template['screenshot'].'" id="cfts-option-screenshot-'.$file.'" class="cfts-screenshot" />';
								} else {
									echo '<img src="'.CFTS_URL.'img/default.png" class="cfts-screenshot" />';
								}
								if (!empty($template['name'])) {
									echo '<strong id="cfts-option-name-'.$file.'" class="cfts-name">'.$template['name'].'</strong>';
								}
								if (!empty($template['description'])) {
									echo '<div id="cfts-option-description-'.$file.'" class="cfts-description">'.$template['description'].'</div>';
								}
								?>
							</li><?php 
						} 
						?>
					</ul>
				</div>
				<div class="cfts-round-1"></div>
				<div class="cfts-round-2"></div>
				<div class="cfts-round-3"></div>
				<div class="cfts-round-4"></div>
				<div class="cfts-round-5"></div>
			</div><!--/cfts-options-->
		</div><!--/cfts-select-->
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