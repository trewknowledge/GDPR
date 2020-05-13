# GDPR

[![license](https://img.shields.io/github/license/trewknowledge/gdpr.svg?style=flat)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
![Tested up to](https://img.shields.io/wordpress/v/gdpr.svg)
![Minimum PHP version](https://img.shields.io/badge/PHP-%5E5.6-blue.svg)
[![WordPress plugin](https://img.shields.io/wordpress/plugin/dt/gdpr.svg)](https://wordpress.org/plugins/gdpr/)
[![WordPress plugin rating](https://img.shields.io/wordpress/plugin/r/gdpr.svg)](https://wordpress.org/support/plugin/gdpr/reviews/#new-post)
![Translations](https://img.shields.io/badge/dynamic/json.svg?label=Translations&url=https%3A%2F%2Fapi.wordpress.org%2Ftranslations%2Fplugins%2F1.0%2F%3Fslug%3Dgdpr&query=%24.translations.length&colorB=%23ff69b4)



This plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

## Documentation
[https://github.com/trewknowledge/GDPR/wiki/](https://github.com/trewknowledge/GDPR/wiki/)

## Installation
1.  Upload the plugin to the `/wp-content/plugins/` directory or install directly from the [https://wordpress.org/plugins/gdpr](WordPress Plugin Repository).
1.  Activate the plugin through the 'Plugins' menu in WordPress
1.  Fill out all sections of the settings page.

## Shortcodes & helper functions
[https://github.com/trewknowledge/GDPR/wiki/Functions-&-Shortcodes](https://github.com/trewknowledge/GDPR/wiki/Functions-&-Shortcodes)

## Features

* Consent management
* Privacy Preference management for Cookies with front-end preference UI & banner notifications
* Privacy Policy page configurations with version control and re-consent management
* Rights to erasure & deletion of website data with a double opt-in confirmation email
* Re-assignment of user data on erasure requests & pseudonymization of user website data
* Data Processor settings and publishing of contact information
* Right to access data by admin dashboard with email look up and export
* Right to access data by Data Subject with front-end requests button & double opt-in confirmation email
* Right to portability & export of data by Admin or Data Subject in XML or JSON formats
* Encrypted audit logs for the lifetime of Data Subject compliance activity
* Data Subject Secret Token for two-factor decryption and recovery of data
* Data breach notification logs and batch email notifications to Data Subjects
* Telemetry Tracker for visualizing plugins and website data

## Settings
**General**
From the Settings options in the dashboard, you can select the Privacy Policy page for tracking and logging consent.

On login, the user must consent to the Privacy Policy outlined on the site. If the user does not consent, the user will not be registered or logged in.

If the site owner updates the Privacy Policy page content, the change will be logged and flagged to the admin that they must notify users on next login to seek re-consent. Additionally, the warning message can be dismissed in the event of a minor correction or mistake.

Additionally, under General Settings the Admin can set the outgoing email limitation which would set the batch notification email limit per hour in the event of a Breach Notification.

**Cookie Preference Management**
Similar to consent management, users can opt in or out of cookies that are being used on the site. There are 3 formats of cookies that can be created which include:

* **Always Active:** Cookies that are always active or are required for the site to function.
* **Toggled:** Cookies that can be activated or blocked based on the user preference
* **Opt-Out Link:** Cookies that require configuration from a third-party source in order to opt-out

Depending on the user preference setting, you can use the `is_allowed_cookie( $cookie )` function to save and set the cookies. The cookie with the user approved cookies can be found at another cookie named `gdpr_approved_cookies`. There's also a helper function called `is_allowed_cookie( $cookie )` that you can use to prevent setting up a cookie.

**Consent Management**

Consents can be registered on the settings page. They can be optional or not. By default, this plugin comes with a Privacy Policy consent that users need to agree with on registration.

For optional consents, there's a wrapper function `have_consent( $consent_id )` to help you display or hide something on the site depending if the user gave consent or not.

Consents are logged to the user record for auditing or for access purposes.

## Requests Table & Rights of Data Subject
**Right to Erasure Requests**

1. The Data Subject is able to submit a request to be erased from the site using a shortcode.
1. When a request is made, the Data Subject will receive an email confirmation to confirm the deletion request.

   1. After email confirmation, the user request is added to the requests table for review by the Administrator. The Administrator can also add a user manually with an email look up and review.
   1. If the Data Subject has content published on the site for any post types or comments, they will be added to this table. If they do not have any content, they will receive a confirmation of erasure request and be provided a 6 digit Token for safekeeping after erasure in case of recover data needs.
   1. The requests table allows the Administrator to reassign any content to another user or delete it.
   1. In the event of comments, the Data Subject’s content would be made anonymous.

1. Admin can also manually add users to the erasure requests table with a manual email search

**Right to Access Data Request & User Data Portability**

1.   The Data Subject can place a request to download their data with the shortcode.
1.   After requesting their data, the user will receive a double opt-in confirmation email then the plugin will generate an XML or JSON file, which will be emailed to them for download with an expiration time of 48 hours.

**Right to Rectify & Complaint Requests**

1.   The Data Subject can place a request to rectify data or file a complaint with the shortcode.
1.   After making their request, the user will receive a double opt-in confirmation email and then add them to the table for admin to handle the request.

## Tools
**Access Data**

The Access Data tool allows the Admin to look up a user email and view the data of a particular user. The Admin can download and export the data in a JSON or XML format and provide to the Data Subject if manually requested.

NOTE: This method should not be used without the Data Subject confirming their identity.

**Audit Log**

Everything the Data Subject does from registration, providing consent to the privacy policy, terms of service and other requests are logged and encrypted in a database. Data breach notifications are also logged to all Data Subjects upon confirmation by Controller.

1.   Using the Data Subject's email, you can look up and retrieve the user information and display it.
1.   If the Data Subject has been removed from the site, this encrypted log is deleted from the database and saved as an encrypted file inside the plugin folder.

If in the future, the Data Subject makes a complaint or there is a need to recover the data, the user can provide their email address and the 6 digit token they received from the deletion confirmation email to decrypt and retrieve the file.

**Data Breach & Notifications**

In case of a data breach, the Admin can generate a Data Breach Notification to users by logging the information and confirm the breach through a double opt-in confirmation email. The following information would be recorded in the audit log:

   1. Nature of the personal data breach
   1. Name and contact details of the data protection officer
   1. Likely consequences of the personal data breach
   1. Measures were taken or proposed to be taken

Once the confirmation of the breach has been confirmed via email, the website will begin a batch email notification process to all users every hour until all users receive the notification.

## Telemetry Tracker
The Telemetry Tracker feature will display all data that is being sent outside of your server to another destination. It will indicate the plugin or theme responsible, file and line where the data is being sent.

WordPress Core and some plugins gather data from your install and send this data to an outside server.

WordPress Plugin Repository does not allow plugins to do that, but premium plugins are able to do this because they are not bound by the Plugin repository rules. If you did not explicitly opt-in for this feature you should make a complaint.

# Important!

Activating this plugin does not guarantee that an organization is successfully meeting its responsibilities and obligations of GDPR. Individual organizations should assess their unique responsibilities and ensure extra measures are taken to meet any obligations required by law and based on a data protection impact assessment (DPIA).


## Frequently Asked Questions

#### What is GDPR
This Regulation lays down rules relating to the protection of natural persons with regard to the processing of personal data and rules relating to the free movement of personal data.

This Regulation protects fundamental rights and freedoms of natural persons and in particular their right to the protection of personal data.

The free movement of personal data within the Union shall be neither restricted nor prohibited for reasons connected with the protection of natural persons with regard to the processing of personal data.

#### How do Businesses benefit from GDPR?
* Build stronger customer relationships and trust
* Improve the brand image of the organization and its brand reputation
* Improve the governance and responsibility of data
* Enhance the security and commitment to the privacy of the brand
* Create value-added competitive advantages

#### Who does the GDPR affect?
The GDPR applies to all EU organisations – whether commercial business, charity or public authority – that collect, store or process EU residents’ personal data, even if they’re not EU citizens.

The GDPR applies to all organisations located within the EU, whether you are a commercial business, charity or public authority, institution and collect, store or process EU citizen data. It also applies to any organisation located outside of the EU if they also collect store or process EU citizen data.

#### What is considered personal data?
The GDPR defines personal data as any information or type of data that can directly or indirectly identify a natural person’s identity. This can include information such as Name, Address, Email, Photos, System Data, IP addresses, Location data, Phone numbers, and Cookies.

For other special categories of personal data, there are more strict regulations for categories such as Race, Religion, Political Views, Sexual Orientation, Health Information, Biometric and Genetic data.

#### What are the penalties for non-compliance?
Organizations can be fined up to 4% of annual global turnover for breaching GDPR or €20 Million. This is the maximum fine that can be imposed for the most serious infringements.

There is a tiered approach to the fines whereby a company can be fined 2% for not having their records in order (Article 28), not notifying the supervising authority and Data Subject about a security breach or for investigating and assessing the breach.

#### Am I compliant just by activating this plugin?
No, this plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

Activating this plugin does not guarantee that an organisation is successfully meeting its responsibilities and obligations of GDPR. Organisations should assess their unique responsibilities and ensure extra measures are taken to meet any obligations required by law and based on a data protection impact assessment (DPIA).
