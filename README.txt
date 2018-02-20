=== GDPR ===
Contributors: fclaussen, trewknowledge
Tags: gdpr, compliance, privacy, law, general data protection regulation
Requires at least: 4.0
Requires PHP: 5.3
Tested up to: 4.9
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is meant to assist with the GDPR obligations of a Data processor and Controller.
This plugin is in BETA. It is not finished and might contain bugs.

== Description ==

**THIS PLUGIN IS IN BETA**

This plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

ACTIVATING THIS PLUGIN DOES NOT GUARANTEE THAT AN ORGANIZATION IS SUCCESSFULLY MEETING ITS RESPONSIBILITIES AND OBLIGATIONS OF GDPR. INDIVIDUAL ORGANIZATIONS SHOULD ASSESS THEIR UNIQUE RESPONSIBILITIES AND ENSURE EXTRA MEASURES ARE TAKEN TO MEET ANY OBLIGATIONS REQUIRED BY LAW AND BASED ON A DATA PROTECTION IMPACT ASSESSMENT (DPIA).

== Collaboration ==

This GitHub repository has two branches:

1. master => This branch is where you will find the code for this plugin. Feel free to help out.
1. rewrite => This is a rewrite version of this plugin with some additional features such as cookie preference management and control. Please feel free to help out here too.

When anything is submitted to the master branch it will ultimately end up being used on the rewrite version. The rewrite is considered to be the polished version of this plugin. The goal would also be to address any UI elements that can be enhanced.


== Known issues / TODO ==

1. Due to server limitations and potential for outgoing emails to be flagged as spam, we cannot send emails to everyone when using the data breach notification. Open to suggestions on best to handle mass emails and any controls required.
1. Cookie preferences ( Being worked on the rewrite branch of the plugin. )
1. Cookie preferences and activation control is currently being developed and will be released shortly in the rewrite branch. This will allow for a custom banner message on the front end UI, cookie settings window with activation controls for various types of cookies being used and user acceptance/saving settings. Administrator will be able to create little or many cookie controls based on obligations.
1. The right to file a complaint with a supervisory authority is being developed and will be added shortly, however, open to suggestions on the best approach.
1. The right to rectify an error in the data or request a correction is also being worked on to send a notification to Administrator contact info.
1. Data residence and the storage of user data is another important concern that we are looking for a solution.
1. Additional, more granular consent management for the Data User to restrict personal data from processing or revoke consent form profile.
1. Consent management for conditions applicable to child’s consent
1. Scanning for personal data being collected by 3rd party WordPress plugins and notification if processing is transmitted out of a set country.

And more to come...

== Features ==

* Terms of Service & Privacy Policy registration consent management
* Rights to erasure & deletion of data with confirmation email
* Re-assignment of user data on erasure request & pseudonymization of user data
* Data Processor settings and publishing of contact information
* Right to access data from admin dashboard and export
* Right to access data from front end by Data User
* Right to portability & export of data to XML by Data User
* Encrypted audit logs for the lifetime of the Data User
* Data User Secret Token for decryption and recovery of data
* Data breach notification and user segments for message obligations
* Cookie Preference management & activation toggles (Released in rewrite ~ Feb 23, 2018)

== Terms of Service & Privacy Policy consent management ==

From the Settings options in the dashboard, the Data Processor can select the pages that will be tracked and logged for Terms of Service and Privacy Policy, then include their content.

On login, the user must consent to the Terms of Service and the Privacy Policy outlined on the site. If the user does not consent, the user will not be registered or logged in.

If the site owner updates the Privacy Policy or Terms of Service page content, the change will be logged and flagged to the admin that they must notify users on next login to seek re-consent. Additionally, the warning message can be dismissed in the event of a minor correction or mistake.

== Requests Table & Right to Erasure ==

1. The Data User is able to submit a request to be erased from the profile using a shortcode.
1. When a request is made, the Data User will receive an email confirmation to confirm the deletion request.
1.
   1. after email confirmation, the user request is added to the requests table for review by the Administrator. The Administrator can also add user manually with an email look up and review.
   1. if the Data User has content published on the site for any post types, or comments, they will be added to this table. If they do not have any content, they will receive a confirmation of erasure request and be provided a 6 digit Token for safe keeping after erasure in case of recover data needs.
1.
   1. the requests table allows the Administrator to reassign any content to another user or delete it.
   1. In the event of comments, the Data User’s content would be made anonymous.

== Audit Log ==

