<?php
/**
 * Шаблон одиночной карточки брокера — uztrading.net
 * Файл: wp-content/themes/blocksy-child/single-broker.php
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------- Вспомогательные функции (самодостаточный файл) ---------- */
if ( ! function_exists( 'uzt_img_url' ) ) {
	function uzt_img_url( $img ) {
		if ( empty( $img ) ) return '';
		if ( is_array( $img ) ) return isset( $img['url'] ) ? $img['url'] : '';
		if ( is_numeric( $img ) ) return wp_get_attachment_image_url( $img, 'full' );
		return $img;
	}
}
if ( ! function_exists( 'uzt_list_items' ) ) {
	function uzt_list_items( $field ) {
		$items = array();
		if ( have_rows( $field ) ) {
			while ( have_rows( $field ) ) { the_row();
				$t = get_sub_field( 'text' );
				if ( ! $t ) $t = get_sub_field( 'item' );
				if ( $t ) $items[] = $t;
			}
		} else {
			$raw = get_field( $field );
			if ( is_array( $raw ) ) {
				foreach ( $raw as $r ) { $items[] = is_array( $r ) ? implode( ' ', $r ) : $r; }
			} elseif ( ! empty( $raw ) ) {
				foreach ( preg_split( "/\r\n|\r|\n/", $raw ) as $line ) {
					$line = trim( $line ); if ( $line !== '' ) $items[] = $line;
				}
			}
		}
		return $items;
	}
}
if ( ! function_exists( 'uzt_bool' ) ) {
	function uzt_bool( $v ) { return ( $v === true || $v === 1 || $v === '1' ); }
}
if ( ! function_exists( 'uzt_img_dims' ) ) {
	function uzt_img_dims( $img ) {
		$id = 0;
		if ( is_array( $img ) ) {
			$w = isset( $img['width'] ) ? (int) $img['width'] : 0;
			$h = isset( $img['height'] ) ? (int) $img['height'] : 0;
			if ( $w && $h ) return array( $w, $h );
			$id = isset( $img['ID'] ) ? (int) $img['ID'] : 0;
		} elseif ( is_numeric( $img ) ) {
			$id = (int) $img;
		} elseif ( is_string( $img ) && $img !== '' ) {
			$id = attachment_url_to_postid( $img );
		}
		if ( $id ) {
			$m = wp_get_attachment_metadata( $id );
			if ( $m && ! empty( $m['width'] ) && ! empty( $m['height'] ) ) return array( (int) $m['width'], (int) $m['height'] );
		}
		return array( 0, 0 );
	}
}
if ( ! function_exists( 'uzt_faq_items' ) ) {
	function uzt_faq_items( $max = 8 ) {
		$faqs = array();
		for ( $i = 1; $i <= $max; $i++ ) {
			$q = trim( (string) get_field( "faq_{$i}_q" ) );
			$a = trim( (string) get_field( "faq_{$i}_a" ) );
			if ( $q !== '' && $a !== '' ) {
				$faqs[] = array( 'q' => $q, 'a' => $a );
			}
		}
		return $faqs;
	}
}

get_header();

/* ---------- Внутренние ссылки сайта (ПРОВЕРЬ слаги перед публикацией) ---------- */
$uzt_links = array(
	'methodology' => home_url( '/metodologiya/' ),          // Методология рейтинга
	'brokers'     => home_url( '/brokers/' ),               // Архив брокеров
	'faq'         => home_url( '/faq/' ),                   // FAQ — проверь точный слаг
	'risk'        => home_url( '/legal/risk-disclaimer/' ), // Риски — по спеке под /legal/
	'author'      => home_url( '/author/konstantin-ivanov/' ),
);

