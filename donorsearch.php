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
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function donorsearch_civicrm_install() {
  _donorsearch_civix_civicrm_install();
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
 * disable/enable/delete DonorSearch links
 */
function changeDSNavigation($action) {
  $names = ['ds_register_api', 'ds_view', 'ds_new'];

  foreach ($names as $name) {
    if ($name == 'delete') {
      $result = civicrm_api3('Navigation', 'get', [
        'return' => "id",
        'name' => $name,
      ]);
      if (!empty($result['id'])) {
        civicrm_api3('Navigation', 'delete', ['id' => $result['id']]);
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
  $permissions['access DonorSearch'] = [
    'label' => E::ts('Access DonorSearch')
  ];
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
    $donorSearchGroupId = civicrm_api3('CustomGroup', 'getvalue', [
      'return' => "id",
      'name' => "DS_details",
    ]);
    if ($groupId != $donorSearchGroupId) {
      return;
    }
    $result = civicrm_api3('DonorSearch', 'get', [
      'contact_id' => $contactId,
      'sequential' => 1,
      'options' => ['sort' => 'id DESC'],
    ]);
    $count = $result['count'];
    CRM_Core_Region::instance('page-header')->add([
      'markup' => '
            <p>See the <a target="_blank" href="https://greenleafadvancement.github.io/com.greenleafadvancement.donorsearch">DonorSearch CiviCRM documentation</a> for details.</p>
        ',
    ]);
    if ($count) {
      $currentSearch = $result['values'][0]['id'];
      CRM_Core_Region::instance('page-header')->add([
        'markup' => '
          <a class="no-popup button" target="_blank" href="' . CRM_Utils_System::url('civicrm/view/ds-profile', "cid=" . $contactId) . '">
            <span>' . ts('View DonorSearch Profile', ['domain' => 'com.greenleafadvancement.donorsearch']) . '</span>
          </a>
        ',
      ]);
      CRM_Core_Region::instance('page-header')->add([
        'markup' => '
        <a class="no-popup button" href="' . CRM_Utils_System::url('civicrm/ds/update-record', [
          'reset' => 1,
          'id' => $currentSearch]) . '">
          <span>' . ts('Update DonorSearch Profile', ['domain' => 'com.greenleafadvancement.donorsearch']) . '</span>
        </a>
        <div class="spacer"></div>
      ',
      ]);
    } else {
      CRM_Core_Region::instance('page-header')->add([
        'markup' => '
        <a class="no-popup button" href="' . CRM_Utils_System::url('civicrm/ds/open-search', [
          'reset' => 1,
          'cid'   => $contactId]) . '">
          <span>' . ts('New DonorSearch', ['domain' => 'com.greenleafadvancement.donorsearch']) . '</span>
        </a>
        <div class="spacer"></div>
',
      ]);
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
    $count = civicrm_api3('DonorSearch', 'getcount', ['contact_id' => $contactId]);
    if ($count) {
      $menu += [
        'view-ds-profile' => [
          'title' => ts('DonorSearch Profile', ['domain' => 'com.greenleafadvancement.donorsearch']),
          'ref' => 'ds-profile',
          'key' => 'view-ds-profile',
          'href' => CRM_Utils_System::url('civicrm/view/ds-profile', 'reset=1'),
          'weight' => 100,
          'class' => 'no-popup',
          'extra' => ' target="_blank"',
          'permissions' => ['access DonorSearch'],
        ],
      ];
    }
    else {
      $menu += [
        'add-ds-profile' => [
          'title' => ts('New DonorSearch', ['domain' => 'com.greenleafadvancement.donorsearch']),
          'ref' => 'ds-profile',
          'key' => 'add-ds-profile',
          'href' => CRM_Utils_System::url('civicrm/ds/open-search', ['reset' => 1, 'cid' => $contactId]),
          'weight' => 100,
          'class' => 'no-popup',
          'permissions' => ['access DonorSearch'],
        ],
      ];
    }
  }
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function donorsearch_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = [
    'name' => 'DonorSearch',
    'class' => 'CRM_DonorSearch_DAO_SavedSearch',
    'table' => 'civicrm_ds_saved_search',
  ];
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 */
function donorsearch_civicrm_alterAPIPermissions($entity, $action, $params, &$permissions) {
  $permissions['donor_search'] = [
    'label' => E::ts('Access DonorSearch')
  ];
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
      ts("The DonorSearch extension is enabled, but the DonorSearch API key is missing.  Navigate to Administeer » System Settings » <a href='%1'>Register DonorSearch API Key</a> to register a key.", [1 => $apiKeyUrl]),
      ts('No DonorSearch API Key'),
      \Psr\Log\LogLevel::WARNING,
      'fa-dollar'
    );
  }
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

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
