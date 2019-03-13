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
class CRM_DonorSearch_Form_Register extends CRM_Core_Form {

  /**
   * DonorSearch API key
   *
   * @var string
   */
  protected $_apiKey;

  /**
   * Set variables up before form is built.
   */
  public function preProcess() {
    if (!CRM_Core_Permission::check('administer CiviCRM')) {
      CRM_Core_Error::fatal(ts('You do not permission to access this page, please contact your system administrator.'));
    }
    $this->_apiKey = CRM_DonorSearch_Util::getApiKey();

    // Ensure we get reloaded with reset=1 after submission.
    $this->controller->_destination = CRM_Utils_System::url('civicrm/ds/register', 'reset=1');
  }

  /**
   * Set default values.
   *
   * @return array
   */
  public function setDefaultValues() {
    $defaults = array('api_key' => $this->_apiKey);
    return $defaults;
  }

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    $collapsible = empty($this->_apiKey) ? FALSE : TRUE;
    $this->assign('collapsible', $collapsible);

    $this->add('text', 'user', ts('Username'), array('class' => 'huge'));
    $this->add('password', 'pass', ts('Password'), array('class' => 'huge'));
    $this->add('password', 'api_key', ts('API Key'), array('class' => 'huge'));

    $this->addFormRule(array('CRM_DonorSearch_Form_Register', 'formRule'), $this);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  /**
   * Global form rule.
   *
   * @param array $fields
   *   The input form values.
   * @param array $files
   *   The uploaded files if any.
   * @param $self
   *
   * @return bool|array
   *   true if no errors, else array of errors
   */
  public static function formRule($fields, $files, $self) {
    $errors = array();
    if (empty($fields['api_key'])) {
      $params = array(
        'user' => 'Username',
        'pass' => 'Password',
      );
      foreach ($params as $name => $label) {
        if (empty($fields[$name])) {
          $errors[$name] = ts("Please enter %1", array(1 => $label));
        }
      }
    }

    return $errors;
  }

  /**
   * Process the form submission.
   */
  public function postProcess() {
    $values = $this->exportValues();
    $apiKey = CRM_Utils_Array::value('api_key', $values);
    if (empty($apiKey)) {
      $searchParams = array(
        'user' => $values['user'],
        'pass' => $values['pass'],
      );

      list($isError, $response) = CRM_DonorSearch_API::singleton($searchParams)->getKey();

      if ($isError) {
        return;
      }

      $apiKey = $response;
    }

    CRM_DonorSearch_Util::saveApiKey($apiKey);
    CRM_Core_Session::setStatus(ts("DonorSearch API key registered"), ts('Success'), 'success');
  }

}