while ( have_posts() ) : the_post();
	$name        = get_the_title();
	$rating      = (float) get_field( 'broker_rating' );
	$logo_wide_raw   = get_field( 'broker_logo_wide' );
	$logo_compact_raw= get_field( 'broker_logo_compact' );
	$logo_wide   = uzt_img_url( $logo_wide_raw );
	$logo_compact= uzt_img_url( $logo_compact_raw );
	list( $logo_w, $logo_h ) = uzt_img_dims( $logo_wide_raw ?: $logo_compact_raw );
	$affiliate   = get_field( 'broker_affiliate_url' );
	$official    = get_field( 'broker_official_url' );
	$min_dep     = get_field( 'broker_min_deposit' );
	$max_lev     = get_field( 'broker_max_leverage' );
	$spread_std  = get_field( 'broker_spread_eurusd_std' );
	$spread_ecn  = get_field( 'broker_spread_eurusd_ecn' );
	$commission  = get_field( 'broker_commission_ecn' );
	$base_cur    = get_field( 'broker_base_currencies' );
	$pairs       = get_field( 'broker_pairs' );
	$instruments = get_field( 'broker_instruments' );
	$swap_free   = uzt_bool( get_field( 'broker_swap_free' ) );
	$legal_entity= get_field( 'broker_legal_entity' );
	$founded     = get_field( 'broker_founded' );
	$countries   = get_field( 'broker_countries' );
	$regulators  = get_field( 'broker_regulators' );
	$tier1       = uzt_bool( get_field( 'broker_tier1' ) );
	$comp_fund   = get_field( 'broker_compensation_fund' );
	$segregation = get_field( 'broker_segregation' );
	$kyc         = uzt_bool( get_field( 'broker_kyc' ) );
	$risk_flags  = get_field( 'broker_risk_flags' );
	$copytrading = uzt_bool( get_field( 'broker_copytrading' ) );
	$crypto_dep  = uzt_bool( get_field( 'broker_crypto_deposit' ) );
	$withdraw    = get_field( 'broker_withdraw_time' );
	$trustpilot  = get_field( 'broker_trustpilot' );
	$aggr        = get_field( 'broker_aggregator_rating' );
	$loss_pct    = get_field( 'broker_loss_pct' );
	$languages   = get_field( 'broker_languages' );

	$platforms_raw = get_field( 'broker_platforms' );
	$platforms = array();
	if ( is_array( $platforms_raw ) ) {
		foreach ( $platforms_raw as $p ) { $platforms[] = is_array( $p ) ? ( isset($p['label']) ? $p['label'] : implode(' ',$p) ) : $p; }
	} elseif ( ! empty( $platforms_raw ) ) { $platforms[] = $platforms_raw; }

	$pros = uzt_list_items( 'broker_pros' );
	$cons = uzt_list_items( 'broker_cons' );
	$rating_pct = $rating > 0 ? min( 100, ( $rating / 5 ) * 100 ) : 0;
	$go_url = $affiliate ? $affiliate : ( $official ? $official : '#' );
