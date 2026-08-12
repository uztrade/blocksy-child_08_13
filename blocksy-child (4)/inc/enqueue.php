<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'uzt_enqueue_auto_blocks_css', 20);
function uzt_enqueue_auto_blocks_css() {
	if (!is_singular('post')) return;

	$css_path = '/assets/css/auto-blocks.css';
	$abs_path = get_stylesheet_directory() . $css_path;
	$version  = file_exists($abs_path) ? filemtime($abs_path) : '1.0.0';

	wp_enqueue_style(
		'uzt-auto-blocks',
		get_stylesheet_directory_uri() . $css_path,
		array('blocksy-child'),
		$version
	);
}