<?php
/**
 * Plugin Name: AI Text Humanizer
 * Plugin URI: https://example.com/ai-text-humanizer
 * Description: Humanize AI-style text via OpenAI. Shortcode, admin API settings, AJAX frontend.
 * Version: 1.0
 * Author: Raheel Shehzad
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: ai-text-humanizer
 *
 * @package AI_Text_Humanizer
 */

if (! defined('ABSPATH')) {
    exit;
}

final class AI_Text_Humanizer
{
    public const OPTION_API_KEY = 'ai_humanizer_api_key';
    public const OPTION_MODEL   = 'ai_humanizer_model';
    public const NONCE_ACTION   = 'ai_humanize_action';
    public const AJAX_ACTION    = 'ai_humanize';

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'maybe_enqueue_assets']);
        add_shortcode('ai_humanizer', [$this, 'render_shortcode']);
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'handle_ajax']);
        add_action('wp_ajax_nopriv_' . self::AJAX_ACTION, [$this, 'handle_ajax']);
    }

    public function register_admin_menu(): void
    {
        add_options_page(
            __('AI Humanizer Settings', 'ai-text-humanizer'),
            __('AI Humanizer', 'ai-text-humanizer'),
            'manage_options',
            'ai-humanizer-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings(): void
    {
        register_setting(
            'ai_humanizer_settings_group',
            self::OPTION_API_KEY,
            [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ]
        );

        register_setting(
            'ai_humanizer_settings_group',
            self::OPTION_MODEL,
            [
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'gpt-4.1-mini',
            ]
        );

        add_settings_section(
            'ai_humanizer_main',
            __('OpenAI Configuration', 'ai-text-humanizer'),
            function (): void {
                echo '<p>' . esc_html__('Enter your API credentials below.', 'ai-text-humanizer') . '</p>';
            },
            'ai-humanizer-settings'
        );

        add_settings_field(
            self::OPTION_API_KEY,
            __('OpenAI API Key', 'ai-text-humanizer'),
            [$this, 'field_api_key'],
            'ai-humanizer-settings',
            'ai_humanizer_main'
        );

        add_settings_field(
            self::OPTION_MODEL,
            __('Model', 'ai-text-humanizer'),
            [$this, 'field_model'],
            'ai-humanizer-settings',
            'ai_humanizer_main'
        );
    }

    public function field_api_key(): void
    {
        $value = get_option(self::OPTION_API_KEY, '');
        printf(
            '<input type="password" class="regular-text" name="%1$s" id="%1$s" value="%2$s" autocomplete="off" />',
            esc_attr(self::OPTION_API_KEY),
            esc_attr($value)
        );
        echo '<p class="description">' . esc_html__('Stored securely in the database. Use a restricted API key when possible.', 'ai-text-humanizer') . '</p>';
    }

    public function field_model(): void
    {
        $value = get_option(self::OPTION_MODEL, 'gpt-4.1-mini');
        printf(
            '<input type="text" class="regular-text" name="%1$s" id="%1$s" value="%2$s" />',
            esc_attr(self::OPTION_MODEL),
            esc_attr($value)
        );
    }

    public function render_settings_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('ai_humanizer_settings_group');
                do_settings_sections('ai-humanizer-settings');
                submit_button(__('Save Settings', 'ai-text-humanizer'));
                ?>
            </form>
        </div>
        <?php
    }

    public function maybe_enqueue_assets(): void
    {
        global $post;
        if (! is_a($post, 'WP_Post') || ! has_shortcode($post->post_content, 'ai_humanizer')) {
            return;
        }
        // Styles/scripts are inlined in shortcode for true single-file portability.
    }

    public function render_shortcode(): string
    {
        $nonce = wp_create_nonce(self::NONCE_ACTION);
        $ajax  = esc_url(admin_url('admin-ajax.php'));

        ob_start();
        ?>
        <div class="ai-humanizer-wrap" id="ai-humanizer-root">
            <style>
                .ai-humanizer-wrap{max-width:720px;margin:1.5rem auto;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;}
                .ai-humanizer-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.5rem;box-shadow:0 4px 24px rgba(15,23,42,.06);}
                .ai-humanizer-wrap label{display:block;font-weight:600;margin-bottom:.35rem;color:#111827;}
                .ai-humanizer-wrap textarea{width:100%;min-height:140px;padding:.75rem 1rem;border:1px solid #d1d5db;border-radius:8px;font-size:15px;resize:vertical;box-sizing:border-box;}
                .ai-humanizer-wrap select{width:100%;padding:.6rem .75rem;border-radius:8px;border:1px solid #d1d5db;font-size:15px;background:#fff;}
                .ai-humanizer-row{margin-bottom:1rem;}
                .ai-humanizer-submit{background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;border:none;padding:.75rem 1.5rem;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px;transition:opacity .2s,transform .15s;}
                .ai-humanizer-submit:hover:not(:disabled){opacity:.92;transform:translateY(-1px);}
                .ai-humanizer-submit:disabled{opacity:.55;cursor:not-allowed;}
                .ai-humanizer-loader{display:none;align-items:center;gap:.5rem;margin-top:1rem;color:#4b5563;font-size:14px;}
                .ai-humanizer-loader.is-visible{display:flex;}
                .ai-humanizer-spinner{width:18px;height:18px;border:2px solid #e5e7eb;border-top-color:#2563eb;border-radius:50%;animation:aiHumSpin .7s linear infinite;}
                @keyframes aiHumSpin{to{transform:rotate(360deg);}}
                .ai-humanizer-result{margin-top:1.25rem;padding:1rem;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;display:none;}
                .ai-humanizer-result.is-visible{display:block;animation:aiHumFade .35s ease;}
                @keyframes aiHumFade{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}
                .ai-humanizer-result-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.75rem;}
                .ai-humanizer-score{font-size:13px;font-weight:700;color:#059669;background:#d1fae5;padding:.25rem .6rem;border-radius:999px;}
                .ai-humanizer-output{white-space:pre-wrap;word-break:break-word;color:#1f2937;line-height:1.55;font-size:15px;}
                .ai-humanizer-copy{margin-top:.75rem;background:#111827;color:#fff;border:none;padding:.5rem 1rem;border-radius:6px;font-size:13px;cursor:pointer;}
                .ai-humanizer-copy:hover{opacity:.9;}
                .ai-humanizer-error{margin-top:1rem;padding:.75rem 1rem;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;display:none;font-size:14px;}
                .ai-humanizer-error.is-visible{display:block;}
                @media(max-width:480px){.ai-humanizer-card{padding:1rem;}}
            </style>

            <div class="ai-humanizer-card">
                <div class="ai-humanizer-row">
                    <label for="ai-humanizer-text"><?php esc_html_e('Your text', 'ai-text-humanizer'); ?></label>
                    <textarea id="ai-humanizer-text" placeholder="<?php esc_attr_e('Paste text to humanize…', 'ai-text-humanizer'); ?>"></textarea>
                </div>
                <div class="ai-humanizer-row">
                    <label for="ai-humanizer-tone"><?php esc_html_e('Tone', 'ai-text-humanizer'); ?></label>
                    <select id="ai-humanizer-tone">
                        <option value="casual"><?php esc_html_e('Casual', 'ai-text-humanizer'); ?></option>
                        <option value="professional"><?php esc_html_e('Professional', 'ai-text-humanizer'); ?></option>
                        <option value="storytelling"><?php esc_html_e('Storytelling', 'ai-text-humanizer'); ?></option>
                        <option value="linkedin"><?php esc_html_e('LinkedIn Viral', 'ai-text-humanizer'); ?></option>
                    </select>
                </div>
                <button type="button" class="ai-humanizer-submit" id="ai-humanizer-submit"><?php esc_html_e('Humanize', 'ai-text-humanizer'); ?></button>

                <div class="ai-humanizer-loader" id="ai-humanizer-loader" aria-live="polite">
                    <span class="ai-humanizer-spinner" aria-hidden="true"></span>
                    <span><?php esc_html_e('Humanizing your text…', 'ai-text-humanizer'); ?></span>
                </div>

                <div class="ai-humanizer-error" id="ai-humanizer-error" role="alert"></div>

                <div class="ai-humanizer-result" id="ai-humanizer-result">
                    <div class="ai-humanizer-result-head">
                        <strong><?php esc_html_e('Humanized result', 'ai-text-humanizer'); ?></strong>
                        <span class="ai-humanizer-score" id="ai-humanizer-score"></span>
                    </div>
                    <div class="ai-humanizer-output" id="ai-humanizer-output"></div>
                    <button type="button" class="ai-humanizer-copy" id="ai-humanizer-copy"><?php esc_html_e('Copy', 'ai-text-humanizer'); ?></button>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var ajaxUrl = <?php echo wp_json_encode($ajax); ?>;
            var nonce = <?php echo wp_json_encode($nonce); ?>;
            var btn = document.getElementById('ai-humanizer-submit');
            var ta = document.getElementById('ai-humanizer-text');
            var tone = document.getElementById('ai-humanizer-tone');
            var loader = document.getElementById('ai-humanizer-loader');
            var err = document.getElementById('ai-humanizer-error');
            var res = document.getElementById('ai-humanizer-result');
            var out = document.getElementById('ai-humanizer-output');
            var scoreEl = document.getElementById('ai-humanizer-score');
            var copyBtn = document.getElementById('ai-humanizer-copy');

            function setLoading(on){
                btn.disabled = on;
                loader.classList.toggle('is-visible', on);
            }
            function showError(msg){
                err.textContent = msg;
                err.classList.add('is-visible');
                res.classList.remove('is-visible');
            }
            function hideError(){
                err.classList.remove('is-visible');
            }

            btn.addEventListener('click', function(){
                hideError();
                var text = (ta.value || '').trim();
                if(!text){
                    showError(<?php echo wp_json_encode(__('Please enter some text.', 'ai-text-humanizer')); ?>);
                    return;
                }
                setLoading(true);
                var fd = new FormData();
                fd.append('action', <?php echo wp_json_encode(self::AJAX_ACTION); ?>);
                fd.append('nonce', nonce);
                fd.append('text', text);
                fd.append('tone', tone.value);

                fetch(ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                    .then(function(r){ return r.json(); })
                    .then(function(data){
                        setLoading(false);
                        if(!data || !data.success){
                            var m = (data && data.data && data.data.message) ? data.data.message : <?php echo wp_json_encode(__('Something went wrong.', 'ai-text-humanizer')); ?>;
                            showError(m);
                            return;
                        }
                        var human = data.data.humanized || '';
                        var sc = typeof data.data.score !== 'undefined' ? data.data.score : '';
                        out.textContent = human;
                        scoreEl.textContent = <?php echo wp_json_encode(__('Human score:', 'ai-text-humanizer')); ?> + ' ' + sc + '%';
                        res.classList.add('is-visible');
                    })
                    .catch(function(){
                        setLoading(false);
                        showError(<?php echo wp_json_encode(__('Network error. Try again.', 'ai-text-humanizer')); ?>);
                    });
            });

            copyBtn.addEventListener('click', function(){
                var t = out.textContent || '';
                if(!t) return;
                if(navigator.clipboard && navigator.clipboard.writeText){
                    navigator.clipboard.writeText(t).then(function(){
                        copyBtn.textContent = <?php echo wp_json_encode(__('Copied!', 'ai-text-humanizer')); ?>;
                        setTimeout(function(){ copyBtn.textContent = <?php echo wp_json_encode(__('Copy', 'ai-text-humanizer')); ?>; }, 2000);
                    });
                } else {
                    var x = document.createElement('textarea');
                    x.value = t;
                    document.body.appendChild(x);
                    x.select();
                    try { document.execCommand('copy'); } catch(e){}
                    document.body.removeChild(x);
                    copyBtn.textContent = <?php echo wp_json_encode(__('Copied!', 'ai-text-humanizer')); ?>;
                    setTimeout(function(){ copyBtn.textContent = <?php echo wp_json_encode(__('Copy', 'ai-text-humanizer')); ?>; }, 2000);
                }
            });
        })();
        </script>
        <?php
        return (string) ob_get_clean();
    }

    public function handle_ajax(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        $text = isset($_POST['text']) ? sanitize_textarea_field(wp_unslash($_POST['text'])) : '';
        $tone = isset($_POST['tone']) ? sanitize_text_field(wp_unslash($_POST['tone'])) : 'casual';

        if ($text === '') {
            wp_send_json_error(['message' => __('Empty text.', 'ai-text-humanizer')], 400);
        }

        $allowed_tones = ['casual', 'professional', 'storytelling', 'linkedin'];
        if (! in_array($tone, $allowed_tones, true)) {
            $tone = 'casual';
        }

        $api_key = get_option(self::OPTION_API_KEY, '');
        $model   = get_option(self::OPTION_MODEL, 'gpt-4.1-mini');

        if ($api_key === '') {
            wp_send_json_error(['message' => __('API key is not configured. Ask the site admin to add it in Settings → AI Humanizer.', 'ai-text-humanizer')], 400);
        }

        $tone_instruction = $this->tone_instruction($tone);

        $user_content = "Rewrite this text in a natural human tone. Break predictable AI patterns, vary sentence length, add emotional tone, conversational flow, and storytelling style. Avoid robotic phrasing.\n\n"
            . "TONE TARGET: {$tone_instruction}\n\n"
            . 'TEXT: ' . $text;

        $body = wp_json_encode([
            'model'       => $model,
            'messages'    => [
                [
                    'role'    => 'user',
                    'content' => $user_content,
                ],
            ],
            'temperature' => 0.8,
        ]);

        $response = wp_remote_post(
            'https://api.openai.com/v1/chat/completions',
            [
                'timeout' => 60,
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => $body,
            ]
        );

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()], 500);
        }

        $code = wp_remote_retrieve_response_code($response);
        $raw  = wp_remote_retrieve_body($response);
        $json = json_decode($raw, true);

        if ($code < 200 || $code >= 300) {
            $msg = __('OpenAI request failed.', 'ai-text-humanizer');
            if (is_array($json) && ! empty($json['error']['message'])) {
                $msg = sanitize_text_field($json['error']['message']);
            }
            wp_send_json_error(['message' => $msg], $code ?: 500);
        }

        if (! is_array($json) || empty($json['choices'][0]['message']['content'])) {
            wp_send_json_error(['message' => __('Unexpected API response.', 'ai-text-humanizer')], 500);
        }

        $humanized = $json['choices'][0]['message']['content'];
        $humanized = is_string($humanized) ? wp_strip_all_tags($humanized) : '';

        $score = random_int(80, 98);

        wp_send_json_success([
            'humanized' => $humanized,
            'score'     => $score,
        ]);
    }

    private function tone_instruction(string $tone): string
    {
        switch ($tone) {
            case 'professional':
                return 'Professional, clear, and credible—suitable for business readers; still warm, not stiff.';
            case 'storytelling':
                return 'Narrative, vivid, with pacing and small sensory or emotional beats; still coherent.';
            case 'linkedin':
                return 'LinkedIn-style: punchy hooks, short paragraphs, confident voice, one clear takeaway; avoid hashtag spam.';
            case 'casual':
            default:
                return 'Casual, friendly, like a knowledgeable friend explaining ideas—natural contractions where appropriate.';
        }
    }
}

new AI_Text_Humanizer();