?>
<main class="uzt-broker" id="broker-top">

  <?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) rank_math_the_breadcrumbs(); ?>

  <section class="uzt-hero">
    <?php if ( $logo_wide || $logo_compact ) : ?>
      <img class="uzt-hero__logo" src="<?php echo esc_url( $logo_wide ?: $logo_compact ); ?>" alt="Логотип <?php echo esc_attr( $name ); ?>"<?php if ( $logo_w && $logo_h ) printf( ' width="%d" height="%d"', $logo_w, $logo_h ); ?> loading="eager" fetchpriority="high" decoding="async">
    <?php endif; ?>
    <h1 class="uzt-hero__title"><?php echo esc_html( $name ); ?>: обзор <?php echo esc_html( date_i18n( 'Y' ) ); ?></h1>

    <?php if ( $rating > 0 ) : ?>
      <div class="uzt-rating" aria-label="Рейтинг <?php echo esc_attr( $rating ); ?> из 5">
        <span class="uzt-rating__bar"><span class="uzt-rating__fill" style="width:<?php echo esc_attr( $rating_pct ); ?>%"></span></span>
        <strong><?php echo esc_html( number_format( $rating, 1 ) ); ?></strong> / 5
      </div>
    <?php endif; ?>

    <div class="uzt-badges">
      <?php if ( $tier1 ) : ?><span class="uzt-badge uzt-badge--ok">✓ Tier-1 регулятор</span><?php endif; ?>
      <?php if ( $swap_free ) : ?><span class="uzt-badge uzt-badge--halal">Swap-free</span><?php endif; ?>
    </div>

    <div class="uzt-kpi">
      <div class="uzt-kpi__item"><span>Мин. депозит</span><strong><?php echo esc_html( $min_dep ?: '—' ); ?></strong></div>
      <div class="uzt-kpi__item"><span>Спред EUR/USD</span><strong><?php echo esc_html( $spread_std ?: '—' ); ?></strong></div>
      <div class="uzt-kpi__item"><span>Макс. плечо</span><strong><?php echo esc_html( $max_lev ?: '—' ); ?></strong></div>
    </div>

    <a class="uzt-cta" href="<?php echo esc_url( $go_url ); ?>" target="_blank" rel="sponsored nofollow noopener">Перейти на сайт брокера →</a>
    <p class="uzt-cta__note">Партнёрская ссылка</p>

    <p class="uzt-risk">⚠ Торговля CFD и Forex связана с высоким риском потери капитала<?php echo $loss_pct ? '. По данным брокера, убыточны ' . esc_html( $loss_pct ) . ' розничных счетов' : ''; ?>.</p>
  </section>

  <section class="uzt-verdict">
  <?php the_content(); ?>

  <!-- Day 3.5.5: Auto-summary отзывов (только если count > 0) -->
  <?php
  $uzt_reviews_count = 0;
  if ( function_exists( 'glsr_get_ratings' ) ) {
    $uzt_r = glsr_get_ratings( array( 'assigned_posts' => get_the_ID() ) );
    if ( is_object( $uzt_r ) && isset( $uzt_r->reviews ) ) {
      $uzt_reviews_count = (int) $uzt_r->reviews;
    }
  }
  if ( $uzt_reviews_count > 0 ) {
    echo do_shortcode( '[uzt_broker_reviews_summary link_text="Читать все отзывы ↓" link_target="#uzt-reviews"]' );
  }
  ?>
