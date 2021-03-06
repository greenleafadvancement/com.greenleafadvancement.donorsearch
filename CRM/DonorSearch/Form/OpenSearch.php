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

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_DonorSearch_Form_OpenSearch extends CRM_Core_Form {

  protected $_apiKey;

  protected $_id;

  protected $_cid;

  protected $_spouseTypes;

  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this, FALSE, NULL, 'GET');
    $this->_cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE, NULL, 'GET');
    $this->_apiKey = CRM_DonorSearch_Util::getApiKey();

    $types = civicrm_api3('RelationshipType', 'get', array(
      'name_a_b' => array('IN' => array("Spouse of", "Partner of")),
    ));
    if (!empty($types['values'])) {
      $this->_spouseTypes = array_keys($types['values']);
      $this->assign('spouseTypes', $this->_spouseTypes);
    }

    if (empty($this->_apiKey)) {
      CRM_DonorSearch_Util::promptForMissingKey();
    }

  }

  /**
   * Set default values.
   *
   * @return array
   */
  public function setDefaultValues() {
    $defaults = array();

    if ($this->_id) {
      $defaults = unserialize(CRM_Core_DAO::getFieldValue('CRM_DonorSearch_DAO_SavedSearch', $this->_id, 'search_criteria'));
    }
    elseif ($this->_cid) {
      $contact = civicrm_api3('Contact', 'get', array('id' => $this->_cid, 'sequential' => 1));
      if (!empty($contact['id'])) {
        if ($this->_spouseTypes) {
          // In looking for a spouse relationship, we have to check both a_b and b_a
          $spouse1 = civicrm_api3('Relationship', 'get', array(
            'sequential' => 1,
            'return' => array("contact_id_a.first_name", "contact_id_a.middle_name", "contact_id_a.last_name"),
            'contact_id_b' => $this->_cid,
            'relationship_type_id' => array('IN' => $this->_spouseTypes),
            'is_active' => 1,
            'end_date' => array('IS NULL' => 1),
          ));
          $spouse2 = civicrm_api3('Relationship', 'get', array(
            'sequential' => 1,
            'return' => array("contact_id_b.first_name", "contact_id_b.middle_name", "contact_id_b.last_name"),
            'contact_id_a' => $this->_cid,
            'relationship_type_id' => array('IN' => $this->_spouseTypes),
            'is_active' => 1,
            'end_date' => array('IS NULL' => 1),
          ));
          foreach (array_merge($spouse1['values'], $spouse2['values']) as $spouse) {
            unset($spouse['id']);
            foreach ($spouse as $key => $val) {
              $key = str_replace(array('contact_id_a', 'contact_id_b'), 'spouse', $key);
              $contact['values'][0][$key] = $val;
            }
            break;
          }
        }
        foreach (CRM_DonorSearch_FieldInfo::getBasicSearchFields() as $name => $field) {
          $defaults[$name] = CRM_Utils_Array::value($field, $contact['values'][0]);
        }
        // Get the Home address if one exists; primary address (which is already populated) is fine otherwise.
        $homeAddress = civicrm_api3('Address', 'get', array(
          'sequential' => 1,
          'contact_id' => $this->_cid,
          'location_type_id' => "Home",
        ));
        if ($homeAddress['count']) {
          foreach (CRM_DonorSearch_FieldInfo::getBasicSearchFields() as $name => $field) {
            // Do not assign 'id' default value from $homeAddress
            if ($name == 'clientID') {
              continue;
            }
            // Do not set default value if the field value is empty.
            if (!$value = CRM_Utils_Array::value($field, $homeAddress['values'][0])) {
              continue;
            }
            $defaults[$name] = CRM_Utils_Array::value($field, $homeAddress['values'][0]);
          }
        }
      }
    }
    return $defaults;
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    $this->addEntityRef('clientID', ts('Search for'), array('create' => TRUE, 'api' => array('params' => array('contact_type' => 'Individual'))), TRUE);
    $this->add('text', 'firstName', ts('First Name'), array(), TRUE);
    $this->add('text', 'middleName', ts('Middle Name'));
    $this->add('text', 'lastName', ts('Last Name'), array(), TRUE);
    $this->add('text', 'homeStreetAddress', ts('Address'), array('maxlength' => 75));
    $this->add('text', 'homeCity', ts('City'), array('maxlength' => 30));
    $this->add('text', 'homeZip', ts('Zip'));
    $this->add('text', 'homeState', ts('State'), array('size' => 4, 'maxlength' => 2), TRUE);
    $this->add('text', 'spouseFirst', ts('Spouse First Name'));
    $this->add('text', 'spouseMiddle', ts('Spouse Middle Name'));
    $this->add('text', 'spouseLast', ts('Spouse Last Name'));
    $this->add('text', 'Employer', ts('Employer'));

    $this->assign('donorFields', CRM_DonorSearch_FieldInfo::getBasicSearchFields());

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $values = $this->exportValues();
    // format form values
    $searchFieldValues = $this->formatFormValue($values);

    $dao = new CRM_DonorSearch_DAO_SavedSearch();
    if ($this->_id) {
      $dao->id = $this->_id;
    }
    $dao->contact_id = $values['clientID'];
    $dao->creator_id = CRM_Core_Session::getLoggedInContactID();
    $dao->search_criteria = serialize($searchFieldValues);
    $dao->save();

    // execute DS send API with provided search parameters
    list($isError, $response) = CRM_DonorSearch_API::singleton($searchFieldValues)->send();

    // populate the custom fields with desired DS data and redirect to DS profile
    CRM_DonorSearch_Util::processDSData($response, $searchFieldValues['clientID']);
    $url = CRM_DonorSearch_Util::getDonorSearchDetailsLink($searchFieldValues['clientID']);
    CRM_Utils_System::redirect($url);
  }

  /**
   * Format form-values
   *
   * @param array $values
   *
   * @return array
   */
  public function formatFormValue($values) {
    $searchFieldValues = array('key' => $this->_apiKey);
    foreach (CRM_DonorSearch_FieldInfo::getBasicSearchFields() as $name => $field) {
      if (!empty($values[$name])) {
        $searchFieldValues[$name] = $values[$name];
      }
    }

    return $searchFieldValues;
  }

}
