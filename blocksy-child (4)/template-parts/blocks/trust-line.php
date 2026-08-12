<?php
if (!defined('ABSPATH')) exit;

$author_id  = get_post_field('post_author', get_the_ID());
$author_url = get_author_posts_url($author_id);
$author     = get_the_author_meta('display_name', $author_id);
$updated    = get_the_modified_date('d.m.Y');
?>
<div class="uzt-trust-line" role="contentinfo">
	<em>
		Проверено редакцией <a href="<?php echo esc_url(home_url('/')); ?>">uztrading.net</a>
		· Автор: <a href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author); ?></a>
		· Обновлено: <?php echo esc_html($updated); ?>
		· <a href="/metodologiya/">Методология</a>
	</em>
</div>