</section>

  <section class="uzt-trust">
    <span>Автор: <a href="<?php echo esc_url( $uzt_links['author'] ); ?>"><?php echo esc_html( get_the_author() ); ?></a></span>
    <span>Опубликовано: <?php echo esc_html( get_the_date() ); ?></span>
    <?php if ( get_the_modified_date() !== get_the_date() ) : ?><span>Обновлено: <?php echo esc_html( get_the_modified_date() ); ?></span><?php endif; ?>
    <a href="<?php echo esc_url( $uzt_links['methodology'] ); ?>">Методология рейтинга →</a>
  </section>

  <?php if ( $pros || $cons ) : ?>
  <section class="uzt-proscons">
    <div class="uzt-pros"><h2>Плюсы</h2><ul><?php foreach ( $pros as $p ) echo '<li>' . esc_html( $p ) . '</li>'; ?></ul></div>
    <div class="uzt-cons"><h2>Минусы</h2><ul><?php foreach ( $cons as $c ) echo '<li>' . esc_html( $c ) . '</li>'; ?></ul></div>
  </section>
  <?php endif; ?>

  <section class="uzt-snapshot">
    <h2>Ключевые факты о <?php echo esc_html( $name ); ?></h2>
    <table>
      <?php
      $rows = array(
        'Регулятор(ы)'    => $regulators,
        'Мин. депозит'    => $min_dep,
        'Макс. плечо'     => $max_lev,
        'Спред EUR/USD'   => $spread_std,
        'Платформы'       => $platforms ? implode( ', ', $platforms ) : '',
        'Исламский счёт'  => $swap_free ? 'Да' : 'Нет',
        'Языки поддержки' => $languages,
        'Год основания'   => $founded,
        'Юр. лицо'        => $legal_entity,
      );
      foreach ( $rows as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
  </section>

  <section class="uzt-reg">
    <h2>Регуляция и безопасность средств</h2>
    <table>
      <?php
      $reg = array(
        'Регулятор(ы)'        => $regulators,
        'Tier-1 регулятор'    => $tier1 ? 'Да' : 'Нет',
        'Сегрегация средств'  => ( is_string( $segregation ) && $segregation !== '' ) ? $segregation : ( uzt_bool( $segregation ) ? 'Да' : 'Нет' ),
        'Компенсационный фонд'=> $comp_fund,
        'KYC (верификация)'   => $kyc ? 'Обязателен' : 'Нет',
        'Страны регистрации'  => $countries,
        'Риск-флаги'          => $risk_flags,
      );
      foreach ( $reg as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
  </section>

  <section class="uzt-conditions">
    <h2>Торговые условия</h2>
    <table>
      <?php
      $cond = array(
        'Спред EUR/USD (Standard)' => $spread_std,
        'Спред EUR/USD (ECN/Raw)'  => $spread_ecn,
        'Комиссия (ECN)'           => $commission,
        'Мин. депозит'             => $min_dep,
        'Макс. плечо'              => $max_lev,
        'Базовые валюты счёта'     => $base_cur,
        'Валютных пар'             => $pairs,
        'Всего инструментов'       => $instruments,
        'Swap-free / Исламский'    => $swap_free ? 'Да' : 'Нет',
        'Копитрейдинг'             => $copytrading ? 'Да' : 'Нет',
      );
      foreach ( $cond as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
    <?php $acct = uzt_list_items( 'broker_account_types' ); if ( $acct ) : ?>
      <h3>Типы счетов</h3>
      <ul><?php foreach ( $acct as $a ) echo '<li>' . esc_html( $a ) . '</li>'; ?></ul>
    <?php endif; ?>
  </section>

  <section class="uzt-pay">
    <h2>Пополнение и вывод</h2>
    <?php $dep = uzt_list_items( 'broker_deposit_methods' ); if ( $dep ) : ?>
      <p><strong>Методы пополнения и вывода:</strong></p>
      <ul><?php foreach ( $dep as $d ) echo '<li>' . esc_html( $d ) . '</li>'; ?></ul>
    <?php endif; ?>
    <table>
      <?php
      $pay = array(
        'Криптодепозит (USDT и др.)' => $crypto_dep ? 'Да' : 'Нет',
        'Среднее время вывода'       => $withdraw,
      );
      foreach ( $pay as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
  </section>

  <section class="uzt-platforms">
    <h2>Платформы и поддержка</h2>
    <table>
      <?php
      $pf = array(
        'Платформы'       => $platforms ? implode( ', ', $platforms ) : '',
        'Копитрейдинг'    => $copytrading ? 'Да' : 'Нет',
        'Языки поддержки' => $languages,
      );
      foreach ( $pf as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
  </section>

  <section class="uzt-reputation">
    <h2>Репутация</h2>
    <table>
      <?php
      $rep = array(
        'Trustpilot'             => $trustpilot,
        'Рейтинг на агрегаторах' => $aggr,
        '% убыточных счетов'     => $loss_pct,
      );
      foreach ( $rep as $k => $v ) { if ( $v !== '' && $v !== null ) echo '<tr><th>' . esc_html( $k ) . '</th><td>' . esc_html( $v ) . '</td></tr>'; }
      ?>
    </table>
  </section>

  <?php $faqs = uzt_faq_items( 8 ); if ( $faqs ) : ?>
  <section class="uzt-faq" id="faq">
    <h2>Часто задаваемые вопросы о <?php echo esc_html( $name ); ?></h2>
    <?php foreach ( $faqs as $i => $f ) : ?>
      <details class="uzt-faq__item"<?php echo $i === 0 ? ' open' : ''; ?>>
        <summary class="uzt-faq__q"><?php echo esc_html( $f['q'] ); ?></summary>
        <div class="uzt-faq__a"><?php echo wp_kses_post( wpautop( $f['a'] ) ); ?></div>
      </details>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>
  
  <!-- НАЧАЛО: Плашка «Как формируется этот блок отзывов» (Шаг 4 v2, вариант C) -->
<div class="uzt-reviews-context" role="note" aria-label="Как формируется этот блок отзывов">
	<span class="uzt-reviews-context__icon" aria-hidden="true">ℹ️</span>
	<div class="uzt-reviews-context__body">
		<h3 class="uzt-reviews-context__title">Как формируется этот блок отзывов</h3>
		<p>Этот блок содержит <strong>два типа отзывов</strong>, которые учитываются в общем рейтинге:</p>
		<p><strong>1. Редакционные обзоры Uztrading</strong> — подготовлены нашими командами <strong>Редакция</strong>, <strong>Экспертная оценка</strong> и <strong>Аналитический отдел Uztrading</strong> на основе перекрёстного анализа мнений реальных пользователей на независимых международных и русскоязычных площадках отзывов и профильных форумах, а также материалов, накопленных за 2023–2026 гг. на нашем сестринском ресурсе <a href="https://uztrading.ru" rel="noopener" target="_blank"><strong>uztrading.ru</strong></a> и в Telegram-сообществе <a href="https://t.me/uztrading_net" rel="noopener" target="_blank"><strong>@uztrading_net</strong></a>. Мы не цитируем внешние площадки дословно и не воспроизводим их логотипы или названия — обзоры представляют собой редакционный синтез.</p>
		<p><strong>2. Пользовательские отзывы</strong> — оставлены реальными клиентами через форму ниже. Проходят модерацию редакции (проверка на спам, оскорбления и заведомо ложную информацию), но редакция не редактирует и не переписывает содержание отзыва. Пользовательские отзывы подписаны реальным именем автора (не командой).</p>
		<p><strong>Как отличить.</strong> Смотри подпись под отзывом: если это «Редакция Uztrading» / «Экспертная оценка Uztrading» / «Аналитический отдел Uztrading» — это наш редакционный обзор. Если это имя человека — это пользовательский отзыв.</p>
		<p><strong>Uztrading не является представителем упомянутых брокеров и бирж.</strong> Средний рейтинг пересчитывается автоматически с каждым новым отзывом. Подробнее о принципах — на странице <a href="https://uztrading.net/metodologiya/">Методология рейтинга и редакционная политика</a>.</p>
	</div>
</div>
<!-- КОНЕЦ: Плашка -->

  <!-- НАЧАЛО: Отзывы трейдеров (Site Reviews bridge v1.3.0, Day 3.5) -->
<?php
$uzt_reviews_title = 'Отзывы трейдеров об ' . $name;

// 1. Принудительный стиль для ссылок (сработает сразу, в обход кэша UCSS)
echo '<style>.uzt-disclaimer a, .uzt-reviews-context a, .glsr-review-assigned_links a, .uzt-trust a { text-decoration: underline !important; }</style>';

// 2. Включаем буферизацию, чтобы гарантированно поймать вывод плагина
ob_start();
echo do_shortcode( sprintf( '[uzt_broker_reviews_full title="%s" per_page="5" pagination="loadmore"]', wp_strip_all_tags( $uzt_reviews_title ) ) );
$reviews_html = ob_get_clean();

// 3. Расширенная регулярка, которая найдет h4 даже с лишними пробелами/атрибутами
$reviews_html = preg_replace('/<h4([^>]*glsr-tag-value[^>]*)>(.*?)<\/h4>/is', '<h3$1>$2</h3>', $reviews_html);

// Выводим очищенный HTML
echo $reviews_html;
?>
<!-- КОНЕЦ: Отзывы трейдеров -->


  <!-- НАЧАЛО: Блок "Похожие брокеры" -->
  <section class="uzt-similar" style="margin: 40px 0;">
    <h2>Похожие брокеры</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
      <?php
      $similar_args = array(
        'post_type'           => 'broker',
        'posts_per_page'      => 4,
        'post__not_in'        => array( get_the_ID() ),
        'post_status'         => 'publish',
        'orderby'             => 'rand',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
      );
      $similar_query = new WP_Query( $similar_args );
      if ( $similar_query->have_posts() ) :
        while ( $similar_query->have_posts() ) : $similar_query->the_post();
          $sim_rating = (float) get_field( 'broker_rating' );
          $sim_logo_raw = get_field( 'broker_logo_compact' ) ?: get_field( 'broker_logo_wide' );
          $sim_logo = uzt_img_url( $sim_logo_raw );
      ?>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center; background: #fff;">
          <?php if ( $sim_logo ) : ?>
            <a href="<?php the_permalink(); ?>" style="display: block; margin-bottom: 15px;">
              <img src="<?php echo esc_url( $sim_logo ); ?>" alt="<?php the_title_attribute(); ?>" style="max-height: 50px; width: auto; object-fit: contain;">
            </a>
          <?php endif; ?>
          <h3 style="font-size: 16px; margin: 0 0 10px 0;">
            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: #1a1a1a;"><?php the_title(); ?></a>
          </h3>
          <?php if ( $sim_rating > 0 ) : ?>
            <div style="font-size: 14px; font-weight: bold; color: #8c4f17;">⭐ <?php echo esc_html( number_format( $sim_rating, 1 ) ); ?> / 5</div>
          <?php endif; ?>
        </div>
      <?php
        endwhile;
        wp_reset_postdata();
      endif;
      ?>
    </div>
  </section>
  <!-- КОНЕЦ: Блок "Похожие брокеры" -->

  <section class="uzt-disclaimer">
    <p>Торговля производными инструментами (CFD, Forex) связана с высоким риском и может привести к потере всего капитала.</p>
    <p>uztrading.net получает партнёрское вознаграждение от брокеров при переходе по ссылкам на этой странице. Это не влияет на нашу оценку — см. <a href="<?php echo esc_url( $uzt_links['methodology'] ); ?>">методологию</a>.</p>
    <p>Информация носит образовательный характер и не является индивидуальной инвестиционной рекомендацией.</p>
  </section>

  <nav class="uzt-footlinks" aria-label="Полезные ссылки" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0; display: flex; gap: 15px; flex-wrap: wrap; font-size: 14px; justify-content: center;">
    <a href="<?php echo esc_url( $uzt_links['methodology'] ); ?>" style="color: #0056b3; text-decoration: none;">Методология рейтинга</a> |
    <a href="<?php echo esc_url( $uzt_links['brokers'] ); ?>" style="color: #0056b3; text-decoration: none;">Все брокеры</a> |
    <a href="<?php echo esc_url( $uzt_links['faq'] ); ?>" style="color: #0056b3; text-decoration: none;">Частые вопросы (FAQ)</a> |
    <a href="<?php echo esc_url( $uzt_links['risk'] ); ?>" style="color: #0056b3; text-decoration: none;">Предупреждение о рисках</a>
  </nav>

  <div class="uzt-sticky">
    <?php if ( $logo_compact || $logo_wide ) : ?><img src="<?php echo esc_url( $logo_compact ?: $logo_wide ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="32" height="32"><?php endif; ?>
    <span class="uzt-sticky__name"><?php echo esc_html( $name ); ?></span>
    <a class="uzt-cta uzt-cta--sm" href="<?php echo esc_url( $go_url ); ?>" target="_blank" rel="sponsored nofollow noopener">Перейти →</a>
  </div>

</main>
<?php
endwhile;

get_footer();