<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

/**
 * 親テーマと子テーマのスタイルを読み込む
 * @see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme
 */
add_action( 'wp_enqueue_scripts', 'blankslate_child_style' );
function blankslate_child_style()
{
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}

/**
 * WordPressテーマエディタにJavaScriptファイルタイプを追加
 */
function add_js_to_wp_theme_editor_filetypes_ex($default_types)
{
    $default_types[] = 'js';
    return $default_types;
}
add_filter('wp_theme_editor_filetypes', 'add_js_to_wp_theme_editor_filetypes_ex');

/**
 * アイキャッチ画像（投稿サムネイル）を有効化
 */
add_theme_support('post-thumbnails');

/**
 * アスペクト比を維持した画像サイズを計算して返す
 * 
 * @param string $src 画像のソースパス
 * @param int $num_w 希望する幅
 * @param string $attr 返却形式（'y'でHTML属性、'css'でCSSスタイル、デフォルトで配列）
 * @return string|array 指定された形式の寸法
 */
function get_ratiocal($src = '', $num_w = 0, $attr = 'y')
{
    if (empty($src) || !is_numeric($num_w)) {
        return ($attr == 'y' || $attr == 'css') ? '' : array($num_w, 0);
    }

    $imgsize = getimagesize($src);
    if (!$imgsize || $imgsize[0] <= 0) {
        return ($attr == 'y' || $attr == 'css') ? '' : array($num_w, 0);
    }

    $num_y = $num_w * $imgsize[1] / $imgsize[0];
    switch($attr){
        case 'y': return ' width="' . $num_w . '" height="' . round($num_y) . '"'; break;
        case 'css': return ' style="width:' . $num_w . 'px; height:' . round($num_y) . 'px;"'; break;
        default: return array($num_w, round($num_y)); break;
    }
}

/**
 * カテゴリーと追加リンクを含むフッターメニューを生成
 * 
 * @return string フォーマットされたフッターメニューのHTML
 */
function get_footer_menu()
{
    if (!function_exists('get_categories')) {
        return '';
    }

    $categories = get_categories(array(
        'orderby' => 'name',
        'order' => 'DESC',
        'hide_empty' => 0
    ));

    if (empty($categories) || is_wp_error($categories)) {
        return '';
    }
    
    $category_links = array();
    foreach ($categories as $category) {
        if (isset($category->name)) {
            $category_links[] = sprintf(
                '<a class="footer__menu-item" href="%s">%s</a>',
                esc_url(home_url('/works/' . sanitize_title($category->name))),
                esc_html(strtoupper($category->name))
            );
        }
    }
    $foot_cat = '<div class="footer__menu-section"><p class="footer__menu-head">WORKS：</p>[' . "&nbsp;" . implode("&nbsp;|&nbsp;", $category_links) . "&nbsp;" . ']</div>';
    
    //フッターメニューその他
    $add_cat_array = array('resume', 'about', 'home');
    $cat_add_array = array();
    foreach($add_cat_array as $val){
        $add_url = ($val == 'home') ? '' : sanitize_title($val);
        $cat_add_array[] = '<a class="footer__menu-item" href="' . esc_url(home_url('/' . $add_url)) . '">' . esc_html(strtoupper($val)) . '</a>';
    }
    $foot_cat .= '<div class="footer__menu-section"><p class="footer__menu-head">REST：</p>[' . "&nbsp;" . implode("&nbsp;|&nbsp;", $cat_add_array) . "&nbsp;" . ']</div>';
    
    return $foot_cat;
}

/**
 * 異なる表示タイプ用のエントリー画像を取得してフォーマット
 * 
 * @param string $acf_img_path 画像パス
 * @param string $acf_cmmnt 画像のコメント/説明
 * @param string $type 表示タイプ（'list'または'main'）
 * @return string フォーマットされた画像のHTML
 */
