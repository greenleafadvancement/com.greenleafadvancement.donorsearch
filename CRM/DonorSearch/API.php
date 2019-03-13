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
 * Class to send DonorSearch API request
 */
class CRM_DonorSearch_API {

  /**
   * Instance of this object.
   *
   * @var CRM_DonorSearch_API
   */
  public static $_singleton = NULL;

  /**
   * Search parameters later formated into API url arguments
   *
   * @var array
   */
  protected $_searchParams;

  /**
   * Instance of CRM_Utils_HttpClient
   *
   * @var CRM_Utils_HttpClient
   */
  protected $_httpClient;

  /**
   * The constructor sets search parameters and instantiate CRM_Utils_HttpClient
   */
  public function __construct($searchParams) {
    $this->_searchParams = $searchParams;
    $this->_httpClient = new CRM_Utils_HttpClient();
  }

  /**
   * Singleton function used to manage this object.
   *
   * @param array $searchParams
   *   DonorSearch parameters
   *
   * @return CRM_DonorSearch_API
   */
  public static function &singleton($searchParams) {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_DonorSearch_API($searchParams);
    }
    return self::$_singleton;
  }

  /**
   * Function to make DonorSearch send API request
   */
  public function get() {
    $params = array(
      'Store' => 1,
      'key' => $this->_searchParams['key'],
      'clientID' => $this->_searchParams['clientID'],
    );
    return $this->sendRequest($params);
  }

  /**
   * Function to make DonorSearch send API request
   */
  public function send() {
    $params = $this->_searchParams;
    // Add 'store = 1' parameter for getting DS data in return
    // later used to update contact
    $params['Store'] = 1;
    return $this->sendRequest($params, TRUE);
  }

  /**
   * Function to make DonorSearch getKey API request
   */
  public function getKey() {
    return $this->sendRequest($this->_searchParams, FALSE, 'https://www.donorlead.net/API/getKey.php');
  }

  /**
   * Make DonorSearch API request
   *
   * @param array $params
   *    API parameters to send.
   * @param bool $doTimestamp
   *    If TRUE, add a current timestamp in a 'submit_time' array element.
   * @param string $baseUrl
   *    The URL for the desired API endpoint.
   *
   * @return array
   *    An array in the format array($isError, $result):
   *      $isError result of this::throwDSError() for the result of the API call.
   *      $result Array of relevant values from the result of the API call.
   */
  public function sendRequest(array $params, $doTimestamp = FALSE, $baseUrl = 'https://data.donorlead.net/v2') {
    // send API request with desired search arguments
    $url = $baseUrl . '/?' . http_build_query($params);
    list($status, $responseJSON) = $this->_httpClient->get($url);
    // In DS api v2, summary details are nested under the 'individual' key in
    // the result array.
    // Here we assume that only the 'individual' key is relevant for return.
    $response = CRM_Utils_Array::value('individual', json_decode($responseJSON, TRUE));

    if ($doTimestamp) {
      $response['submit_time'] = CRM_Utils_Date::currentDBDate();
    }
    return array(
      self::throwDSError($responseJSON),
      $response,
    );
  }

  /**
   * Show error/warning if there's anything wrong in $response
   *
   * @param string $responseJSON
   *   fetched data from DS API
   *
   * @return bool
   *   Found error ? TRUE or FALSE
   */
  public static function throwDSError($responseJSON) {

    $response = json_decode($responseJSON, TRUE);
    $isError = FALSE;
    if (!is_array($response)) {
      switch ($responseJSON) {
        case 'Missing required data':
        case 'API key already created':
        case 'Error':
          $errorMessage = $responseJSON;
          break;

        default:
          $errorMessage = E::ts('Unknown error');
          break;
      }
      $isError = TRUE;
    }
    elseif ($response['status'] != 200) {
      $errorMessage = $response['statusMessage'];
      $isError = TRUE;
    }

    if (!empty($errorMessage)) {
      CRM_Core_Session::setStatus(E::ts("DonorSearch API error: ") . $errorMessage, E::ts('Error'), 'error');
    }

    return $isError;
  }

}
