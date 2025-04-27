<?php

/**
 * Plugin Name: Yoshizumi Animation Effects
 * Description: ローディング・ページ遷移・スクロールのアニメーションを統合するプラグイン。
 * Version: 1.0.4
 * Author: Yoshizumi
 */

if (!defined('ABSPATH')) exit;

// CSSとJSの読み込み
function yae_enqueue_assets()
{
    $plugin_url = plugin_dir_url(__FILE__);

    // AOS CSS + JS 読み込み
    wp_enqueue_style('aos-css', $plugin_url . 'assets/vendor/aos/aos.css');
    wp_enqueue_script('aos-js', $plugin_url . 'assets/vendor/aos/aos.js', array(), null, true);


    wp_enqueue_style('yae-style', $plugin_url . 'assets/css/style.css');
    wp_enqueue_script('yae-script', $plugin_url . 'assets/js/main.js', array('jquery'), null, true);

    wp_localize_script('yae-script', 'yaeSettings', array(
        'globalLoaderEnabled' => get_option('yae_global_loader_enabled', '1') === '1',

        'loaderType' => get_option('yae_loader_type', 'spinner'),
        'loaderSpeed' => get_option('yae_loader_speed', 'normal'),
        'dotsColorOption' => get_option('yae_dots_color_option', 'black'),
        'dotsColor' => get_option('yae_dots_color', '#333333'),
        'dotsSizeOption' => get_option('yae_dots_size_option', 'medium'),
        'dotsSize' => get_option('yae_dots_size', 14),
        'logoImage' => get_option('yae_logo_image', ''),
        'logoSizeOption' => get_option('yae_logo_size_option', 'medium'),
        'transitionSlideOut' => get_option('yae_slide_out_enabled', '1') === '1',
        'transitionDiagonal' => get_option('yae_slide_diagonal_enabled', '0') === '1',
        'transitionOverlayColorOption' => get_option('yae_transition_overlay_color_option', 'black'),
        'transitionOverlayColor' => get_option('yae_transition_overlay_color', '#111111'),
        'transitionOverlayOpacity' => get_option('yae_transition_overlay_opacity', '100'),
        'gradientColorOption' => get_option('yae_gradient_color_option', 'bluegreen'),
        'gradientColor1' => get_option('yae_gradient_color_1', '#0073aa'),
        'gradientColor2' => get_option('yae_gradient_color_2', '#43a047'),

    ));
}
add_action('wp_enqueue_scripts', 'yae_enqueue_assets');

// 管理画面のスタイルを読み込む
function yae_enqueue_admin_styles($hook)
{
    // 設定ページのみに限定（オプション：条件付きで最適化）
    if ($hook !== 'toplevel_page_yoshizumi-animation-effects') return;
    wp_enqueue_media();
    wp_enqueue_style(
        'yae-admin-style',
        plugin_dir_url(__FILE__) . 'assets/css/admin-style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin-style.css')
    );
}
add_action('admin_enqueue_scripts', 'yae_enqueue_admin_styles');




// 管理画面に設定ページを追加
function yae_add_admin_menu()
{
    add_menu_page(
        'Yoshizumi Animation Effects',       // ページタイトル
        'Yoshizumi AE',               // メニューに表示される名前
        'manage_options',                    // 権限
        'yoshizumi-animation-effects',       // スラッグ
        'yae_settings_page',                 // コールバック関数
        'dashicons-admin-customizer',        // アイコン（お好みで変更可）
        60                                   // 表示位置（数値が小さいほど上に）
    );
}

add_action('admin_menu', 'yae_add_admin_menu');

