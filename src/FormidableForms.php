<?php

namespace Codelight\GDPR\Modules\FormidableForms;

class FormidableForms
{
    public function __construct()
    {
        gdpr()->make(AdminSettingsTab::class);

        add_filter('gdpr/data-subject/data', [$this, 'getExportData'], 20, 2);
        add_action('gdpr/data-subject/delete', [$this, 'deleteEntries']);
        add_action('gdpr/data-subject/anonymize', [$this, 'deleteEntries']);
    }

    public function getExportData(array $data, $email)
    {
        $forms = $this->getValidForms($this->getForms(), 'export');

        foreach ($forms as $form) {
            $entries = $this->getEntriesByEmail($form, $email);

            if (!count($entries)) {
                continue;
            }

            $fields = $this->getFormFields($form);

            $title = __('Form submissions:', 'gdpr') . ' ' . $form->name;
            foreach ($entries as $i => $entry) {

                foreach ($fields as $field) {
                    if (isset($entry->metas[$field['id']])) {
                        $data[$title][$i][$field['label']] = $entry->metas[$field['id']];
                    }
                }

                $data[$title][$i]['date'] = $entry->created_at;
                $data[$title][$i]['ip'] = $entry->ip;

                $entryData = maybe_unserialize($entry->description);
                $data[$title][$i]['url'] = $entryData['referrer'];
                $data[$title][$i]['user_agent'] = $entryData['browser'];
            }
        }

        return $data;
    }

    public function deleteEntries($email)
    {
        $forms = $this->getValidForms($this->getForms(), 'delete');

        if (!count($forms)) {
            return;
        }

        foreach ($forms as $form) {
            $entries = $this->getEntriesByEmail($form, $email);

            if (!count($entries)) {
                return;
            }

            foreach ($entries as $entry) {
                \FrmEntry::destroy($entry->id);
            }
        }
    }

    public function getEntriesByEmail($form, $email)
    {
        $options = maybe_unserialize(get_option('frm_gdpr_' . $form->id));
        $entries = \FrmEntry::getAll(['it.form_id' => $form->id], '', 0, true, true);
        $matches = [];

        foreach ($entries as $entry) {

            if (isset($entry->metas[$options['email']]) && $email == $entry->metas[$options['email']]) {
                $matches[] = $entry;
            }
        }

        return $matches;
    }

    public function getValidForms($forms, $action)
    {
        $validForms = [];

        foreach ($forms as $form) {

            // Skip templates
            if ($form->is_template) {
                continue;
            }

            $options = maybe_unserialize(get_option('frm_gdpr_' . $form->id));

            if (!isset($options['email']) || !$options['email']) {
                continue;
            }

            if ('delete' === $action) {
                if (isset($options['exclude_from_delete']) && $options['exclude_from_delete']) {
                    continue;
                }
            } else if ('export' === $action) {
                if (isset($options['exclude_from_export']) && $options['exclude_from_export']) {
                    continue;
                }
            }

            $validForms[] = $form;
        }

        return $validForms;
    }

    public function getForms()
    {
        return \FrmForm::getAll();
    }

    public function getFormFields($form)
    {
        $fields = [];

        $formFields = \FrmField::getAll(['fi.form_id' => (int)$form->id]);

        if (!count($formFields)) {
            return $fields;
        }

        foreach ($formFields as $field) {
            $fields[] = [
                'id'    => $field->id,
                'label' => $field->name,
            ];
        }

        return $fields;
    }
}