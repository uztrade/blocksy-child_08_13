<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------- Подключение стилей Blocksy child ---------- */
add_action( 'wp_enqueue_scripts', function () {
	$child_css = get_stylesheet_directory() . '/style.css';
	$version   = file_exists( $child_css ) ? filemtime( $child_css ) : '1.0.2';

	// Blocksy Free сама подключает 'ct-main-styles' — child CSS грузится ПОСЛЕ него.
	$deps = wp_style_is( 'ct-main-styles', 'registered' ) ? array( 'ct-main-styles' ) : array();

	wp_enqueue_style( 'blocksy-child', get_stylesheet_uri(), $deps, $version );
}, 20 );

/* ---------- Editor-styles: style.css в редактор Gutenberg ---------- */
/* Чтобы кастомные классы (.tldr-box, .factcheck-box, .uzt-*) отображались
 * с оформлением прямо в блочном редакторе поста — как это было раньше,
 * когда стили жили в Customizer → Дополнительный CSS.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
} );

/* ---------- CPT: криптобиржи ---------- */
add_action( 'init', function () {
	register_post_type( 'crypto_exchange', array(
		'labels' => array(
			'name'          => 'Криптобиржи',
			'singular_name' => 'Криптобиржа',
			'add_new_item'  => 'Добавить биржу',
			'edit_item'     => 'Редактировать биржу',
			'menu_name'     => 'Криптобиржи',
		),
		'public'       => true,
		'has_archive'  => 'crypto-exchanges',
		'menu_icon'    => 'dashicons-chart-line',
		'rewrite'      => array( 'slug' => 'crypto-exchanges', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ),
		'show_in_rest' => true,
	) );
} );