function yae_settings_page()
{
?>
    <div class="wrap">
        <h1>Yoshizumi Animation Effects 設定</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('yae_settings');
            do_settings_sections('yae_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">ローディングを有効にする</th>
                    <td>
                        <label>
                            <input type="checkbox" name="yae_global_loader_enabled" value="1" <?php checked(get_option('yae_global_loader_enabled', '1'), '1'); ?> />
                            全体でローディングアニメーションを有効にする
                        </label>
                        <p class="description">※ このチェックを外すと、すべてのページでローディングが無効になります（個別設定も無効になります）。</p>
                        <p class="description">
                            ※ 特定のページのみローディングを無効にしたい場合は、各ページの編集画面にある<strong>「Yoshizumi Animation Effects 設定」</strong>のチェックボックスから制御できます。
                        </p>

                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">ローディングの種類</th>
                    <td>
                        <select name="yae_loader_type">
                            <option value="spinner" <?php selected(get_option('yae_loader_type'), 'spinner'); ?>>スピナー</option>
                            <option value="progress" <?php selected(get_option('yae_loader_type'), 'progress'); ?>>プログレスバー</option>
                            <option value="dots" <?php selected(get_option('yae_loader_type'), 'dots'); ?>>ドットアニメーション</option>
                            <option value="logo" <?php selected(get_option('yae_loader_type'), 'logo'); ?>>ロゴアニメーション</option> <!-- ★追加 -->
                            <option value="transition" <?php selected(get_option('yae_loader_type'), 'transition'); ?>>
                                スライドオーバーレイ
                            </option>
                            <option value="gradient" <?php selected(get_option('yae_loader_type'), 'gradient'); ?>>グラデーション</option>


                        </select>

                    </td>
                </tr>
                <tr valign="top" id="yae_loader_speed_row">
                    <th scope="row">ローディング速度</th>
                    <td>
                        <select name="yae_loader_speed">
                            <option value="fast" <?php selected(get_option('yae_loader_speed'), 'fast'); ?>>速い</option>
                            <option value="normal" <?php selected(get_option('yae_loader_speed'), 'normal'); ?>>普通</option>
                            <option value="slow" <?php selected(get_option('yae_loader_speed'), 'slow'); ?>>ゆっくり</option>
                        </select>
                    </td>
                </tr>

                <tr valign="top" id="yae_dots_color_row">
                    <th scope="row">ドットの色</th>
                    <td>
                        <?php $color_opt = get_option('yae_dots_color_option', 'black'); ?>
                        <select name="yae_dots_color_option" id="yae_dots_color_option">
                            <option value="black" <?php selected($color_opt, 'black'); ?>>ブラック</option>
                            <option value="blue" <?php selected($color_opt, 'blue'); ?>>ブルー</option>
                            <option value="red" <?php selected($color_opt, 'red'); ?>>レッド</option>
                            <option value="green" <?php selected($color_opt, 'green'); ?>>グリーン</option>
                            <option value="custom" <?php selected($color_opt, 'custom'); ?>>カスタム</option>
                        </select><br><br>

                        <?php $custom_color = get_option('yae_dots_color', '#333333'); ?>
                        <p>
                            <input type="color" name="yae_dots_color" id="yae_dots_color" value="<?php echo esc_attr($custom_color); ?>" />
                            <span class="description">※ カスタムを選択した場合のみ適用されます。</span>
                        </p>
                    </td>
                </tr>


                <tr valign="top" id="yae_dots_size_row">
                    <th scope="row">ドットのサイズ</th>
                    <td>
                        <?php $size_opt = get_option('yae_dots_size_option', 'medium'); ?>
                        <select name="yae_dots_size_option" id="yae_dots_size_option">
                            <option value="small" <?php selected($size_opt, 'small'); ?>>小（14px）</option>
                            <option value="medium" <?php selected($size_opt, 'medium'); ?>>中（18px）</option>
                            <option value="large" <?php selected($size_opt, 'large'); ?>>大（22px）</option>
                            <option value="custom" <?php selected($size_opt, 'custom'); ?>>カスタム</option>
                        </select>
                        <br><br>

                        <?php $custom_size = get_option('yae_dots_size', 14); ?>
                        <p>
                            <input type="number" name="yae_dots_size" id="yae_dots_size" value="<?php echo esc_attr($custom_size); ?>" min="10" max="30" />
                            <span>px</span><br>
                            <span class="description">※ カスタムを選択した場合のみ適用されます（10〜30px の間で指定可能です）。</span>
                        </p>
                    </td>
                </tr>



                <tr valign="top" id="yae_logo_image_row">
                    <th scope="row">ロゴ画像のアップロード</th>
                    <td>
                        <?php $logo_url = get_option('yae_logo_image'); ?>
                        <input type="hidden" id="yae_logo_image" name="yae_logo_image" value="<?php echo esc_attr($logo_url); ?>" />

                        <input type="button" class="button" id="yae_logo_image_button" value="メディアライブラリから選択" />
                        <p class="description">※ 未設定の場合はカスタマイザーで指定されたロゴが表示されます。</p>

                        <?php if ($logo_url): ?>
                            <div id="yae_logo_preview_wrapper" style="margin-top: 10px;">
                                <img id="yae_logo_preview" src="<?php echo esc_url($logo_url); ?>" alt="ロゴプレビュー" style="max-width: 200px; height: auto; border: 2px solid #ccc; padding: 10px; background: #eee;" />
                            </div>
                        <?php else: ?>
                            <div id="yae_logo_preview_wrapper" style="margin-top: 10px; display: none;">
                                <img id="yae_logo_preview" src="" alt="ロゴプレビュー" style="max-width: 200px; height: auto; border: 1px solid #ccc; padding: 4px; background: #fff;" />
                            </div>
                        <?php endif; ?>

                    </td>
                </tr>

                <tr valign="top" id="yae_logo_size_row">
                    <th scope="row">ロゴの表示サイズ</th>
                    <td>
                        <?php $size_option = get_option('yae_logo_size_option', 'medium'); ?>
                        <select name="yae_logo_size_option" id="yae_logo_size_option">
                            <option value="small" <?php selected($size_option, 'small'); ?>>小（180px）</option>
                            <option value="medium" <?php selected($size_option, 'medium'); ?>>中（240px）</option>
                            <option value="large" <?php selected($size_option, 'large'); ?>>大（300px）</option>
                            <option value="custom" <?php selected($size_option, 'custom'); ?>>カスタム</option>
                        </select><br><br>

                        <p id="yae_logo_width_wrapper">
                            <input type="number" name="yae_logo_width" id="yae_logo_width" value="<?php echo esc_attr(get_option('yae_logo_width', 200)); ?>" min="50" max="600" />
                            <span>px</span><br>
                            <span class="description">
                                ※ カスタムを選択した場合のみ適用されます（50〜600px の間で指定可能です）。
                            </span>
                        </p>
                    </td>
                </tr>

                <tr valign="top" id="yae_slide_out_row">
                    <th scope="row">スライドアウトを有効にする</th>
                    <td>
                        <label>
                            <input type="checkbox" name="yae_slide_out_enabled" value="1" <?php checked(get_option('yae_slide_out_enabled', '1'), '1'); ?> />
                            遷移後にスライドアウトさせる
                        </label>
                        <p class="description">※ オンにすると、次ページ表示時にオーバーレイが右から左にスライドアウトします。</p>
                    </td>
                </tr>

                <tr valign="top" id="yae_slide_diagonal_row">
                    <th scope="row">オーバーレイを斜めにする</th>
                    <td>
                        <label>
                            <input type="checkbox" name="yae_slide_diagonal_enabled" value="1" <?php checked(get_option('yae_slide_diagonal_enabled', '0'), '1'); ?> />
                            有効にする
                        </label>
                    </td>
                </tr>


                <tr valign="top" id="yae_transition_overlay_color_row">
                    <th scope="row">スライドオーバーレイの背景色</th>
                    <td>
                        <select name="yae_transition_overlay_color_option" id="yae_transition_overlay_color_option">
                            <option value="black" <?php selected(get_option('yae_transition_overlay_color_option'), 'black'); ?>>ブラック</option>
                            <option value="blue" <?php selected(get_option('yae_transition_overlay_color_option'), 'blue'); ?>>ブルー</option>
                            <option value="red" <?php selected(get_option('yae_transition_overlay_color_option'), 'red'); ?>>レッド</option>
                            <option value="green" <?php selected(get_option('yae_transition_overlay_color_option'), 'green'); ?>>グリーン</option>
                            <option value="custom" <?php selected(get_option('yae_transition_overlay_color_option'), 'custom'); ?>>カスタム</option>
                        </select><br><br>

                        <?php $custom_color = get_option('yae_transition_overlay_color', '#111111'); ?>
                        <p>
                            <input type="color" name="yae_transition_overlay_color" id="yae_transition_overlay_color" value="<?php echo esc_attr($custom_color); ?>" />
                            <span class="description">※ カスタムを選択した場合のみ適用されます。</span>
                        </p>
                    </td>
                </tr>

                <tr valign="top" id="yae_transition_overlay_opacity_row">
                    <th scope="row">オーバーレイの透明度</th>
                    <td>
                        <input type="number" name="yae_transition_overlay_opacity" id="yae_transition_overlay_opacity" value="<?php echo esc_attr(get_option('yae_transition_overlay_opacity', '100')); ?>" min="0" max="100" step="1" />
                        <span class="description">※ 0 で完全透明、100 で完全不透明です。</span>
                    </td>
                </tr>

                <tr valign="top" id="yae_gradient_colors_row">
                    <th scope="row">グラデーションの色</th>
                    <td>
                        <?php $selected = get_option('yae_gradient_color_option', 'bluegreen'); ?>
                        <div id="yae_gradient_presets">
                            <?php
                            $presets = [
                                'bluegreen' => ['#0073aa', '#43a047'],
                                'redorange' => ['#d32f2f', '#fdd835'],
                                'purplepink' => ['#8e24aa', '#ec407a'],
                                'custom' => null,
                            ];
                            foreach ($presets as $key => $colors) {
                                $is_checked = checked($selected, $key, false);
                                $style = $colors ? "background: linear-gradient(90deg, {$colors[0]}, {$colors[1]});" : '';

                                // PHP7対応：matchの代わりにswitchを使用
                                switch ($key) {
                                    case 'bluegreen':
                                        $label = 'ブルー×グリーン';
                                        break;
                                    case 'redorange':
                                        $label = 'レッド×オレンジ';
                                        break;
                                    case 'purplepink':
                                        $label = 'パープル×ピンク';
                                        break;
                                    case 'custom':
                                        $label = 'カスタム';
                                        break;
                                    default:
                                        $label = '';
                                        break;
                                }

                                echo "<label style='display:inline-block; margin-right: 10px; cursor:pointer;'>
        <input type='radio' name='yae_gradient_color_option' value='{$key}' {$is_checked} />
        <span class='yae-gradient-preview' style='{$style}'></span>
        {$label}
    </label>";
                            }

                            ?>
                        </div>
                        <div id="yae_gradient_custom_colors" style="margin-top:10px;">
                            <input type="color" name="yae_gradient_color_1" value="<?php echo esc_attr(get_option('yae_gradient_color_1', '#0073aa')); ?>" />
                            ～
                            <input type="color" name="yae_gradient_color_2" value="<?php echo esc_attr(get_option('yae_gradient_color_2', '#43a047')); ?>" />
                            <span class="description">※ カスタムを選択した場合のみ有効です。</span>
                        </div>
                    </td>
                </tr>












            </table>
            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // ▼ ローディングタイプに応じて表示項目を切り替え
            function toggleLoaderFields() {
                const selected = $('select[name="yae_loader_type"]').val();
                const isDots = selected === 'dots';
                const isLogo = selected === 'logo' || selected === 'gradient';
                const isTransition = selected === 'transition';
                const isGradient = selected === 'gradient';


                // ▼ ローディング速度が不要なタイプ一覧（今後追加しやすいように）
                const speedNotNeededTypes = ['transition'];
                const isSpeedHidden = speedNotNeededTypes.includes(selected);

                // ドット関連
                $('#yae_dots_color_row').toggle(isDots);
                $('#yae_dots_size_row').toggle(isDots);
                toggleDotsColorField();
                toggleDotsSizeField();

                // ロゴ関連
                $('#yae_logo_image_row').toggle(isLogo);
                $('#yae_logo_size_row').toggle(isLogo);
                $('#yae_logo_preview_wrapper').toggle(isLogo);

                // ▼ ローディング速度設定（対象タイプでは非表示）
                $('#yae_loader_speed_row').toggle(!isSpeedHidden);

                // ▼ スライドオーバーレイ関連
                $('#yae_slide_out_row').toggle(isTransition);
                $('#yae_slide_diagonal_row').toggle(isTransition);
                $('#yae_transition_overlay_color_row').toggle(isTransition);
                $('#yae_transition_overlay_opacity_row').toggle(isTransition);

                // ▼ グラデーション関連
                $('#yae_gradient_colors_row').toggle(isGradient);

            }


            // ▼ ドット色セレクトに応じてカラーピッカーを表示／非表示
            function toggleDotsColorField() {
                const colorOption = $('#yae_dots_color_option').val();
                $('#yae_dots_color').closest('p').toggle(colorOption === 'custom');
            }

            // ▼ ドットサイズセレクトに応じて数値入力を表示／非表示
            function toggleDotsSizeField() {
                const sizeOption = $('#yae_dots_size_option').val();
                $('#yae_dots_size').closest('p').toggle(sizeOption === 'custom');
            }

            // ▼ ロゴのカスタムサイズ表示制御
            function toggleCustomWidthField() {
                const sizeOption = $('select[name="yae_logo_size_option"]').val();
                $('#yae_logo_width').closest('p').toggle(sizeOption === 'custom');
            }

            // ▼ メディアアップローダー（ロゴ画像選択）
            $('#yae_logo_image_button').on('click', function(e) {
                e.preventDefault();
                const custom_uploader = wp.media({
                        title: 'ロゴ画像を選択',
                        button: {
                            text: 'この画像を使用'
                        },
                        multiple: false
                    })
                    .on('select', function() {
                        const attachment = custom_uploader.state().get('selection').first().toJSON();
                        $('#yae_logo_image').val(attachment.url);
                        $('#yae_logo_preview').attr('src', attachment.url);
                        $('#yae_logo_preview_wrapper').show();
                    })
                    .open();
            });



            // ▼ スライドオーバーレイ背景色のカラーピッカー表示制御
            function toggleOverlayColorPicker() {
                const option = $('#yae_transition_overlay_color_option').val();
                $('#yae_transition_overlay_color').closest('p').toggle(option === 'custom');
            }

            // ▼ グラデーションのカスタム色表示制御
            function toggleGradientColorPicker() {
                const selected = $('input[name="yae_gradient_color_option"]:checked').val();
                $('#yae_gradient_custom_colors').toggle(selected === 'custom');
            }


            // ▼ 初期実行
            toggleLoaderFields();
            toggleDotsColorField();
            toggleDotsSizeField();
            toggleCustomWidthField();
            toggleOverlayColorPicker();
            toggleGradientColorPicker();

            // ▼ イベント監視
            $('select[name="yae_loader_type"]').on('change', toggleLoaderFields);
            $('#yae_dots_color_option').on('change', toggleDotsColorField);
            $('#yae_dots_size_option').on('change', toggleDotsSizeField);
            $('select[name="yae_logo_size_option"]').on('change', toggleCustomWidthField);
            $('#yae_transition_overlay_color_option').on('change', toggleOverlayColorPicker);
            $('input[name="yae_gradient_color_option"]').on('change', toggleGradientColorPicker);


        });
    </script>




