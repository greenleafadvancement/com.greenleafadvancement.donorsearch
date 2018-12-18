<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2016                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

use CRM_DonorSearch_ExtensionUtil as E;

/**
 * Class CRM_DonorSearch_Util
 */
class CRM_DonorSearch_Util {

  /**
   * Update the current DonorSearch data of a contact
   */
  public static function updateRecord() {
    $dao = new CRM_DonorSearch_DAO_SavedSearch();
    $self = NULL;
    $dao->id = CRM_Utils_Request::retrieve('id', 'Positive', $self, TRUE);
    $dao->find(TRUE);
    $previousDSparams = unserialize($dao->search_criteria);

    // If DonorSearch API key is missing
    if (empty($previousDSparams['key'])) {
      $apiKey = Civi::settings()->get('ds_api_key');
      if (empty($apiKey)) {
        CRM_Core_Error::fatal(E::ts("DonorSearch API key missing. Navigate to Administer >> System Settings >> Register DonorSearch API Key to register API key"));
      }
      $previousDSparams['key'] = $apiKey;
    }

    // Fetch DonorSearch data via GET api
    $apiRequest = CRM_DonorSearch_API::singleton($previousDSparams);
    list($isError, $response) = $apiRequest->get();

    // If there is no record found for given Search ID then register a new search
    // using search parameters used earlier via SEND api. This will return the
    // corrosponding DonorSearch data which is later stored against logged in contact ID
    if ($isError && (trim($response) == 'No records found')) {
      if (!empty($previousDSparams)) {
        list($isError, $response) = $apiRequest->send();
      }
    }

    // update DS data recieved from GET or SEND api above, against contact ID (as search ID)
    if (!$isError) {
      self::processDSData($response, $previousDSparams['clientID']);
      CRM_Core_Session::setStatus(E::ts("DS Record updated for Contact ID - " . $previousDSparams['clientID']), E::ts('Success'), 'success');
    }

    // redirect to 'Donor Integrated Search' page
    CRM_Utils_System::redirect(self::getDonorSearchDetailsLink($dao->contact_id));
  }

  /**
   * Process DonorSearch data, recieved from SEND or GET api
   *
   * @param array $response
   *   DonorSearch data as keyed array
   * @param int $contactID
   *   contact ID as search ID
   *
   * @return array
   */
  public static function processDSData($response, $contactID) {
    // useful to format api param by placing value against
    // corresponding custom field that represent a DS attribute
    $responseToFieldMap = CRM_DonorSearch_FieldInfo::getResponseToCustomFieldNameMap();

    $params = array('id' => $contactID);
    // set value against its desired custom field that represent a DS attribute
    foreach ($response as $key => $value) {
      // as per the documentation there are few attributes which are optional and can be ignored
      if (!array_key_exists($key, $responseToFieldMap)) {
        continue;
      }
      $params[$responseToFieldMap[$key]] = $value;
    }

    // update the contact (id - $contactID) with DonorSearch data
    civicrm_api3('Contact', 'create', $params);

    return $response;
  }

  /**
   * View the desired DonorSearch profile of a contact
   */
  public static function viewProfile() {
    $self = NULL;
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $self, TRUE);
    $profileLink = civicrm_api3('Contact', 'getvalue', array(
      'id' => $cid,
      'return' => CRM_DonorSearch_FieldInfo::getResponseToCustomFieldNameMap('ProfileLink'),
    ));

    if ($profileLink) {
      CRM_Utils_System::redirect($profileLink);
    }
    else {
      CRM_Core_Error::fatal(E::ts('There is no DonorSearch profile'));
    }
  }

  /**
   * Get DonorSearch custom group view link
   */
  public static function getDonorSearchDetailsLink($contactID) {
    $customGroupID = civicrm_api3('customGroup', 'getvalue', array(
      'name' => 'DS_details',
      'return' => 'id',
    ));
    return CRM_Utils_System::url('civicrm/contact/view', sprintf('reset=1&gid=%d&cid=%d&selectedChild=custom_%d', $customGroupID, $contactID, $customGroupID));
  }

}