/* ---------- ACF-поля криптобирж ---------- */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	acf_add_local_field_group( array(
		'key'    => 'group_uzt_cex',
		'title'  => 'Crypto Exchange Fields',
		'fields' => array(
			array( 'key'=>'field_cex_rating','label'=>'Рейтинг редакции (3.5–5)','name'=>'cex_rating','type'=>'number','min'=>3.5,'max'=>5,'step'=>0.1 ),
			array( 'key'=>'field_cex_logo_wide','label'=>'Логотип (широкий)','name'=>'cex_logo_wide','type'=>'image','return_format'=>'array' ),
			array( 'key'=>'field_cex_logo_compact','label'=>'Логотип (квадрат)','name'=>'cex_logo_compact','type'=>'image','return_format'=>'array' ),
			array( 'key'=>'field_cex_official_url','label'=>'Официальный сайт','name'=>'cex_official_url','type'=>'url' ),
			array( 'key'=>'field_cex_affiliate_url','label'=>'Партнёрская ссылка /go/','name'=>'cex_affiliate_url','type'=>'url' ),
			array( 'key'=>'field_cex_legal_entity','label'=>'Юр. лицо / ТМ','name'=>'cex_legal_entity','type'=>'text' ),
			array( 'key'=>'field_cex_founded','label'=>'Год основания','name'=>'cex_founded','type'=>'number' ),
			array( 'key'=>'field_cex_countries','label'=>'Страны / HQ','name'=>'cex_countries','type'=>'text' ),
			array( 'key'=>'field_cex_licenses','label'=>'Лицензии (по странам)','name'=>'cex_licenses','type'=>'textarea' ),
			array( 'key'=>'field_cex_spot_fee','label'=>'Спот Maker/Taker','name'=>'cex_spot_fee','type'=>'text' ),
			array( 'key'=>'field_cex_futures_fee','label'=>'Фьючерсы Maker/Taker','name'=>'cex_futures_fee','type'=>'text' ),
			array( 'key'=>'field_cex_p2p_fee','label'=>'Комиссия P2P','name'=>'cex_p2p_fee','type'=>'text' ),
			array( 'key'=>'field_cex_leverage','label'=>'Плечо (спот/маржа/фьюч)','name'=>'cex_leverage','type'=>'text' ),
			array( 'key'=>'field_cex_staking','label'=>'Стейкинг/Earn','name'=>'cex_staking','type'=>'text' ),
			array( 'key'=>'field_cex_copytrading','label'=>'Копитрейдинг/боты','name'=>'cex_copytrading','type'=>'text' ),
			array( 'key'=>'field_cex_p2p','label'=>'P2P площадка','name'=>'cex_p2p','type'=>'text' ),
			array( 'key'=>'field_cex_coins_count','label'=>'Кол-во криптовалют','name'=>'cex_coins_count','type'=>'text' ),
			array( 'key'=>'field_cex_fiat','label'=>'Фиатные валюты','name'=>'cex_fiat','type'=>'text' ),
			array( 'key'=>'field_cex_uzs_support','label'=>'Поддержка UZS','name'=>'cex_uzs_support','type'=>'select','choices'=>array('Да'=>'Да','Нет'=>'Нет','частично'=>'частично'),'allow_null'=>1 ),
			array( 'key'=>'field_cex_por','label'=>'Страховой фонд / PoR','name'=>'cex_por','type'=>'textarea' ),
			array( 'key'=>'field_cex_hacks','label'=>'История взломов','name'=>'cex_hacks','type'=>'textarea' ),
			array( 'key'=>'field_cex_reg_status','label'=>'Регуляторный статус','name'=>'cex_reg_status','type'=>'textarea' ),
			array( 'key'=>'field_cex_uz_access','label'=>'Доступность в Узбекистане','name'=>'cex_uz_access','type'=>'textarea' ),
			array( 'key'=>'field_cex_trust_score','label'=>'Trust Score (CMC/CG)','name'=>'cex_trust_score','type'=>'text' ),
			array( 'key'=>'field_cex_daily_volume','label'=>'Суточный объём','name'=>'cex_daily_volume','type'=>'text' ),
			array( 'key'=>'field_cex_support_lang','label'=>'Поддержка RU/UZ','name'=>'cex_support_lang','type'=>'text' ),
			array( 'key'=>'field_cex_min_deposit','label'=>'Мин. депозит','name'=>'cex_min_deposit','type'=>'text' ),
			array( 'key'=>'field_cex_pros','label'=>'Плюсы','name'=>'cex_pros','type'=>'textarea' ),
			array( 'key'=>'field_cex_cons','label'=>'Минусы','name'=>'cex_cons','type'=>'textarea' ),
			// FAQ-поля для бирж
			array( 'key'=>'field_cex_1_q','label'=>'Вопрос 1','name'=>'cex_1_q','type'=>'text' ),
			array( 'key'=>'field_cex_1_a','label'=>'Ответ 1','name'=>'cex_1_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_2_q','label'=>'Вопрос 2','name'=>'cex_2_q','type'=>'text' ),
			array( 'key'=>'field_cex_2_a','label'=>'Ответ 2','name'=>'cex_2_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_3_q','label'=>'Вопрос 3','name'=>'cex_3_q','type'=>'text' ),
			array( 'key'=>'field_cex_3_a','label'=>'Ответ 3','name'=>'cex_3_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_4_q','label'=>'Вопрос 4','name'=>'cex_4_q','type'=>'text' ),
			array( 'key'=>'field_cex_4_a','label'=>'Ответ 4','name'=>'cex_4_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_5_q','label'=>'Вопрос 5','name'=>'cex_5_q','type'=>'text' ),
			array( 'key'=>'field_cex_5_a','label'=>'Ответ 5','name'=>'cex_5_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_6_q','label'=>'Вопрос 6','name'=>'cex_6_q','type'=>'text' ),
			array( 'key'=>'field_cex_6_a','label'=>'Ответ 6','name'=>'cex_6_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_7_q','label'=>'Вопрос 7','name'=>'cex_7_q','type'=>'text' ),
			array( 'key'=>'field_cex_7_a','label'=>'Ответ 7','name'=>'cex_7_a','type'=>'textarea' ),
			array( 'key'=>'field_cex_8_q','label'=>'Вопрос 8','name'=>'cex_8_q','type'=>'text' ),
			array( 'key'=>'field_cex_8_a','label'=>'Ответ 8','name'=>'cex_8_a','type'=>'textarea' ),
		),
		'location' => array( array( array( 'param'=>'post_type','operator'=>'==','value'=>'crypto_exchange' ) ) ),
	) );
} );

/* ---------- Универсальный хелпер FAQ (broker + crypto_exchange) ---------- */
if ( ! function_exists( 'uzt_get_faq_items' ) ) {
	function uzt_get_faq_items( $prefix, $max = 8 ) {
		$faqs = array();
		for ( $i = 1; $i <= $max; $i++ ) {
			$q = trim( (string) get_field( "{$prefix}_{$i}_q" ) );
            $a = trim( (string) get_field( "{$prefix}_{$i}_a" ) );
			
			if ( $q !== '' && $a !== '' ) {
				$faqs[] = array( 'q' => $q, 'a' => $a );
			}
		}
		return $faqs;
	}
}