<?php
}

// 設定を登録
function yae_register_settings()
{
    register_setting('yae_settings', 'yae_global_loader_enabled');

    register_setting('yae_settings', 'yae_loader_type');
    register_setting('yae_settings', 'yae_loader_speed'); // ★ 追加：スピードの保存
    register_setting('yae_settings', 'yae_logo_image');   // ★追加

    register_setting('yae_settings', 'yae_dots_color_option');
    register_setting('yae_settings', 'yae_dots_color');
    register_setting('yae_settings', 'yae_dots_size_option');
    register_setting('yae_settings', 'yae_dots_size');

    register_setting('yae_settings', 'yae_logo_size_option'); // ★ プリセット選択肢
    register_setting('yae_settings', 'yae_logo_width');       // ★ カスタム数値

    register_setting('yae_settings', 'yae_slide_out_enabled');
    register_setting('yae_settings', 'yae_slide_diagonal_enabled');
    register_setting('yae_settings', 'yae_transition_overlay_color_option');
    register_setting('yae_settings', 'yae_transition_overlay_color');
    register_setting('yae_settings', 'yae_transition_overlay_opacity');

    register_setting('yae_settings', 'yae_gradient_color_option');

    register_setting('yae_settings', 'yae_gradient_color_1'); // グラデーションの色（1色目）
    register_setting('yae_settings', 'yae_gradient_color_2'); // グラデーションの色（2色目）
}
add_action('admin_init', 'yae_register_settings');

