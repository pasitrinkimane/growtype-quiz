<?php

defined('ABSPATH') || exit;

class Growtype_Quiz_Admin_Result_Edit
{
    const NONCE = 'growtype_quiz_edit_result';

    /**
     * Render the edit form for a single result.
     */
    public static function render(int $item_id): void
    {
        global $wpdb;
        $table = Growtype_Quiz_Result_Crud::table_name();
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $item_id), ARRAY_A);

        if (!$item) {
            echo '<div class="wrap"><div class="notice notice-error"><p>' . __('Result not found.', 'growtype-quiz') . '</p></div></div>';
            return;
        }

        $errors = self::handle_save($item_id, $item);

        // Refresh after save
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $item_id), ARRAY_A);
        }

        $answers_json = self::pretty_json($item['answers'] ?? '');
        $extra_json   = self::pretty_json($item['extra_details'] ?? '');
        $back_url     = admin_url('edit.php?post_type=' . Growtype_Quiz::get_growtype_quiz_post_type() . '&page=' . Growtype_Quiz_Admin_Result::PAGE_NAME);

        foreach ($errors as $err) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($err) . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h2><?php printf(__('Edit Result #%d', 'growtype-quiz'), $item_id); ?>
                <a href="<?php echo esc_url($back_url); ?>" class="page-title-action"><?php _e('Back to results', 'growtype-quiz'); ?></a>
            </h2>

            <form method="post" style="max-width:900px;">
                <?php wp_nonce_field(self::NONCE); ?>
                <input type="hidden" name="growtype_quiz_edit_result" value="1">

                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php _e('ID', 'growtype-quiz'); ?></label></th>
                        <td><?php echo esc_html($item['id']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('User ID', 'growtype-quiz'); ?></label></th>
                        <td><input type="number" name="user_id" value="<?php echo esc_attr($item['user_id'] ?? ''); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Quiz ID', 'growtype-quiz'); ?></label></th>
                        <td><?php echo esc_html($item['quiz_id']); ?> (<?php echo esc_html(get_the_title($item['quiz_id'])); ?>)</td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Unique Hash', 'growtype-quiz'); ?></label></th>
                        <td><code><?php echo esc_html($item['unique_hash'] ?? '—'); ?></code></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Duration', 'growtype-quiz'); ?></label></th>
                        <td><input type="text" name="duration" value="<?php echo esc_attr($item['duration'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Questions Amount', 'growtype-quiz'); ?></label></th>
                        <td><?php echo esc_html($item['questions_amount'] ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Correct Answers', 'growtype-quiz'); ?></label></th>
                        <td><input type="number" name="correct_answers_amount" value="<?php echo esc_attr($item['correct_answers_amount'] ?? '0'); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Wrong Answers', 'growtype-quiz'); ?></label></th>
                        <td><input type="number" name="wrong_answers_amount" value="<?php echo esc_attr($item['wrong_answers_amount'] ?? '0'); ?>" class="small-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Evaluated', 'growtype-quiz'); ?></label></th>
                        <td>
                            <select name="evaluated">
                                <option value="0" <?php selected($item['evaluated'] ?? 0, 0); ?>><?php _e('No', 'growtype-quiz'); ?></option>
                                <option value="1" <?php selected($item['evaluated'] ?? 0, 1); ?>><?php _e('Yes', 'growtype-quiz'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Created At', 'growtype-quiz'); ?></label></th>
                        <td><?php echo esc_html($item['created_at'] ?? '—'); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php _e('Updated At', 'growtype-quiz'); ?></label></th>
                        <td><?php echo esc_html($item['updated_at'] ?? '—'); ?></td>
                    </tr>
                </table>

                <h3><?php _e('Answers (JSON)', 'growtype-quiz'); ?></h3>
                <textarea name="answers" rows="16" class="large-text code" style="font-family:monospace;"><?php echo esc_textarea($answers_json); ?></textarea>

                <h3><?php _e('Extra Details (JSON)', 'growtype-quiz'); ?></h3>
                <textarea name="extra_details" rows="12" class="large-text code" style="font-family:monospace;"><?php echo esc_textarea($extra_json); ?></textarea>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Changes', 'growtype-quiz'); ?></button>
                    <a href="<?php echo esc_url($back_url); ?>" class="button"><?php _e('Cancel', 'growtype-quiz'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Process save and return any errors.
     *
     * @return string[] Error messages.
     */
    private static function handle_save(int $item_id, array $item): array
    {
        if (!isset($_POST['growtype_quiz_edit_result']) || !wp_verify_nonce($_POST['_wpnonce'], self::NONCE)) {
            return [];
        }

        $errors = [];
        $fields = [];

        $fields['answers']       = self::validate_json_field('answers', __('Answers contains invalid JSON.', 'growtype-quiz'), $errors);
        $fields['extra_details'] = self::validate_json_field('extra_details', __('Extra Details contains invalid JSON.', 'growtype-quiz'), $errors);

        if (isset($_POST['evaluated'])) {
            $fields['evaluated'] = absint($_POST['evaluated']);
        }
        if (isset($_POST['correct_answers_amount'])) {
            $fields['correct_answers_amount'] = absint($_POST['correct_answers_amount']);
        }
        if (isset($_POST['wrong_answers_amount'])) {
            $fields['wrong_answers_amount'] = absint($_POST['wrong_answers_amount']);
        }
        if (isset($_POST['duration'])) {
            $fields['duration'] = sanitize_text_field($_POST['duration']);
        }
        if (isset($_POST['user_id']) && $_POST['user_id'] !== '') {
            $fields['user_id'] = absint($_POST['user_id']);
        }

        // Remove fields that haven't changed
        $fields = array_filter($fields, function ($val, $key) use ($item) {
            return !array_key_exists($key, $item) || (string) $item[$key] !== (string) $val;
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($fields)) {
            return [];
        }

        if (empty($errors)) {
            $updated = Growtype_Quiz_Result_Crud::update_quiz_single_result($item_id, $fields);
            if ($updated === false) {
                $errors[] = __('Database update failed.', 'growtype-quiz');
            } else {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Result updated.', 'growtype-quiz') . '</p></div>';
            }
        }

        return $errors;
    }

    /**
     * Validate a JSON textarea field and return the sanitized value.
     */
    private static function validate_json_field(string $field, string $error_msg, array &$errors): ?string
    {
        if (!isset($_POST[$field])) {
            return null;
        }

        $raw = trim(wp_unslash($_POST[$field]));
        if ($raw !== '') {
            json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = $error_msg;
            }
        }

        return $raw;
    }

    /**
     * Pretty-print a JSON string for display.
     */
    private static function pretty_json(string $raw): string
    {
        if ($raw === '') {
            return '';
        }

        $decoded = json_decode($raw, true);
        return $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $raw;
    }
}