function get_entry_image($acf_img_path='', $acf_cmmnt='', $type='list')
{
    if(!empty($acf_img_path)){
        $path = esc_url($acf_img_path);
        $cmmnt = wp_kses_post($acf_cmmnt);

        switch($type){
            case 'list': $class = ' class="detail__list-image"'; break;
            case 'main': $class = ' class="entry__image--main"'; break;
            case 'last': $class = ' class="detail__list-image--last"'; break;
            default: $class=''; break;
        }

        $img = (!empty($path)) ? '<img' . $class . ' src="' . $path . '" alt="' . esc_attr($cmmnt) . '">' : '';
        
        switch($type){
            case 'list': 
                $img = '<a href="' . $path . '" data-lightbox="group" data-title="' . esc_attr($cmmnt) . '">' . $img . '</a>';
                $cmmnt = (!empty($cmmnt)) ? '<p class="detail__list-cmmnt">' . $cmmnt . '</p>' : '';
                $img = '<li class="detail__list">' . $cmmnt . $img . '</li>';
                break;
                
            case 'main': 
                $cmmnt = (!empty($cmmnt)) ? '<p class="entry__image-main-cmmnt">' . $cmmnt . '</p>' : '';
                $img = '<div class="entry__image">' . $img . $cmmnt . '</div>';
                break;
				
			case 'last':
                $img = '<a href="' . $path . '" data-lightbox="group" data-title="' . esc_attr($cmmnt) . '">' . $img . '</a>';
                $cmmnt = (!empty($cmmnt)) ? '<p class="detail__list-cmmnt">' . $cmmnt . '</p>' : '';
                $img = '<div class="entry__image">' . $cmmnt . $img . '</div>';
				break;
                
            default:
                break;
        }
        return $img;
    }else{
        return '';
    }
}

/**
 * アーカイブタイトルのフォーマットをカスタマイズ
 * 
 * @return string 修正されたアーカイブタイトル
 */
function custom_archive_title()
{
    if (function_exists('is_category') && is_category()) {
        $title = function_exists('single_cat_title') ? single_cat_title('', false) : '';
        $title = 'WORKS: ' . strtoupper($title);
		return $title;
    }else{
		return;
	}
}
add_filter('get_the_archive_title', 'custom_archive_title');

/**
 * タグのスタイル付きHTMLリンクを生成する関数
 * 
 * @param array $tags タグオブジェクトの配列
 * @return string|void タグのHTMLリンクを連結した文字列、または空の場合はvoid
 * 
 * 各タグに対して:
 * - タグ名を大文字に変換
 * - タグページへのリンクを生成
 * - HTMLエスケープ処理を実施
 * - aタグでマークアップ
 */
function get_styled_tags($tags=''){
    if(empty($tags)){
        return;
    }else{
        $result = array();
        foreach($tags as $tag) {
            if($tag){
                $result[] = '<a class="tag__link" href="' . esc_url(home_url('/tags/' . sanitize_title($tag->name))) . '" rel="tag">' . esc_html(strtoupper($tag->name)) . '(' . esc_html($tag->count) . ')</a>';
            }
        }
        return implode('', $result);
    }
}

/**
 * ページトップスクロール機能の初期化
 */
function jq_page_top()
{
	echo <<< here
	var pagetop = $('.page-top');
	pagetop.hide();
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			pagetop.fadeIn();
		} else {
			pagetop.fadeOut();
		}
	});
	pagetop.click(function () {
		$('body, html').animate({ scrollTop: 0 }, 500);
		return false;
	});
	
	here;
}

/**
 * サムネイルの幅を取得（デフォルト値あり）
 * 
 * @param int $num カスタム幅の値
 * @return int サムネイルの幅
 */
function get_thumbnail_width($is_mobile = 'N') {
    $width_size = ($is_mobile === 'N') ? intval(300) : intval(300);
    return $width_size;
}

/**
 * Masonryレイアウトの初期化
 * 
 * @param string $container コンテナのセレクタ
 * @param string $class アイテムのクラスセレクタ
 * @param string $thumb サムネイルの幅
 */