// ローディングHTMLを出力
function yae_add_loader_html()
{
    if (get_option('yae_global_loader_enabled', '1') !== '1') return; // グローバルで無効化

    if (is_singular()) {
        $post_id = get_queried_object_id();
        if (get_post_meta($post_id, '_yae_disable_loader', true)) return; // 個別で無効化
    }

    $type = get_option('yae_loader_type', 'spinner');

    if ($type === 'progress') {
        echo '
        <div id="yae-loader">
            <div class="yae-percentage">0%</div>
            <div class="yae-progress-container">
                <div class="yae-progress-bar"></div>
            </div>
        </div>';
    } elseif ($type === 'dots') {
        echo '
        <div id="yae-loader">
            <div class="yae-dots">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>';
    } elseif ($type === 'logo') {
        $logo_url = get_option('yae_logo_image');
        $size_option = get_option('yae_logo_size_option', 'medium');

        // 幅を選択肢に応じて決定
        switch ($size_option) {
            case 'small':
                $width = 180;
                break;
            case 'medium':
                $width = 240;
                break;
            case 'large':
                $width = 300;
                break;
            case 'custom':
            default:
                $width = intval(get_option('yae_logo_width', 200));
                break;
        }

        // カスタマイザーロゴを fallback に使用
        if (!$logo_url && function_exists('get_custom_logo')) {
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            }
        }

        if ($logo_url) {
            echo '
        <div id="yae-loader">
            <img src="' . esc_url($logo_url) . '" class="yae-logo" style="max-width:' . $width . 'px;" />
        </div>';
        } else {
            echo '<div id="yae-loader"><div class="yae-logo-placeholder">Logo</div></div>';
        }
    } elseif ($type === 'transition') {
        // ▼ スライドオーバーレイ（ページ遷移）のみ表示
        echo '<div class="yae-slide-overlay" style="display:none;"></div>';
    } elseif ($type === 'gradient') {
        // グラデーションはJS側で動的に生成されるため、ここではHTML出力しない
    } else {
        echo '
        <div id="yae-loader">
            <div class="yae-spinner"></div>
        </div>';
    }
}


