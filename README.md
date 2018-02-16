# GDPR


**THIS PLUGIN IS IN BETA**


This plugin is meant to assist with GDPR.
It has a set of features that aim for data processors to be compliant and users to have easy access to its data.


## Collaboration


On our GitHub repository, you will find 2 branches
1. master => On this branch you will find the code for this plugin. Feel free to help out and send pull-requests.
1. rewrite => This branch contains a version of the plugin with additional settings, an improved UI, and additional features such as cookie preference management. This branch will soon be merged into master.
## Known issues / TODO


1. Due to server limitations, we can't send mass emails to everyone in the database when using the data breach notification. We are open to suggestions on how to best handle this.
1. The download of user data should have an additional layer of confirmation. A user should receive an email confirmation before being able to download their data.
1. Cookie preferences ( Being worked on the rewrite branch of the plugin. )


## Features


* Privacy Policy and Terms of Service update detection
* Requests Table
* Audit Log
* Right to access
* Data Breach notification
* Right to be forgotten


### Privacy Policy and Terms of Service update detection
If the data processor updates the privacy policy or terms of service page content, all users will be notified when they attempt to login and are asked to review the new policies and provide their consent again.


### Requests Table


1. Here the data processor can search for a user to be reviewed.
1. If the user has content published on the site for any post type or comments, they will be added to this table.
1. The users content can be reassigned to another user or deleted.
1. The user's comments can be anonymized.


### Audit Log


1. Everything the user does regarding their privacy preferences and consents gets encrypted and logged in the database.
1. Data breach notifications are also logged for all users.
1. Using the user's email, we can retrieve this information and display it.
1. If the user has been removed from the site, this encrypted log is deleted from the database and saved as an encrypted file inside the plugin folder.
1. If in the future this user makes a complaint, you need his email and a 6 digit token that he got with his deletion email. That will decrypt the file and display the data.


### Right to access


1. The user can place a request to download their data. They can do this on their WordPress profile page or on a custom profile page with a shortcode. [gdpr-right-to-access]
1. After requesting their data, the plugin generates an XML file and it gets downloaded.
1. If a user has lost access to their account or is requesting their information, the data processor can search for their email and download the data. The data processor should first verify that the user is indeed who they say they are.


### Data Breach notification


1. It's possible to notify everyone of a data breach.
1. The data processor needs to fill out some information for his records:
* Nature of the personal data breach
* Name and contact details of the data protection officer
* Likely consequences of the personal data breach
* Measures taken or proposed to be taken to prevent another data breach
1. A confirmation email is sent to the data processor to confirm that they, in fact, wish to notify users of the data breach.
1. After the data processor confirms they want to notify users of the data breach, the data processor gets another email with a simple list of user emails. They can then put that into an emailing service and notify the users.
1. The data breach event and information provided are logged to all users audit logs.


### Right to be forgotten


1. The user can request that their data be removed from the site
1. A confirmation by email needs to happen.
1. After confirmation, the data processor can analyze that user data. If the user has any posts or comments on the site, the data processor goes to the Requests Table to take action, otherwise, the user is removed from the site and their data deleted.


## Installation


1. Upload the plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Fill out all sections of the settings page.


## Frequently Asked Questions

#### When is the GDPR coming into effect?
GDPR will be enforced on May 25, 2018.


#### Who does the GDPR affect?


GDPR not only applies to organisations located within the EU but it will also apply to organisations located outside of the EU if they offer goods or services to, or monitor the behaviour of, EU data subjects. It applies to all companies processing and holding the personal data of data subjects residing in the European Union, regardless of the company’s location.


#### What are the penalties for non-compliance?


Organizations can be fined up to 4% of annual global turnover for breaching GDPR or €20 Million. This is the maximum fine that can be imposed for the most serious infringements e.g.not having sufficient customer consent to process data or violating the core of Privacy by Design concepts. There is a tiered approach to fines e.g. a company can be fined 2% for not having their records in order (article 28), not notifying the supervising authority and data subject about a breach or not conducting impact assessment. It is important to note that these rules apply to both controllers and processors -- meaning 'clouds' will not be exempt from GDPR enforcement.


#### Am I compliant just by activating this plugin?


No. This plugin is used to assist you on being compliant. You need to know what you need to do and what the law is.


## Changelog


##### 0.1.0
* Beta version released to the public


## Upgrade Notice


##### 0.1.0
This plugin is in beta. Use it at your own discretion.
