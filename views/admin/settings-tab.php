<h3 class="frm_first_h3" style="margin-top: 25px;">
    <?= __('Privacy', 'gdpr'); ?>
    <span
            class="frm_help frm_icon_font frm_tooltip_icon"
            title="This is my tooltip text."
    ></span>
</h3>

<table class="form-table">
    <tr>
        <td width="200px">
            <label for="frm_gdpr_email">
                <?= __('Primary Email address field', 'gdpr-admin'); ?>
                <span
                    class="frm_help frm_icon_font frm_tooltip_icon"
                    title="<?= __("Select the field which will contain the customer's primary email address. This is used to identify which form submission belongs to which customer.", 'gdpr-admin'); ?>"
                ></span>
            </label>
        </td>
        <td>
            <select id="frm_gdpr_email" name="frm_gdpr[email]">
                <option value="">
                    <?= __('— Select —'); ?>
                </option>
                <?php foreach ($fields as $field): ?>
                    <option value="<?= $field->id ?>" <?= selected($options['email'], $field->id); ?>>
                        <?php echo FrmAppHelper::truncate($field->name, 40) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="200px">
            <?= __('Exclude this form from automatic data download', 'gdpr-admin'); ?>
            <span
                    class="frm_help frm_icon_font frm_tooltip_icon"
                    title="<?= __("Check this if you want this form to be excluded when a customer's data is downloaded automatically.", 'gdpr-admin'); ?>"
            ></span>
        </td>
        <td>
            <label for="frm_gdpr_exclude_from_export">
                <input type="checkbox" id="frm_gdpr_exclude_from_export" name="frm_gdpr[exclude_from_export]" value="1" <?= checked($options['exclude_from_export'], true); ?>>
                <span><?= __('Exclude', 'gdpr-admin'); ?></span>
            </label>
        </td>
    </tr>
    <tr>
        <td width="200px">
            <?= __('Exclude this form from automatic data delete', 'gdpr-admin'); ?>
            <span
                    class="frm_help frm_icon_font frm_tooltip_icon"
                    title="<?= __("Check this if you want this form to be excluded when a customer's data is deleted automatically.", 'gdpr-admin'); ?>"
            ></span>
        </td>
        <td>
            <label for="frm_gdpr_exclude_from_delete">
                <input type="checkbox" id="frm_gdpr_exclude_from_delete" name="frm_gdpr[exclude_from_delete]" value="1" <?= checked($options['exclude_from_delete'], true); ?>>
                <span><?= __('Exclude', 'gdpr-admin'); ?></span>
            </label>
        </td>
    </tr>
</table>