/* ---------- Backwards-compat алиас для криптобирж ---------- */
if ( ! function_exists( 'uzt_cex_faq_items' ) ) {
	function uzt_cex_faq_items( $max = 8 ) {
		return uzt_get_faq_items( 'cex', $max );
	}
}

/* ---------- Schema FAQPage: единый хендлер для broker и crypto_exchange ---------- */
add_action( 'wp_footer', function () {
	$prefix = null;
	if ( is_singular( 'broker' ) ) {
		$prefix = 'faq';
	} elseif ( is_singular( 'crypto_exchange' ) ) {
		$prefix = 'cex';
	}
	if ( ! $prefix ) return;

	$entities = array();
	for ( $i = 1; $i <= 8; $i++ ) {
		$q = trim( (string) get_field( "{$prefix}_{$i}_q" ) );
        $a = trim( wp_strip_all_tags( (string) get_field( "{$prefix}_{$i}_a" ) ) );
		if ( $q === '' || $a === '' ) continue;
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $q,
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
		);
	}
	if ( empty( $entities ) ) return;

	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
} );

/* ---------- E-E-A-T v6: включаем CPT (broker, crypto_exchange) в архив автора ---------- */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) return;
	if ( ! $query->is_author() ) return;
	$query->set( 'post_type', array( 'post', 'broker', 'crypto_exchange' ) );
} );

/* ---------- E-E-A-T v6: расширяем Person schema для Константина Иванова ---------- */
add_filter( 'rank_math/json_ld', function ( $data, $jsonld ) {
	if ( ! is_author() ) return $data;
	$author_id = get_query_var( 'author' );
	if ( ! $author_id ) return $data;
	$user = get_userdata( $author_id );
	if ( ! $user || $user->user_login !== 'konstantin-ivanov' ) return $data;

	foreach ( $data as $key => $item ) {
		if ( ! is_array( $item ) ) continue;
		$type_val = isset( $item['@type'] ) ? $item['@type'] : '';
		$is_person = is_array( $type_val ) ? in_array( 'Person', $type_val, true ) : $type_val === 'Person';
		if ( ! $is_person ) continue;

		$existing_same_as = isset( $item['sameAs'] ) ? (array) $item['sameAs'] : array();
		$data[ $key ]['sameAs'] = array_values( array_unique( array_merge(
			$existing_same_as,
			array(
				'https://uztrading.net',
				'https://t.me/uztrading_net',
				'https://youtube.com/@uztrading',
			)
		) ) );

		if ( empty( $data[ $key ]['jobTitle'] ) ) {
			$data[ $key ]['jobTitle'] = 'Автор-аналитик, сооснователь uztrading.net';
		}
		if ( empty( $data[ $key ]['knowsAbout'] ) ) {
			$data[ $key ]['knowsAbout'] = array(
				'Forex',
				'CFD Trading',
				'Cryptocurrency Exchanges',
				'Prop Trading',
				'Stock Trading',
			);
		}
		if ( empty( $data[ $key ]['homeLocation'] ) ) {
			$data[ $key ]['homeLocation'] = array(
				'@type'   => 'Place',
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => 'Бишкек',
					'addressCountry'  => 'KG',
				),
			);
		}
	}

	return $data;
}, 20, 2 );

/* ---------- Site Reviews: принудительная загрузка скриптов для CPT ---------- */
add_filter( 'glsr_enqueue_scripts', function( $enqueue ) {
	if ( is_singular( array( 'broker', 'crypto_exchange' ) ) ) {
		return true;
	}
	return $enqueue;
} );

/* ============================================================
 * uztrading.net auto-blocks system (Задача Т1.6)
 * Безопасная загрузка: если файл inc/*.php не создан — просто
 * пропускается. Никакого fatal error при отсутствии файлов.
 * ============================================================ */
$uzt_auto_files = array(
	'/inc/enqueue.php',
	'/inc/auto-blocks.php',
	'/inc/author-box.php',
);
foreach ( $uzt_auto_files as $uzt_file ) {
	$uzt_path = get_stylesheet_directory() . $uzt_file;
	if ( file_exists( $uzt_path ) ) {
		require_once $uzt_path;
	}
}
unset( $uzt_auto_files, $uzt_file, $uzt_path );

// Инлайн SVG-sprite Tabler Icons для главной страницы
add_action('wp_body_open', function () {
	if (!is_front_page()) return;
	$sprite_path = get_stylesheet_directory() . '/icons/sprite.svg';
	if (file_exists($sprite_path)) {
		echo file_get_contents($sprite_path);
	}
}, 5);