function jq_masonry($container='', $class='', $thumb='')
{
	if(empty($container) && empty($class)){
		return;
	}
	else{
		echo <<< here
		var \$container = $('${container}');
			\$container.imagesLoaded(function(){
				\$container.masonry({
					itemSelector: '${class}',
					columnWidth: ${thumb},
					isFitWidth: true,
					isAnimated: true,
					isResizeBound: true
				});
			});
		
		here;
	}
}

/**
 * onScreenアニメーション機能の初期化
 * 
 * 要素が画面内に入ったときにフェードイン、画面外に出たときにフェードアウトする
 * アニメーション効果を適用します。
 * 
 * @param string $class 対象要素のクラス
 * @return void
 */
function jq_on_screen($class='')
{
    if(empty($class)){
        return;
    }
    else{
        echo <<< here
        document.addEventListener('DOMContentLoaded', function() {
            // 初期状態を設定
            $("${class}").css('opacity', '0');
            
            // onScreenプラグインが利用可能か確認
            if (typeof $.fn.onScreen === 'undefined') {
                console.warn('onScreen plugin is not loaded');
                // フォールバック: 単純なフェードイン
                $("${class}").animate({ opacity: 1 }, 800);
                return;
            }
            
            $("${class}").onScreen({
                doIn: function(){
                    $(this).animate({
                        opacity: 1
                    }, 800);
                },
                doOut: function(){
                    $(this).animate({
                        opacity: 0
                    }, 800);
                },
                tolerance: 100,  // より広い範囲で検出
                throttle: 100,   // より少ない頻度でチェック
                toggleClass: 'onScreen',
                lazyAttr: null
            });
        });
        here;
    }
}

/**
 * ハンバーガーメニューの初期化と制御
 * 
 * 以下の機能を提供します：
 * - フッターメニューの内容をハンバーガーメニューとして表示
 * - メニューの開閉アニメーション
 * - メニュー表示時のスクロール制御
 * - メニュー外クリック時の自動閉じる機能
 * - タッチデバイス対応（Android/iOS）
 * - iOS Safari対応（ダブルタップズーム防止、バウンススクロール制御）
 * 
 * @return string ハンバーガーメニュー制御用のJavaScriptコード
 */
