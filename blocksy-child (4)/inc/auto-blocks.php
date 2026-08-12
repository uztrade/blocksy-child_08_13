<?php
if (!defined('ABSPATH')) exit;

/**
 * uztrading.net — Auto-inject reusable blocks for single blog posts.
 * BOTTOM ONLY: Risk (только риск-категории) + Affiliate + Trust line.
 * Верх поста остаётся чистым — только заголовок, meta и share buttons.
 *
 * Всё через the_content filter — callouts внутри .entry-content,
 * ширина наследуется от Blocksy автоматически.
 */

function uzt_get_risk_categories() {
	return array('forex', 'crypto', 'prop-trading', 'akcii-etf', 'regulyatory');
}

add_filter('the_content', 'uzt_inject_auto_blocks', 5);
function uzt_inject_auto_blocks($content) {
	if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
		return $content;
	}

	ob_start();

	// Risk Disclaimer — только для риск-категорий
	if (has_category(uzt_get_risk_categories())) {
		get_template_part('template-parts/blocks/risk-disclaimer');
	}

	// Affiliate Disclosure — всегда
	get_template_part('template-parts/blocks/affiliate-disclosure');

	// Trust line — всегда, замыкает блок автоматики
	get_template_part('template-parts/blocks/trust-line');

	$bottom = ob_get_clean();

	return $content . $bottom;
}