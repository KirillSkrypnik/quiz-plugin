<?php
/*
Plugin Name: Quiz Imedia Team
Plugin URI: https://imedia.studio/
Description: A plugin that allows you to create surveys for users. After completing the form, the selected options are sent to the administrator by email.
Version: 1.01
Author: Kirill Skrypnik
Author URI: https://github.com/KirillSkrypnik
Text Domain: quiz-imedia-team
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Выход, если файл запущен напрямую, а не через WordPress.
}

register_activation_hook(__FILE__, function() {
    // проверяем права пользователя на установку плагинов
    if (!current_user_can('activate_plugins')) {
        return;
    }
});

register_deactivation_hook(__FILE__, function() {
    // проверяем права пользователя на деактивацию плагинов
    if (!current_user_can('deactivate_plugins')) {
        return;
    }
});

add_action('init', function () {
    $labels = [
        'name' => __('Квиз', 'quiz-imedia-team'),
        'menu_name' => __('Квизы', 'quiz-imedia-team'),
        'singular_name' => __('Квиз', 'quiz-imedia-team'),
        'add_new' => __('Добавить квиз', 'quiz-imedia-team'),
        'add_new_item' => __('Добавить новый квиз', 'quiz-imedia-team'),
        'edit_item' => __('Редактировать квиз', 'quiz-imedia-team'),
        'new_item' => __('Новый квиз', 'quiz-imedia-team'),
        'all_items' => __('Все квизы', 'quiz-imedia-team'),
        'view_item' => __('Посмотреть квиз', 'quiz-imedia-team'),
        'search_items' => __('Найти квиз', 'quiz-imedia-team'),
        'not_found' =>  __('Ничего не найдено', 'quiz-imedia-team'),
        'not_found_in_trash' => __('В корзине не найдено', 'quiz-imedia-team')
    ];
    $args = [
        'labels' => $labels,
        'description' => __('Описание квизов', 'quiz-imedia-team'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => 'quiz_imedia_settings',
        'query_var' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'rewrite' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'],
    ];
    if (function_exists('register_post_type')) {
        register_post_type('quiz', $args);
    }
});


/*
 * Добавляем страницу настроек плагина
 */
function quiz_add_menu_pages() {
    // Добавляем главную страницу меню для плагина "Квиз"
    add_menu_page(
        __('Плагин «Квиз»', 'quiz-imedia-team'), // Название страницы
        __('Плагин «Квиз»', 'quiz-imedia-team'), // Название в меню
        'manage_options', // Капабилити, необходимая для доступа к этой странице
        'quiz_imedia_settings', // Slug страницы меню
        'plugin_callback_dashboard', // Callback функция, отображающая содержимое страницы
        'dashicons-admin-settings', // Иконка для меню
        30 // Позиция в меню
    );

    // Добавляем подменю "Настройки" для плагина "Квиз"
    add_submenu_page(
        'quiz_imedia_settings', // Slug родительского меню
        __('Настройки Quiz Plugin', 'quiz-imedia-team'), // Название страницы
        __('Настройки', 'quiz-imedia-team'), // Название в меню
        'manage_options', // Капабилити, необходимая для доступа к этой странице
        'quiz_plugin_settings', // Slug страницы подменю
        'quiz_callback_settings' // Callback функция, отображающая содержимое страницы
    );
}

add_action('admin_menu', 'quiz_add_menu_pages');

// Callback функция для главной страницы меню
function plugin_callback_dashboard() {
    echo '<div class="wrap"><h2>' . esc_html__('Плагин «Квиз»', 'quiz-imedia-team') . '</h2><p>' . esc_html__('Главная страница плагина Квиз.', 'quiz-imedia-team') . '</p></div>';
}