1.   Everything the Data User does from registration, providing consent to privacy policy, terms of service and requests is logged and encrypted in a database.
1.   Data breach notifications are also logged to all users.
1.   Using the Data User's email, we can retrieve this information and display it.
1.   If the Data User has been removed from the site, this encrypted log is deleted from the database and saved as an encrypted file inside the plugin folder.
1.   If in the future, the Data User makes a complaint, they will need to provide their email address and the 6 digit token they received from the deletion confirmation email. With the Data User’s email and the secondary 6 digit token, you can decrypt the file and display the data.

== Right to Access Data & User Data Portability ==

1.   The Data User can place a request to download their data. They may also do this on their WordPress Dashboard Profile page, or on a custom profile page with the shortcode.
1.   After requesting their data, the plugin will generate an XML file, which can be downloaded.
1.   There is also an email lookup for the Administrator to access the Data User’s information. NOTE: This method should not be used without the Data User confirming their identity.

== Data Breach & Notifications ==

1. In case of breach, the Administrator can notify the Data Users by confirming the breach and receiving an export of user data.
1. The Administrator would complete the following information which would be recorded in the audit log:
   1. Nature of the personal data breach
   1. Name and contact details of the data protection officer
   1. Likely consequences of the personal data breach
   1. Measures taken or proposed to be taken
1. A confirmation email is then sent to the Data Processor to confirm the breach notification and the data breach event, including information provided, are logged to all Data Users.
1. After email confirming, the Data Processor will receive a second email with a list of Data User emails which can be imported into an email marketing platform of email provider for bulk notifications.

== Installation ==

1.  Upload the plugin to the `/wp-content/plugins/` directory
1.  Activate the plugin through the 'Plugins' menu in WordPress
1.  Fill out all sections of the settings page.

== Frequently Asked Questions ==

= What is GDPR? =

This Regulation lays down rules relating to the protection of natural persons with regard to the processing of personal data and rules relating to the free movement of personal data.

This Regulation protects fundamental rights and freedoms of natural persons and in particular their right to the protection of personal data.

The free movement of personal data within the Union shall be neither restricted nor prohibited for reasons connected with the protection of natural persons with regard to the processing of personal data.

= How do Businesses benefit from GDPR? =

* Build stronger customer relationships and trust
* Improve the brand image of the organization and its brand reputation
* Improve the governance and responsibility of data
* Enhance the security and commitment to privacy of the brand
* Create value added competitive advantages

= When is the GDPR coming into effect? =

It will be in force May 25th, 2018.

= Who does the GDPR affect? =

The GDPR applies to all EU organisations – whether commercial business, charity or public authority – that collect, store or process EU residents’ personal data, even if they’re not EU citizens.

The GDPR applies to all organisations located within the EU, whether you are a commercial business, charity or public authority, institution and collect, store or process EU citizen data. It also applies to any organisation located outside of the EU if they also collect store or process EU citizen data.

= What is considered personal data? =

The GDPR defines personal data as any information or type of data that can directly or indirectly identify a natural person’s identity. This can include information such as: Name, Address, Email, Photos, System Data, IP addresses, Location data, Phone numbers and Cookies.

For other special categories of personal data, there are more strict regulations for categories such as: Race, Religion, Political Views, Sexual Orientation, Health Information, Biometric and Genetic data.

= What are the penalties for non-compliance? =

Organizations can be fined up to 4% of annual global turnover for breaching GDPR or €20 Million. This is the maximum fine that can be imposed for the most serious infringements.

There is a tiered approach to the fines whereby a company can be fined 2% for not having their records in order (Article 28), not notifying the supervising authority and Data User about a security breach or for investigating and assessing the breach.

= Am I compliant just by activating this plugin? =

NO! This plugin is meant to assist a Controller, Data Processor, and Data Protection Officer (DPO) with efforts to meet the obligations and rights enacted under the GDPR.

ACTIVATING THIS PLUGIN DOES NOT GUARANTEE THAT AN ORGANIZATION IS SUCCESSFULLY MEETING ITS RESPONSIBILITIES AND OBLIGATIONS OF GDPR. INDIVIDUAL ORGANIZATIONS SHOULD ASSESS THEIR UNIQUE RESPONSIBILITIES AND ENSURE EXTRA MEASURES ARE TAKE TO MEET ANY OBLIGATIONS REQUIRED BY LAW AND BASED ON A DATA PROTECTION IMPACT ASSESSMENT (DPIA).

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

= 0.1.1 =
* Set the admin email as the default processor information on activation
* Settings updated notice is now dismissible

= 0.1.0 =
* Beta version released to the public

== Upgrade Notice ==

= 0.1.0 =
This plugin is in beta. Use it at your own discretion.
