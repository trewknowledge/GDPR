=== GDPR ===
Contributors: fclaussen, matthewfarlymn, trewknowledge
Tags: gdpr, compliance, privacy, law, general data protection regulation
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 4.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is meant to assist with the GDPR obligations of a Data processor and Controller.

== Description ==

This plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

== Collaboration ==

You can send your pull request at [https://github.com/trewknowledge/gdpr](https://github.com/trewknowledge/gdpr)

== Shortcodes & helper functions ==

**Display one of the request forms**
`[gdpr_request_form type="$type"]`
or
`gdpr_request_form( $type );`
$type can be `delete`, `rectify`, `complaint`

**Create a button to open the preferences modals**
`gdpr_preferences( $text, $type );`
$text is the button label
$type is the preferences type. Valid options are `cookies` or `consent`

**Checks whether a cookie is allowed**
`is_allowed_cookie( $cookie )`

**Checks wheter a consent was given**
`have_consent( $consent_id )`


== Features ==

* Consent management.
* Cookie Preference management & activation toggles
* Rights to erasure & deletion of data with a confirmation email
* Re-assignment of user data on erasure request & pseudonymization of user data
* Data Processor settings and publishing of contact information
* Right to access data from admin dashboard and export
* Right to access data from front end by Data User
* Right to portability & export of data to XML or JSON by Data User
* Encrypted audit logs for the lifetime of the Data User
* Data User Secret Token for decryption and recovery of data
* Data breach notification and user segments for message obligations

== Consent Management ==

Consents can be registered on the settings page. They can be optional or not.
By default, this plugin comes with a Privacy Policy consent that users need to agree on registration.

For optional consents, there's a wrapper function `have_consent( $consent_id )` to help you display or hide something on the site depending if the user gave consent or not.

== Cookie Management ==

Similar to consent management, users can opt in and out of cookies.
We also offer a function to prevent setting cookies depending on the user setting.
The cookie with the user approved cookies can be found at another cookie named `gdpr_approved_cookies`.

There's also a helper function called `is_allowed_cookie( $cookie )` that you can use to prevent setting up a cookie.

== Telemetry Tracker ==

WordPress Core and some plugins gather data from your install and send this data to an outside server for whatever reason.
WordPress Plugin Repository does not allow plugins to do that, but premium plugins do because they are not bound by the Plugin repository rules.
If you did not explicitly opt-in for this feature you should make a complaint.

This feature will display all data that is being sent outside of your server. Indicate the plugin or theme responsible, file and line where the data is being sent.

== Privacy Policy Consent Management ==

From the Settings options in the dashboard, the Data Processor can select the Privacy Policy page for tracking.

On login, the user must consent to the Privacy Policy outlined on the site. If the user does not consent, the user will not be registered or logged in.

If the site owner updates the Privacy Policy page content, the change will be logged and flagged to the admin that they must notify users on next login to seek re-consent. Additionally, the warning message can be dismissed in the event of a minor correction or mistake.

== Requests Table & Right to Erasure ==

1. The Data User is able to submit a request to be erased from the site using a shortcode.
1. When a request is made, the Data User will receive an email confirmation to confirm the deletion request.
1.
   1. after email confirmation, the user request is added to the requests table for review by the Administrator. The Administrator can also add a user manually with an email look up and review.
   1. if the Data User has content published on the site for any post types or comments, they will be added to this table. If they do not have any content, they will receive a confirmation of erasure request and be provided a 6 digit Token for safekeeping after erasure in case of recover data needs.
1.
   1. the requests table allows the Administrator to reassign any content to another user or delete it.
   1. In the event of comments, the Data User’s content would be made anonymous.

== Audit Log ==

1.   Everything the Data User does from registration, providing consent to the privacy policy, terms of service and requests is logged and encrypted in a database.
1.   Data breach notifications are also logged to all users.
1.   Using the Data User's email, we can retrieve this information and display it.
1.   If the Data User has been removed from the site, this encrypted log is deleted from the database and saved as an encrypted file inside the plugin folder.
1.   If in the future, the Data User makes a complaint, they will need to provide their email address and the 6 digit token they received from the deletion confirmation email. With the Data User’s email and the secondary 6 digit token, you can decrypt the file and display the data.

== Right to Access Data & User Data Portability ==

1.   The Data User can place a request to download their data. They may also do this on a custom page with the shortcode.
1.   After requesting their data, the plugin will generate an XML or JSON file, which will be emailed to them and downloaded.
1.   There is also an email lookup for the Administrator to access the Data User’s information. NOTE: This method should not be used without the Data User confirming their identity.

== Data Breach & Notifications ==

1. In case of breach, the Administrator can notify the Data Users by confirming the breach and receiving an export of user data.
1. The Administrator would complete the following information which would be recorded in the audit log:
   1. Nature of the personal data breach
   1. Name and contact details of the data protection officer
   1. Likely consequences of the personal data breach
   1. Measures were taken or proposed to be taken
1. A confirmation email is then sent to the Data Processor to confirm the breach notification.
1. After email confirming, the plugin will begin notifying all users of the breach in batches every hour until all users receive the notification.

== Installation ==

1.  Upload the plugin to the `/wp-content/plugins/` directory
1.  Activate the plugin through the 'Plugins' menu in WordPress
1.  Fill out all sections of the settings page.

== Important! ==

Activating this plugin does not guarantee that an organization is successfully meeting its responsibilities and obligations of GDPR. Individual organizations should assess their unique responsibilities and ensure extra measures are taken to meet any obligations required by law and based on a data protection impact assessment (DPIA).

== Frequently Asked Questions ==

= What is GDPR? =

This Regulation lays down rules relating to the protection of natural persons with regard to the processing of personal data and rules relating to the free movement of personal data.

This Regulation protects fundamental rights and freedoms of natural persons and in particular their right to the protection of personal data.

The free movement of personal data within the Union shall be neither restricted nor prohibited for reasons connected with the protection of natural persons with regard to the processing of personal data.

= How do Businesses benefit from GDPR? =

* Build stronger customer relationships and trust
* Improve the brand image of the organization and its brand reputation
* Improve the governance and responsibility of data
* Enhance the security and commitment to the privacy of the brand
* Create value-added competitive advantages

= When is the GDPR coming into effect? =

It will be enforced on May 25th, 2018.

= Who does the GDPR affect? =

The GDPR applies to all EU organisations – whether commercial business, charity or public authority – that collect, store or process EU residents’ personal data, even if they’re not EU citizens.

The GDPR applies to all organisations located within the EU, whether you are a commercial business, charity or public authority, institution and collect, store or process EU citizen data. It also applies to any organisation located outside of the EU if they also collect store or process EU citizen data.

= What is considered personal data? =

The GDPR defines personal data as any information or type of data that can directly or indirectly identify a natural person’s identity. This can include information such as Name, Address, Email, Photos, System Data, IP addresses, Location data, Phone numbers, and Cookies.

For other special categories of personal data, there are more strict regulations for categories such as Race, Religion, Political Views, Sexual Orientation, Health Information, Biometric and Genetic data.

= What are the penalties for non-compliance? =

Organizations can be fined up to 4% of annual global turnover for breaching GDPR or €20 Million. This is the maximum fine that can be imposed for the most serious infringements.

There is a tiered approach to the fines whereby a company can be fined 2% for not having their records in order (Article 28), not notifying the supervising authority and Data User about a security breach or for investigating and assessing the breach.

= Am I compliant just by activating this plugin? =

No, this plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

Activating this plugin does not guarantee that an organisation is successfully meeting its responsibilities and obligations of GDPR. Organisations should assess their unique responsibilities and ensure extra measures are taken to meet any obligations required by law and based on a data protection impact assessment (DPIA).

== Screenshots ==

1. Audit log results example.
2. Plugin Settings.
3. Right to access result example.
4. Data breach request page.
5. Right to forget requests table.
6. Front facing buttons.
7. Forget me confirmation.
8. Data download confirmation.
9. Forget me email confirmation.
10. Data breach email confirmation.

== Changelog ==

= 1.0.0 =
* Added cookie management screen
* Added consent management screen
* Added Telemetry tracker
* Complete code rewrite
* Added more types of request
* Added Help documentation
* Added new shortcodes
* Changed to Settings API

= 0.1.1 =
* Set the admin email as the default processor information on activation
* Settings updated notice is now dismissible

= 0.1.0 =
* Beta version released to the public

== Upgrade Notice ==

= 1.0.0 =
This is a major rewrite of the plugin. Things will look different and work differently.
We tried to keep most things the same so the impact would be minimal.
This plugin is no longer in BETA.
Update with care

= 0.1.0 =
This plugin is in beta. Use it at your own discretion.
