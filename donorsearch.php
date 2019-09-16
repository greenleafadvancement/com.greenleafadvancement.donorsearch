<?php

require_once 'donorsearch.civix.php';
require_once 'CRM/DonorSearch/FieldInfo.php';

use CRM_DonorSearch_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function donorsearch_civicrm_config(&$config) {
  _donorsearch_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function donorsearch_civicrm_xmlMenu(&$files) {
  _donorsearch_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function donorsearch_civicrm_install() {
  _donorsearch_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function donorsearch_civicrm_uninstall() {
  _donorsearch_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function donorsearch_civicrm_enable() {
  _donorsearch_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function donorsearch_civicrm_disable() {
  _donorsearch_civix_civicrm_disable();
}

/**
 * disable/enable/delete DonorSearch links
 */
function changeDSNavigation($action) {
  $names = array('ds_register_api', 'ds_view', 'ds_new');

  foreach ($names as $name) {
    if ($name == 'delete') {
      $id = civicrm_api3('Navigation', 'getvalue', array(
        'return' => "id",
        'name' => $name,
      ));
      if ($id) {
        civicrm_api3('Navigation', 'delete', array('id' => $id));
      }
    }
    else {
      $isActive = ($action == 'enable') ? 1 : 0;
      CRM_Core_BAO_Navigation::setIsActive(
        CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', $name, 'id', 'name'),
        $isActive
      );
    }
  }

  CRM_Core_BAO_Navigation::resetNavigation();
}

/**
 * Implements hook_civicrm_permission().
 */
function donorsearch_civicrm_permission(&$permissions) {
  $permissions += array('access DonorSearch' => ts('Access DonorSearch', array('domain' => 'com.greenleafadvancement.donorsearch')));
}

/**
 * @inheritDoc
 */
function donorsearch_civicrm_pageRun(&$page) {
  // Inject JS to make the Action Summary Link open in a new window.
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_Summary') {
    CRM_Core_Resources::singleton()->addScriptFile('com.greenleafadvancement.donorsearch', 'js/DonorSearchNewWindow.js');
  }
  // Inject custom buttons at the top of the DonorSearch custom fields
  if ($page->getVar('_name') == 'CRM_Contact_Page_View_CustomData') {
    $contactId = $page->getVar('_contactId');
    $groupId = $page->getVar('_groupId');
    $donorSearchGroupId = civicrm_api3('CustomGroup', 'getvalue', array(
      'return' => "id",
      'name' => "DS_details",
    ));
    if ($groupId != $donorSearchGroupId) {
      return;
    }
    $result = civicrm_api3('DonorSearch', 'get', array(
      'contact_id' => $contactId,
      'sequential' => 1,
      'options' => array('sort' => 'id DESC'),
    ));
    $count = $result['count'];
    CRM_Core_Region::instance('page-header')->add(array(
      'markup' => '
            <p>See the <a target="_blank" href="https://greenleafadvancement.github.io/com.greenleafadvancement.donorsearch">DonorSearch CiviCRM documentation</a> for details.</p>
        ',
    ));
    if ($count) {
      $currentSearch = $result['values'][0]['id'];
      CRM_Core_Region::instance('page-header')->add(array(
        'markup' => '
          <a class="no-popup button" target="_blank" href="' . CRM_Utils_System::url('civicrm/view/ds-profile', "cid=" . $contactId) . '">
            <span>' . ts('View DonorSearch Profile', array('domain' => 'com.greenleafadvancement.donorsearch')) . '</span>
          </a>
        ',
      ));
      CRM_Core_Region::instance('page-header')->add(array(
        'markup' => '
        <a class="no-popup button" href="' . CRM_Utils_System::url('civicrm/ds/update-record', array(
          'reset' => 1,
          'id' => $currentSearch)) . '">
          <span>' . ts('Update DonorSearch Profile', array('domain' => 'com.greenleafadvancement.donorsearch')) . '</span>
        </a>
        <div class="spacer"></div>
      ',
      ));
    } else {
      CRM_Core_Region::instance('page-header')->add(array(
        'markup' => '
        <a class="no-popup button" href="' . CRM_Utils_System::url('civicrm/ds/open-search', array(
          'reset' => 1,
          'cid'   => $contactId)) . '">
          <span>' . ts('New DonorSearch', array('domain' => 'com.greenleafadvancement.donorsearch')) . '</span>
        </a>
        <div class="spacer"></div>
',
      ));
    }
  }
}

