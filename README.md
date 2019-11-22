# com.greenleafadvancement.donorsearch

Prospect research is essential for identifying high-impact donors within an
organization’s current donor pool and in the larger community.  [Donor
Search](https://www.donorsearch.net/) provides these insights for your
fundraisers and development teams to learn more about your donors’ personal
backgrounds, giving histories, wealth indicators, and philanthropic motivations,
to help you evaluate any prospect’s capacity (ability to give) and affinity
(warmth) toward your  organization and its goals.

Use this extension to perform wealth lookups on your CiviCRM contacts using the
DonorSearch prospect research service. Sites using this extension will receive
two free lookups per month. Learn more at
[https://www.donorsearch.net/](https://www.donorsearch.net/).

## Installation
Install per usual procedure for any CiviCRM extension:

* Download the extension package from
[https://github.com/greenleafadvancement/com.greenleafadvancement.donorsearch]
(https://github.com/greenleafadvancement/com.greenleafadvancement.donorsearch)
* Unzip / untar the package and place it in your configured extensions
directory.
* Use the Reload button on the Manage Extensions page to refresh the list of
available extensions. "DonorSearch" should now be listed; click its _Install_
link.

## Configuration
A valid DonorSearch API key is required. After installing this extension,
navigate to  _Administer_ > _System Settings_ > _Register DonorSearch API key_
and follow instructions on that page to obtain an API key.
Next, go to Permissions in Drupal and enable the `Access DonorSearch` permission. Save that updated permission.

## Usage
### Creating a linked profile
Navigate to _Contacts_ > _New Donor Search_ to perform a DonorSearch lookup for
any Individual contact. After performing this lookup, you'll be redirected to
that contact's "DonorSearch" tab, which will contain the latest DonorSearch data
for that lookup.

### Working with linked profile data
Once a contact has a linked DonorSearch lookup, that data will be visible under
the contact's "DonorSearch" tab. Also under this tab are two buttons:

* "View DonorSearch Profile": Click this to open the full DonorSearch page for
the linked profile (requires a login to the DonorSearch site). The URL for this
page is also shown in the field labeled "Profile" under the "DonorSearch" tab.
(Note, if this field is empty, it means no DonorSearch profile was created, most
likely because the search did not return any results; in this case, the button
will not be able to link to a DonorSearch profile.)
* "Update DonorSearch Profile": Click this button to retrieve the latest field
values from the linked DonorSearch profile.

### Reporting, searching, and analysis
Fields under the "DonorSearch" tab are available in Advance Search under _Custom
Fields_ > _DonorSearch_. They're also available as display columns and filter
criteria in reports that include custom data for contacts, e.g., the _Contact
Summary_ report.
