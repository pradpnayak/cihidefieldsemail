<?php

require_once 'cihidefieldsemail.civix.php';

use CRM_Cihidefieldsemail_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function cihidefieldsemail_civicrm_config(&$config): void {
  _cihidefieldsemail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function cihidefieldsemail_civicrm_install(): void {
  _cihidefieldsemail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function cihidefieldsemail_civicrm_enable(): void {
  _cihidefieldsemail_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterMailParams
 */
function cihidefieldsemail_civicrm_alterMailParams(&$params, $context) {
  if ($context == 'messageTemplate' && !empty($params['workflow'])) {
    if ($params['workflow'] == 'event_online_receipt') {
      $tplParams = &$params['tplParams'];
      foreach ([
        'custom_pre_id' => 'customPre',
        'custom_post_id' => 'customPost',
      ] as $key => $vKey) {
        if (empty($tplParams[$key])) {
          continue;
        }

        if (is_array($tplParams[$key])) {
          foreach ($tplParams[$key] as $pfId) {
            if (empty($pfId)) {
              continue;
            }
            _cihidefieldsemail_civicrm_unsetData($tplParams[$vKey], $pfId);
          }
        }
        else {
          _cihidefieldsemail_civicrm_unsetData($tplParams[$vKey], $tplParams[$key]);
        }
      }
    }
  }
}

/**
 * Remove fields that needs hiding.
 *
 * @param array $params
 * @param int $profileId
 */
function _cihidefieldsemail_civicrm_unsetData(array &$params, int $profileId) {
  $settings = \Civi::settings()->get('cihidefieldsemail');
  if (empty($settings)) {
    return;
  }
  $fields = _cihidefieldsemail_civicrm_getProfileFields($profileId);
  foreach ($fields as $key => $label) {
    if (in_array($key, $settings)) {
      foreach ($params as &$p) {
        unset($p[$label]);
      }

    }
  }
}

/**
 * Get list of custom fields.
 *
 * @return array
 */
function _cihidefieldsemail_civicrm_getSettingFields(): array {
  $results = \Civi\Api4\CustomField::get(TRUE)
    ->addSelect('id', 'label', 'custom_group_id:label')
    ->addWhere('is_active', '=', TRUE)
    ->addWhere('custom_group_id.is_active', '=', TRUE)
    ->addOrderBy('custom_group_id:label', 'ASC')
    ->addOrderBy('label', 'ASC')
    ->execute();
  $customFields = [];
  foreach ($results as $result) {
    $customFields["custom_{$result['id']}"] = $result['custom_group_id:label'] . ':' . $result['label'];
  }
  return $customFields;
}

/**
 * Get profile fields.
 *
 * @param int $profileId
 *
 * @return array
 */
function _cihidefieldsemail_civicrm_getProfileFields(int $profileId): array {
  static $fields = [];
  if (!empty($fields[$profileId])) {
    return $fields[$profileId];
  }
  $uFFields = \Civi\Api4\UFField::get(FALSE)
    ->addSelect('field_name', 'label')
    ->addWhere('is_active', '=', TRUE)
    ->addWhere('uf_group_id', '=', $profileId)
    ->execute();
  $fields[$profileId] = array_column((array) $uFFields, 'label', 'field_name');
  return $fields[$profileId];
}
