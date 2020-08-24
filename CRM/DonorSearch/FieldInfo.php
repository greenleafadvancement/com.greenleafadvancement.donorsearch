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
 * One place to store frequently used DonorSearch variables.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 * $Id$
 *
 */
class CRM_DonorSearch_FieldInfo {

  /**
   * Return array of DonorSearch fields where key is the response key and value contains attributes of corresponding custom field
   * This array is used for:
   *   1. creating the CiviCRM custom fields, and
   *   2. mapping DonorSearch data to CiviCRM custom fields.
   *
   * @return array
   */
  public static function getAttributes() {
    // Define an srray of DonorSearch fields related to CiviCRM custom fields. Format is like so:
    //   [DS API field key] => [array of custom field parameters, as returned in CustomField.getSingle]
    return array(
      'DS_Rating' => array(
        'name' => 'ds_rating',
        'label' => ts('DS Rating', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 5,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 1,
        'help_pre' => 'pre help',
        'help_post' => 'post help',
      ),
      'newQS' => array(
        'name' => 'overall',
        'label' => ts('Overall', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 5,
        'data_type' => 'Float',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 2,
      ),
      'LargestGiftLow' => array(
        'name' => 'largest_gift_low',
        'label' => ts('Largest gift found lower range', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 4,
      ),
      'LargestGiftHigh' => array(
        'name' => 'largest_gift_high',
        'label' => ts('Largest gift found higher range', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 3,
      ),
      'CapacityRangeBasedOnWealth' => array(
        'name' => 'capacity_range',
        'label' => ts('Wealth capacity range', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 35,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 5,
      ),
      'RealEstateEst' => array(
        'name' => 'real_estate_est',
        'label' => ts('Real estate estimate', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 8,
      ),
      'ProfileLink' => array(
        'name' => 'profile_link',
        'label' => ts('Profile', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'data_type' => 'Link',
        'html_type' => 'Link',
        'weight' => 6,
      ),
      'submit_time' => array(
        'name' => 'submit_time',
        'label' => ts('Submit time', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'date_format' => 'yy-mm-dd',
        'time_format' => 2,
        'is_search_range' => 1,
        'weight' => 7,
      ),
      'RealEstateCount' => array(
        'name' => 'real_estate_count',
        'label' => ts('Real estate count', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 9,
      ),
      'LikelyMatchesCount' => array(
        'name' => 'likely_matches_count',
        'label' => ts('Count of likely matches', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 11,
      ),
      'LikelyMatchesTotal' => array(
        'name' => 'real_estate_total',
        'label' => ts('Total of likely matches', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 10,
      ),
      'FndBoard' => array(
        'name' => 'fnd_board',
        'label' => ts('Foundation board', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 13,
      ),
      'GSBoard' => array(
        'name' => 'gs_board',
        'label' => ts('Grant seeking board', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 14,
      ),
      'PoliticalLikelyCount' => array(
        'name' => 'political_likely_count',
        'label' => ts('Political count', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 20,
      ),
      'PoliticalLikelyTotal' => array(
        'name' => 'political_likely_total',
        'label' => ts('Political total', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 15,
      ),
      'BusinessRevenues' => array(
        'name' => 'business_revenues',
        'label' => ts('Business revenues', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 16,
      ),
      'SECStockValue' => array(
        'name' => 'sec_stock_value',
        'label' => ts('SEC stock value', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 20,
        'data_type' => 'Money',
        'html_type' => 'Text',
        'is_search_range' => 1,
        'weight' => 17,
      ),
      'SECInsider' => array(
        'name' => 'sec_stock_insider',
        'label' => ts('SEC stock or insider', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 18,
      ),
      'IRS990PF' => array(
        'name' => 'IRS990PF',
        'label' => ts('IRS 990PF', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 19,
      ),
      'RealEstateTrust' => array(
        'name' => 'real_est_trust',
        'label' => ts('Real estate trust', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 35,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 21,
      ),
      'BusinessAffiliation' => array(
        'name' => 'business_affiliation',
        'label' => ts('Business affiliation', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 22,
      ),
      'Pilot' => array(
        'name' => 'pilot',
        'label' => ts('FAA Pilot license', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 23,
      ),
      'AirplaneOwner' => array(
        'name' => 'airplane_owner',
        'label' => ts('Airplane Owner', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 24,
      ),
      'Boat' => array(
        'name' => 'boat',
        'label' => ts('Boat', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 25,
      ),
      'RFMTotal' => array(
        'name' => 'rfm',
        'label' => ts('RFM (Recency, Frequency, Money)', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 3,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 26,
      ),
      'Assessed' => array(
        'name' => 'assessed',
        'label' => ts('Assessed', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 12,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 27,
      ),
      'MajorGiftLikelihood' => array(
        'name' => 'major_gift_likelihood',
        'label' => ts('Major Gift Likelihood', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 3,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 28,
      ),
      'AnnualFundLikelihood' => array(
        'name' => 'annual_fund_likelihood',
        'label' => ts('Annual Fund Likelihood', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 3,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 29,
      ),
      'PGID_Rating' => array(
        'name' => 'pgid',
        'label' => ts('PGID (Planned Giving Identification)', array('domain' => 'com.greenleafadvancement.donorsearch')),
        'text_length' => 1,
        'data_type' => 'String',
        'html_type' => 'Text',
        'weight' => 30,
      ),
    );
  }

  public static function getResponseToCustomFieldNameMap($key = NULL) {
    $responseToCustomFieldMap = Civi::cache('long')->get('donorsearch_custom_field_map_response');
    if (!empty($responseToCustomFieldMap)) {
      $responseToCustomFieldMap = array();
      foreach (self::getAttributes() as $key => $fieldInfo) {
        $customFieldID = civicrm_api3('custom_field', 'getvalue', array(
          'name' => $fieldInfo['name'],
          'return' => 'id',
        ));
        $responseToCustomFieldMap[$key] = 'custom_' . $customFieldID;
      }
      $key = NULL;
      Civi::cache('long')->set('donorsearch_custom_field_map_response', $responseToCustomFieldMap);
    }
    return CRM_Utils_Array::value($key, $responseToCustomFieldMap, $responseToCustomFieldMap);
  }

  public static function getBasicSearchFields() {
    return array(
      'clientID' => 'id',
      'firstName' => 'first_name',
      'middleName' => 'middle_name',
      'lastName' => 'last_name',
      'homeStreetAddress' => 'street_address',
      'homeCity' => 'city',
      'homeZip' => 'postal_code',
      'homeState' => 'state_province',
      'Employer' => 'current_employer',
      'spouseFirst' => 'spouse.first_name',
      'spouseMiddle' => 'spouse.middle_name',
      'spouseLast' => 'spouse.last_name',
    );
  }

}