function jq_hamburger_menu()
{
    echo <<< here
    jQuery(document).ready(function($) {
        // iOS Safariのバウンススクロールを防止
        document.body.addEventListener('touchmove', function(e) {
            if ($('.hamburger-menu').hasClass('is-active')) {
                e.preventDefault();
            }
        }, { passive: false });

        // ダブルタップズームを防止
        var lastTouchEnd = 0;
        document.addEventListener('touchend', function(e) {
            var now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // ハンバーガーメニューのHTMLを追加
        var menuContent = $('.footer__menu').html();
        // 特殊文字を削除
        menuContent = menuContent.replace(/\[|\]|\||&nbsp;/g, '');
        
        var hamburgerHTML = 
            '<div class="hamburger">' +
                '<span class="hamburger__line"></span>' +
                '<span class="hamburger__line"></span>' +
                '<span class="hamburger__line"></span>' +
            '</div>' +
            '<div class="hamburger-menu">' +
                '<div class="hamburger-menu__list">' +
                    menuContent +
                '</div>' +
            '</div>';
        
        $('body').append(hamburgerHTML);

        // スクロール位置を保存する変数
        var scrollPosition = 0;
        var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        // メニューの開閉を制御する関数
        function toggleMenu(isOpen) {
            $('.hamburger').toggleClass('is-active', isOpen);
            $('.hamburger-menu').toggleClass('is-active', isOpen);
            
            if (isOpen) {
                // メニューを開くとき
                scrollPosition = window.pageYOffset;
                if (isIOS) {
                    // iOS Safari用の特別な処理
                    $('body').css({
                        'position': 'fixed',
                        'width': '100%',
                        'top': -scrollPosition + 'px',
                        'overflow': 'hidden',
                        '-webkit-overflow-scrolling': 'touch'
                    });
                    // iOS Safariのバウンススクロールを防止
                    document.body.style.height = '100%';
                } else {
                    $('body').css({
                        'position': 'fixed',
                        'width': '100%',
                        'top': -scrollPosition + 'px',
                        'overflow': 'hidden'
                    });
                }
            } else {
                // メニューを閉じるとき
                if (isIOS) {
                    $('body').css({
                        'position': '',
                        'width': '',
                        'top': '',
                        'overflow': '',
                        '-webkit-overflow-scrolling': '',
                        'height': ''
                    });
                } else {
                    $('body').css({
                        'position': '',
                        'width': '',
                        'top': '',
                        'overflow': ''
                    });
                }
                window.scrollTo(0, scrollPosition);
            }
        }

        // タッチイベントとクリックイベントの両方に対応
        var touchStartX = 0;
        var touchStartY = 0;
        var touchEndX = 0;
        var touchEndY = 0;
        
        $('.hamburger').on('touchstart click', function(e) {
            if (e.type === 'touchstart') {
                touchStartX = e.originalEvent.touches[0].clientX;
                touchStartY = e.originalEvent.touches[0].clientY;
            }
            e.preventDefault();
        });

        $('.hamburger').on('touchend click', function(e) {
            if (e.type === 'touchend') {
                touchEndX = e.originalEvent.changedTouches[0].clientX;
                touchEndY = e.originalEvent.changedTouches[0].clientY;
                // スワイプの判定（縦方向のスワイプを除外）
                var deltaX = Math.abs(touchEndX - touchStartX);
                var deltaY = Math.abs(touchEndY - touchStartY);
                if (deltaX < 10 && deltaY < 10) {
                    toggleMenu(!$('.hamburger').hasClass('is-active'));
                }
            } else {
                toggleMenu(!$('.hamburger').hasClass('is-active'));
            }
        });

        // メニューリンクのクリックイベント
        $('.hamburger-menu__link').on('touchstart click', function(e) {
            e.preventDefault();
            toggleMenu(false);
            // リンクの遷移を遅延実行
            setTimeout(function() {
                window.location.href = $(e.currentTarget).attr('href');
            }, 300);
        });

        // 画面外クリックでメニューを閉じる
        $(document).on('touchstart click', function(e) {
            if (!$(e.target).closest('.hamburger, .hamburger-menu').length) {
                if ($('.hamburger').hasClass('is-active')) {
                    toggleMenu(false);
                }
            }
        });

        // 画面回転時の処理
        $(window).on('resize orientationchange', function() {
            if ($('.hamburger').hasClass('is-active')) {
                // 画面回転時にメニューを閉じる
                toggleMenu(false);
            }
        });

        // iOS Safariのバックグラウンド/フォアグラウンド切り替え時の処理
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible' && $('.hamburger').hasClass('is-active')) {
                toggleMenu(false);
            }
        });
    });
    here;
}

/**
 * スプラッシュ画面の制御機能を初期化
 * 
 * スプラッシュ画面を指定時間後にフェードアウトさせる機能を提供します。
 * 
 * @param int $time フェードアウトまでの待機時間（ミリ秒）
 * @return string スプラッシュ制御用のJavaScriptコード
 */
function jq_splash($time = 3000)
{
    echo <<< here
    function splash(param) {
        // パラメータの検証
        if (typeof param !== 'number' || isNaN(param) || param < 0) {
            console.warn('Invalid parameter for splash function. Expected a positive number.');
            param = ${time}; // デフォルト値
        }

        // jQueryの存在確認
        if (typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // スプラッシュ要素の存在確認
        var \$splash = jQuery('.splash');
        if (\$splash.length === 0) {
            console.warn('Splash element not found');
            return;
        }

        // フェードアウト処理
        setTimeout(function() {
            \$splash.fadeOut(500, function() {
                // フェードアウト完了後のコールバック
                \$splash.remove(); // 要素を完全に削除
            });
        }, param);
    }
    here;
}