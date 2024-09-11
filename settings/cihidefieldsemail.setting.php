<?php
return [
  'cihidefieldsemail' => [
    'group_name' => 'CI_HIDE_FIELDS Preferences',
    'group' => 'core',
    'name' => 'cihidefieldsemail',
    'type' => 'Array',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => [
      'class' => 'crm-select2',
      'multiple' => TRUE,
      'placeholder' => ts('Select field(s)')
    ],
    'pseudoconstant' => [
      'callback' => '_cicustom_civicrm_getSettingFields'
    ],
    'settings_pages' => ['mailing' => ['weight' => 10]],
    'default' => NULL,
    'add' => '5.65',
    'title' => ts('Field(s) to hide in receipts'),
    'is_domain' => '1',
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ]
];