add_action('wp_body_open', 'yae_add_loader_html');


function yae_add_loading_metabox()
{
    // 投稿タイプ一覧を取得（公開されているものだけ）
    $post_types = get_post_types(['public' => true], 'names');

    foreach ($post_types as $type) {
        add_meta_box(
            'yae_loader_metabox',
            'Yoshizumi Animation Effects 設定',
            'yae_render_loader_metabox',
            $type,
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'yae_add_loading_metabox');

function yae_render_loader_metabox($post)
{
    // nonce セキュリティ
    wp_nonce_field('yae_loader_metabox_nonce_action', 'yae_loader_metabox_nonce');

    $value = get_post_meta($post->ID, '_yae_disable_loader', true);
?>
    <label for="yae_disable_loader">
        <input type="checkbox" name="yae_disable_loader" id="yae_disable_loader" value="1" <?php checked($value, '1'); ?> />
        このページではローディングを無効にする
    </label>
<?php
}

function yae_save_loader_metabox($post_id)
{
    // nonce チェック
    if (!isset($_POST['yae_loader_metabox_nonce']) || !wp_verify_nonce($_POST['yae_loader_metabox_nonce'], 'yae_loader_metabox_nonce_action')) {
        return;
    }

    // 自動保存・権限チェック
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // チェックボックスがオンなら保存、オフなら削除
    if (isset($_POST['yae_disable_loader'])) {
        update_post_meta($post_id, '_yae_disable_loader', '1');
    } else {
        delete_post_meta($post_id, '_yae_disable_loader');
    }
}
add_action('save_post', 'yae_save_loader_metabox');


function yae_enqueue_block_editor_assets()
{
    $script_path = plugin_dir_path(__FILE__) . 'build/index.asset.php';
    $script_url = plugins_url('build/index.js', __FILE__);

    if (!file_exists($script_path)) {
        return; // ファイルがないなら何もしない
    }

    $asset_data = include $script_path;

    wp_register_script(
        'yae-block-editor',
        $script_url,
        $asset_data['dependencies'],
        $asset_data['version'],
        true
    );

    wp_enqueue_script('yae-block-editor');

    wp_enqueue_style(
        'yae-block-editor-style',
        plugins_url('assets/css/editor-style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/editor-style.css')
    );
}
add_action('enqueue_block_editor_assets', 'yae_enqueue_block_editor_assets');