/**
 * @inheritDoc
 */
function donorsearch_civicrm_tabset($link, &$allTabs, $context) {
  // hide custom group 'DonorSearch' if user doesn't have 'access DonorSearch' permission
  if (!CRM_Core_Permission::check('access DonorSearch') && $link == 'civicrm/contact/view') {
    $customGroupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_CustomGroup', 'DS_details', 'id', 'name');
    $key = array_search("custom_$customGroupID", CRM_Utils_Array::collect('id', $allTabs));
    if (!empty($allTabs)) {
      unset($allTabs[$key]);
    }
  }
}

/**
 * @inheritDoc
 */
function donorSearch_civicrm_summaryActions(&$menu, $contactId) {
  // show action link 'View DonorSearch Profile' if user have 'access DonorSearch' permission
  if (CRM_Core_Permission::check('access DonorSearch')) {
    $count = civicrm_api3('DonorSearch', 'getcount', array('contact_id' => $contactId));
    if ($count) {
      $menu += array(
        'view-ds-profile' => array(
          'title' => ts('DonorSearch Profile', array('domain' => 'com.greenleafadvancement.donorsearch')),
          'ref' => 'ds-profile',
          'key' => 'view-ds-profile',
          'href' => CRM_Utils_System::url('civicrm/view/ds-profile', 'reset=1'),
          'weight' => 100,
          'class' => 'no-popup',
          'extra' => ' target="_blank"',
          'permissions' => array('access DonorSearch'),
        ),
      );
    }
    else {
      $menu += array(
        'add-ds-profile' => array(
          'title' => ts('New DonorSearch', array('domain' => 'com.greenleafadvancement.donorsearch')),
          'ref' => 'ds-profile',
          'key' => 'add-ds-profile',
          'href' => CRM_Utils_System::url('civicrm/ds/open-search', array('reset' => 1, 'cid' => $contactId)),
          'weight' => 100,
          'class' => 'no-popup',
          'permissions' => array('access DonorSearch'),
        ),
      );
    }
  }
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function donorsearch_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _donorsearch_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function donorsearch_civicrm_managed(&$entities) {
  _donorsearch_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function donorsearch_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = array(
    'name' => 'DonorSearch',
    'class' => 'CRM_DonorSearch_DAO_SavedSearch',
    'table' => 'civicrm_ds_saved_search',
  );
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 */
function donorsearch_civicrm_alterAPIPermissions($entity, $action, $params, &$permissions) {
  $permissions['donor_search'] = array(
    'default' => array('access DonorSearch'),
  );
}

/**
 * @inheritDoc
 */
function donorsearch_civicrm_check(&$messages) {
  $apiKey = Civi::settings()->get('ds_api_key');

  if (empty($apiKey)) {
    $apiKeyUrl = CRM_Utils_System::url('civicrm/ds/register', 'reset=1');
    $messages[] = new CRM_Utils_Check_Message(
      'donorsearch_noapikey',
      ts("The DonorSearch extension is enabled, but the DonorSearch API key is missing.  Navigate to Administeer » System Settings » <a href='%1'>Register DonorSearch API Key</a> to register a key.", array(1 => $apiKeyUrl)),
      ts('No DonorSearch API Key'),
      \Psr\Log\LogLevel::WARNING,
      'fa-dollar'
    );
  }
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function donorsearch_civicrm_angularModules(&$angularModules) {
  _donorsearch_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function donorsearch_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _donorsearch_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function donorsearch_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function donorsearch_civicrm_navigationMenu(&$menu) {
  _donorsearch_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'com.greenleafadvancement.donorsearch')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _donorsearch_civix_navigationMenu($menu);
} // */
