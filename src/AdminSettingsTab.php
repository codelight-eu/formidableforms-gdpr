<?php


namespace Codelight\GDPR\Modules\FormidableForms;


class AdminSettingsTab
{
    public function __construct()
    {
        add_filter('frm_add_form_settings_section', [$this, 'addSettingsTab'], 10, 2);
        add_filter('frm_form_options_before_update', [$this, 'saveSettings'], 20, 2);
    }

    public function addSettingsTab($sections, $values)
    {
        $sections[] = [
            'name'     => __('Privacy', 'gdpr-admin'),
            'anchor'   => 'gdpr',
            'function' => [$this, 'renderSettings'],
        ];

        return $sections;
    }

    public function renderSettings($values)
    {
        $fields  = \FrmField::getAll('fi.form_id=' . (int)$values['id'] . " and fi.type in ('email')", 'field_order');

        // Handle situations where the email field type is not 'email'
        if (empty($fields)) {
            $fields = \FrmField::getAll('fi.form_id=' . (int)$values['id'], 'field_order');
        }

        $options = maybe_unserialize(get_option('frm_gdpr_' . $values['id']));

        // Populate array to avoid empty value warnings in template
        $options['email'] = isset($options['email']) ? $options['email'] : false;
        $options['exclude_from_export'] = isset($options['exclude_from_export']) ? $options['exclude_from_export'] : false;
        $options['exclude_from_delete'] = isset($options['exclude_from_delete']) ? $options['exclude_from_delete'] : false;

        echo gdpr('view')->render(
            'admin/settings-tab',
            compact('fields', 'options', 'values'),
            gdpr('config')->get('formidable.template_path')
        );
    }

    public function saveSettings($options, $values)
    {
        if (!isset($values['frm_gdpr'])) {
            return $options;
        }

        update_option('frm_gdpr_' . $values['id'], maybe_serialize($values['frm_gdpr']));

        return $options;
    }
}