// Callback функция для страницы настроек
function quiz_callback_settings() {
    echo '<div class="wrap"><h2>' . esc_html__('Настройки Quiz Plugin', 'quiz-imedia-team') . '</h2><p>' . esc_html__('Страница настроек плагина Квиз.', 'quiz-imedia-team') . '</p></div>';
    ?>
    <div class="wrap">
        <form method="post" action="options.php">
            <?php
            // Обновите идентификаторы здесь, добавив префикс 'qu_'
            settings_fields('qu_quiz_settings');
            do_settings_sections('qu_quiz_imedia_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}



/*Регистрируем опции для настроек*/

add_action('admin_init', 'qu_quiz_simple_setting');

function qu_quiz_simple_setting() {
    
    register_setting('qu_quiz_settings', 'qu_quiz_settings_options');
    
    add_settings_section('qu_quiz_settings_section', esc_html__('Настройки викторины', 'quiz-imedia-team'), 'qu_settings_section_callback', 'qu_quiz_imedia_settings');
    
    add_settings_field('qu_buttons_color', esc_html__('Выберите цвет кнопок', 'quiz-imedia-team'), 'qu_buttons_color_html_callback', 'qu_quiz_imedia_settings', 'qu_quiz_settings_section');
    add_settings_field('qu_main_color', esc_html__('Выберите основной цвет', 'quiz-imedia-team'), 'qu_main_color_html_callback', 'qu_quiz_imedia_settings', 'qu_quiz_settings_section');
    add_settings_field('qu_consent_to_processing', esc_html__('Текст согласия на обработку персональных данных', 'quiz-imedia-team'), 'qu_consent_to_processing_callback', 'qu_quiz_imedia_settings', 'qu_quiz_settings_section');
    add_settings_field('qu_letter_subject', esc_html__('Тема письма', 'quiz-imedia-team'), 'qu_letter_subject_callback', 'qu_quiz_imedia_settings', 'qu_quiz_settings_section');
}


function qu_settings_section_callback() {
    // Здесь можно добавить описание вашего раздела настроек, если нужно
    echo esc_html__('', 'quiz-imedia-team');
}


function qu_letter_subject_callback() {
    // Получение настроек плагина с уникальным префиксом
    $options = get_option('qu_quiz_settings_options');
    ?>
    <!-- Обновление атрибута name для использования уникального префикса -->
    <input type="text" name="qu_quiz_settings_options[qu_letter_subject]" value="<?php echo esc_attr($options['qu_letter_subject'] ?? ''); ?>" />
    <?php
}

function qu_consent_to_processing_callback() {
    $options = get_option('qu_quiz_settings_options');
    ?>
    <input type="text" name="qu_quiz_settings_options[qu_consent_to_processing]" value="<?php echo esc_attr($options['qu_consent_to_processing'] ?? ''); ?>" />
    <?php
}

function qu_buttons_color_html_callback() {
    $options = get_option('qu_quiz_settings_options');
    ?>
    <input type="color" name="qu_quiz_settings_options[qu_buttons_color]" value="<?php echo esc_attr($options['qu_buttons_color'] ?? ''); ?>" />
    <?php
}

function qu_main_color_html_callback() {
    $options = get_option('qu_quiz_settings_options');
    ?>
    <input type="color" name="qu_quiz_settings_options[qu_main_color]" value="<?php echo esc_attr($options['qu_main_color'] ?? ''); ?>" />
    <?php
}
/**/


// Подключаем css и js фронта
add_action('wp_enqueue_scripts', 'qu_quiz_init');

function qu_quiz_init() {
    // Подключаем jQuery из поставки WordPress
    wp_enqueue_script('jquery');
    // Подключаем скрипты и стили плагина
    wp_enqueue_script('qu_quiz_script', plugins_url('/js/quiz_script.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_style('qu_quiz_style', plugins_url('/css/quiz_style.css', __FILE__), array(), '1.0', 'all');
    
    wp_enqueue_script('qu_quiz_feedback', plugins_url('/js/quiz_sending.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('qu_quiz_feedback', 'qu_feedback_object', array(
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('qu_feedback-nonce'),
    ));
}


require_once('carbon-fields/carbon-fields-plugin.php');

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'qu_crb_attach_theme_options' );
function qu_crb_attach_theme_options() {
        Container::make( 'post_meta', __( 'Page Options', 'quiz-imedia-team' ) )
        ->show_on_post_type('quiz')
        ->add_tab( __('Квиз'), array(
            Field::make( 'text', 'quiz_final_title', 'Заголовок последнего квиза с формой' ),
            Field::make( 'text', 'quiz_final_content', 'Описание последнего квиза с формой' ),
            Field::make( 'image', 'quiz_final_background_image', 'Фоновая картинка последнего квиза с формой' )
            ->set_value_type( 'url' ),
            Field::make( 'checkbox', 'quiz_final_check', 'Полная ширина последнего квиза' )
            ->set_option_value( 'yes' ),
            Field::make( 'complex', 'quiz_final_social_media', 'Социальные сети последнего квиза' )
                ->set_layout( 'tabbed-horizontal' )
                ->add_fields( array(
                    Field::make( 'text', 'quiz_final_social_media_item_title', 'Название социальной сети' ),
                    Field::make( 'text', 'quiz_final_social_media_item_slug', 'Slug социальной сети(название социальной сети на английском маленькими буквами без пробелов, тире и нижнее подчеркивание допускается)' ),
                    Field::make( 'image', 'quiz_final_social_media_item_image', 'Иконку социальной сети' )
                    ->set_value_type( 'url' ),
                ) ),
            Field::make( 'complex', 'main_quiz_complex', 'Вопросы' )
                ->set_layout( 'tabbed-horizontal' )
                ->add_fields( array(
                    Field::make( 'text', 'question_quiz', 'Вопрос' ),
                    Field::make( 'checkbox', 'multiplicity_check', 'Несколько вариантов ответов' )
                    ->set_option_value( 'yes' ),
                    Field::make( 'complex', 'child_quiz_complex', 'Варианты ответов' )
                    ->set_layout( 'tabbed-horizontal' )
                    ->add_fields( array(
                        Field::make( 'text', 'answer_text', 'Варианты ответов текст' ),
                        Field::make( 'image', 'answer_image', 'Варианты картинок для ответов' )
                        ->set_value_type( 'url' ),
                    ) ),
                ) ),
        ) );
}

// Шорткод 
add_shortcode('qu_quizimedia', 'qu_call_shortcode_quiz');


function qu_call_shortcode_quiz($atts) {
    $atts = shortcode_atts(array('id' => ''), $atts);
    $post_id = intval($atts['id']);
    if (!$post_id) {
        return esc_html__('Quiz not found', 'quiz-imedia-team'); // Используйте ваш уникальный text-domain
    }

    $query = new WP_Query(array(
        'post_type' => 'quiz',
        'post__in' => array($post_id)
    ));
    
    $options = get_option('qu_quiz_settings_options');
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
                $i = 1;
                $b = 1;
                ob_start();
                /*Button color*/
            $qu_buttons_color = !empty($options['qu_buttons_color']) ? esc_attr($options['qu_buttons_color']) : '';
            $qu_main_color = !empty($options['qu_main_color']) ? esc_attr($options['qu_main_color']) : '';
                ?>
                <form class="quiz_form" id="quiz_form">
                    <?php 
                        $mainQuizComplexs = carbon_get_the_post_meta('main_quiz_complex'); 
                        foreach ($mainQuizComplexs as $mainQuizComplex){
                            $b++;
                        }
                    ?>
                    <div class="quiz_form_percent_wrapper" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>
                        <div class="quiz_form_percent">0</div><div>%</div>
                    </div>
                    <div class="progress_quiz_bar" data-loaditem="<?php echo esc_attr($b); ?>">
                        <div class="progress_quiz_bar_active" <?php if ($qu_main_color) { echo 'style="background-color:' . esc_attr($qu_main_color) . ';"'; } ?>></div>
                    </div>

                    <div class="quiz_section_item_wrapper">
                        <?php
                        foreach ($mainQuizComplexs as $mainQuizComplex){
                            ?>
                            <div data-item="<?php echo esc_attr($i); ?>" data-itemQuantity="" class="quiz_section_item <?php if ($i == 1) { echo 'quiz_section_item_active'; } ?>">
                                <div class="quiz_question" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>
                                    <span class="quiz_question_number"><?php echo $i < 10 ? '0' . esc_html($i) : esc_html($i); ?></span>
                                    <span class="quiz_question_text"><?php echo esc_html($mainQuizComplex['question_quiz']); ?></span>
                                </div>

                                <div class="quiz_content">
                                    <?php $j = 1; $mainQuizComplexLength = count($mainQuizComplex['child_quiz_complex']); foreach ($mainQuizComplex['child_quiz_complex'] as $file_item ) { ?>
                                        <label 
                                            data-name="label_quiz_radio_<?php echo esc_attr($i); ?>" 
                                            for="quiz_question_<?php echo esc_attr($i); ?>_<?php echo esc_attr($j); ?>" 
                                            class="quiz_content_item quiz_content_item_step_<?php echo esc_attr($j); ?> <?php echo $file_item['answer_image'] ? 'quiz_content_label_img' : 'quiz_content_only_text'; ?>"
                                        >
                                        <?php if ($file_item['answer_image']){ ?>
                                            <img src="<?php echo esc_url($file_item['answer_image']); ?>" class="quiz_content_image <?php if($mainQuizComplexLength < 4){ echo 'quiz_content_image_max_height'; } ?>"/>
                                        <?php } ?>
                                        <div class="quiz_content_status">
                                            <span class="quiz_content_status_span">
                                                <svg data-name="Layer 2" id="Layer_2" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg"><rect height="2.5" rx="1.25" transform="translate(-7.27 20.449) rotate(-45)" width="13.08" x="14.509" y="17.75"/><rect height="2.5" rx="1.25" transform="translate(11.395 46.123) rotate(-135)" width="8.267" x="11.116" y="19.451"/></svg>    
                                            </span>
                                        </div>
                                        <div class="quiz_content_text" <?php if($qu_main_color){ echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>><?php echo esc_html($file_item['answer_text']); ?></div>
                                        <input 
                                            id="quiz_question_<?php echo esc_attr($i); ?>_<?php echo esc_attr($j); ?>" 
                                            type="<?php echo $mainQuizComplex['multiplicity_check'] ? 'checkbox' : 'radio'; ?>" 
                                            name="quiz_radio_<?php echo esc_attr($i); ?>" 
                                            value="<?php echo esc_attr($file_item['answer_text']); ?>" 
                                            class="quiz_radio_hidden" 
                                        />
                                    </label>
                                    <?php $j++; } ?>
                                </div>

                            </div>
                            <?php
                        $i++;
                        }
                        ?>
                    
                    <div data-item="<?php echo esc_attr($i); ?>" class="quiz_section_item quiz_section_item_final" style="<?php $quizFinalBackgroundImage = carbon_get_the_post_meta('quiz_final_background_image'); if ($quizFinalBackgroundImage) { echo 'background:url(' . esc_url($quizFinalBackgroundImage) . ');'; } ?>">
                        <div class="quiz_section_item_final_child <?php $quizFinalCheck = carbon_get_the_post_meta('quiz_final_check'); if ($quizFinalCheck) { echo 'quiz_section_item_final_child_full_width'; } ?>">
                            <div class="quiz_question_text quiz_question_text_hide">
                                Мессенджер
                            </div>
                            <div class="quiz_final_title" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>

                                <?php 
                                    $quizFinalTitle = carbon_get_the_post_meta('quiz_final_title');
                                    echo wp_kses_post($quizFinalTitle);
                                ?>
                            </div>
                            <div class="quiz_final_content" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>
                                <?php 
                                    $quizFinalContent = carbon_get_the_post_meta('quiz_final_content');
                                    echo wp_kses_post($quizFinalContent);
                                ?>
                            </div>
                            <div class="social_label_wrapper">
                                <?php 
                                    $quizFinalSocialMedias = carbon_get_the_post_meta('quiz_final_social_media');
                                    foreach ($quizFinalSocialMedias as $quizFinalSocialMedia) {

                                        ?>
                                            <label for="<?php echo esc_attr($quizFinalSocialMedia['quiz_final_social_media_item_slug']); ?>_quiz" class="social_label">
                                                <img src="<?php echo esc_url($quizFinalSocialMedia['quiz_final_social_media_item_image']); ?>" />
                                                <div class="social_label_text" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>
                                                    <?php echo esc_html($quizFinalSocialMedia['quiz_final_social_media_item_title']); ?>
                                                </div>
                                                <input type="radio" id="<?php echo esc_attr($quizFinalSocialMedia['quiz_final_social_media_item_slug']); ?>_quiz" name="social" class="quiz_radio_social" value="<?php echo esc_attr($quizFinalSocialMedia['quiz_final_social_media_item_title']); ?>" />
                                            </label>
                                        <?php   
                                    } 
                                ?>
                                
                                
                            </div>
                            <input type="text" name="quiz_name" id="quiz_name" class="required quiz_name" placeholder="<?php echo esc_html_e('Ваше Имя'); ?>" value=""/>
                            <input type="tel" name="quiz_phone" id="quiz_phone" class="required quiz_phone" placeholder="<?php echo esc_html_e('Ваш Телефон'); ?>" value=""/>
                            <div class="check_consent_to_processing">
                                <input type="checkbox" name="qu_consent_to_processing" id="qu_consent_to_processing" class="consent_to_processing" required/>
                                <label for="consent_to_processing" <?php if ($qu_main_color) { echo 'style="color:' . esc_attr($qu_main_color) . ';"'; } ?>>
                                    <?php
                                        if($options['qu_consent_to_processing']){
                                            $consent_to_processing = $options['qu_consent_to_processing'];
                                        } else {
                                            $consent_to_processing = 'Согласие на обработку ПД';
                                        }
                                        echo esc_html($consent_to_processing);
                                    ?>
                                </label>
                            </div>
                            <input type="checkbox" name="art_anticheck" id="art_anticheck" class="art_anticheck" style="display: none !important;" value="true" checked="checked"/>
    		                <input type="text" name="art_submitted" id="art_submitted" value="" style="display: none !important;"/>
                            <input type="submit" id="submit-feedback" class="button" value="<?php echo esc_html_e('Отправить'); ?>"/>
                        </div>
                    </div>
                    </div>
                    <div class="quiz_button_wrapper">
                        <div style="background-color: <?php echo esc_attr($qu_buttons_color); ?>!important;" class="quiz_button_prev">
                            <?php echo esc_html_e('Back'); ?>
                        </div>
                        <div style="background-color: <?php echo esc_attr($qu_buttons_color); ?>!important;" class="quiz_button_next">
                            <?php echo esc_html_e('Next'); ?>
                        </div>
                    </div>
                </form>
                <?php
	            return ob_get_clean();
        }
    }
    else {
        echo esc_html_e('Quiz not found');
    }
    wp_reset_postdata();
}

// Отправка сообщения
// add_action( 'wp_ajax_feedback_action', 'ajax_action_callback' );
// add_action( 'wp_ajax_nopriv_feedback_action', 'ajax_action_callback' );

add_action('wp_ajax_feedback_action', 'qu_handle_feedback_action');
add_action('wp_ajax_nopriv_feedback_action', 'qu_handle_feedback_action'); 

function qu_handle_feedback_action() {
    // Массив ошибок
    $err_message = array();

    // Определяем язык
    $locale = get_locale(); 
    if ($locale == 'ru_RU') {
        $name = 'Имя';
        $phone = 'Телефон';
        $message = 'Сообщение'; 
        $message_success = 'Сообщение отправлено. Я свяжусь с вами в ближайшее время.';
        $nonce = 'Данные отправлены с постороннего адреса';
        $spam = 'Нет твоей власти здесь!(c)';
        $errorName = 'Пожалуйста, введите Ваше имя';
        $errorPhone = 'Пожалуйста, введите действующий телефонный номер';
    } else {
        $name = 'Name';
        $phone = 'Phone';
        $message = 'Message';    
        $message_success = 'Message sent. I will contact you shortly.';
        $nonce = 'Data sent from a third party address';
        $spam = 'Your power is not here! (c)';
        $errorName = 'Please enter your name';
        $errorPhone = 'Please enter a valid phone number';
    }

    // Проверка nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'qu_feedback-nonce')) {
        wp_die($nonce);
    }

    // Проверка на спам
    if (!isset($_POST['art_anticheck']) || false === $_POST['art_anticheck'] || !empty($_POST['art_submitted'])) {
        wp_die(esc_html($spam));
    }

    // Проверка имени
    if (empty($_POST['quiz_name']) || !isset($_POST['quiz_name'])) {
        $err_message['name'] = $errorName;
    } else {
        $art_name = sanitize_text_field($_POST['quiz_name']);
    }

    // Проверка телефона
    if (empty($_POST['quiz_phone']) || !isset($_POST['quiz_phone'])) {
        $err_message['phone'] = $errorPhone;
    } else {
        $art_phone = sanitize_text_field($_POST['quiz_phone']);
    }

    // Обработка результатов квиза
    // $quizResults = isset($_POST['quizResults']) ? json_decode(stripslashes($_POST['quizResults']), true) : [];
    if (isset($_POST['quizResults'])) {
        $quizResultsRaw = sanitize_text_field($_POST['quizResults']);
        $quizResults = json_decode(stripslashes($quizResultsRaw), true);
        if (!is_array($quizResults)) {
            $quizResults = []; // Устанавливаем значение по умолчанию, если данные некорректны
        }
    } else {
    $quizResults = [];
    }    
    $finalResultsString = '';
    foreach ($quizResults as $key => $finalResult) {
        $sanitizedKey = sanitize_text_field($key);
        $sanitizedValue = sanitize_text_field($finalResult);
        $finalResultsString .= "<div>{$sanitizedKey} - {$sanitizedValue}</div>";
    }

    // Проверка массива ошибок
    if ($err_message) {
        wp_send_json_error($err_message);
    } else {
        // Формирование и отправка письма
        $email_to = get_option('admin_email');
        $body = "Имя: $art_name\nТелефон: $art_phone\nСообщение: $finalResultsString";
        $headers = ['From: Imedia Quiz <info@imedia161.ru>', 'content-type: text/html'];

        $options = get_option('qu_quiz_settings_options');
        $letterSubject = $options['qu_letter_subject'] ?? 'Application from the site';

        wp_mail($email_to, $letterSubject, $body, $headers);
        wp_send_json_success($message_success);
    }

    wp_die();
}


/**
 * Вывод шорт-кода в админке
 */
function qu_conclusion_quiz_shortcode( $post_type ) {
?>
    <?php 
    $postId = get_the_ID();
    $postType = get_post_type($postId);
    if ('quiz' === $postType) {
        printf('<h3>Ваш шорт-код: [qu_quizimedia id="%s"]</h3>', esc_attr($postId));
    }
        
    ?>
<?php
}
add_action( 'edit_form_after_title', 'qu_conclusion_quiz_shortcode' );

             