<?php

require_once 'eoyremind.civix.php';
use CRM_Eoyremind_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eoyremind_civicrm_config(&$config) {
  _eoyremind_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eoyremind_civicrm_xmlMenu(&$files) {
  _eoyremind_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eoyremind_civicrm_install() {
  _eoyremind_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function eoyremind_civicrm_postInstall() {
  _eoyremind_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eoyremind_civicrm_uninstall() {
  _eoyremind_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eoyremind_civicrm_enable() {
  _eoyremind_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eoyremind_civicrm_disable() {
  _eoyremind_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eoyremind_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eoyremind_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eoyremind_civicrm_managed(&$entities) {
  _eoyremind_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eoyremind_civicrm_caseTypes(&$caseTypes) {
  _eoyremind_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function eoyremind_civicrm_angularModules(&$angularModules) {
  _eoyremind_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eoyremind_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eoyremind_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function eoyremind_civicrm_entityTypes(&$entityTypes) {
  _eoyremind_civix_civicrm_entityTypes($entityTypes);
}

function eoyremind_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op == 'create' && $objectName == 'Contribution') {
    $start = Civi::settings()->get('eoyremind_start').' 00:00:00';
    $end = Civi::settings()->get('eoyremind_end').' 23:59:59';

    if ($objectRef->financial_type_id == 1 &&
      strtotime($objectRef->receive_date) >= strtotime($start) &&
      strtotime($objectRef->receive_date) <= strtotime($end) &&
      $objectRef->contribution_status_id == 1
    ) {
      //Civi::log()->debug('eoyremind_civicrm_post', ['$objectRef' => $objectRef]);

      //search for a completed remind me later activity within the date range
      try {
        $activities = civicrm_api3('activity', 'get', [
          'source_contact_id' => $objectRef->contact_id,
          'activity_type_id' => 'Remind Me Later',
          'status_id' => 'Completed',
          'options' => [
            'limit' => 0,
          ],
        ]);
        //Civi::log()->debug('eoyremind_civicrm_post', ['$activities' => $activities]);

        foreach ($activities['values'] as $activity) {
          if (strtotime($activity['activity_date_time']) >= strtotime($start) &&
            strtotime($activity['activity_date_time']) <= strtotime($end)
          ) {
            //change status to 'Not Required'
            civicrm_api3('activity', 'create', [
              'id' => $activity['id'],
              'status_id' => 'Not Required',
            ]);
          }
        }
      }
      catch (CRM_API3_Exception $e) {
        CRM_Core_Error::debug_var('EOY Reminder unable to update activity.',
          ['$ojectRef' => $objectRef, '$activities' => $activities, '$e' => $e],
          TRUE, TRUE, 'eoyremind');
      }
    }
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function eoyremind_civicrm_navigationMenu(&$menu) {
  _eoyremind_civix_insert_navigation_menu($menu, 'Administer', array(
    'label' => E::ts('EOY Remind Workflow Settings'),
    'name' => 'eoyremind_settings',
    'url' => 'civicrm/admin/setting/eoyremind',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _eoyremind_civix_navigationMenu($menu);
}
