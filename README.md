# GDPR

**THIS PLUGIN IS IN BETA**

This plugin is meant to assist with GDPR.
It has a set of features that aim to website owners to be compliant and users to have easy access to it's data.

## Collaboration

On our github repo you will find 2 branches
1. master => On this branch is where you will find the code for this plugin. Feel free to help out.
1. rewrite => It's a rewrite version of this plugin with some extra stuff. This version includes the cookie component. It is built with the same file structure. Feel free to help out there too.

Whatever is submitted to the master branch will end up being used on the rewrite. The rewrite is just a polished version of this plugin. Maybe with a nicer UI.

## Known issues / TODO

1. Due to server limitations we can't shoot an email to everyone when using the data breach notification. Open to suggestions.
1. Data download should have an additional layer of confirmation. Email confirmation as all other options.
1. When removing a consent from the settings, it doesn't revoke that particular consent for all users.
1. Cookie preferences ( Being worked on the rewrite branch of the plugin. )

## Features

*   Privacy Policy and Terms of Service update detection
*   Requests Table
*   Audit Log
*   Right to access
*   Data Breach notification
*   Right to be forgotten

### Privacy Policy and Terms of Service update detection
If the site owner updates the privacy policy or terms of service page content, all users will be notified on login and asked to review the new policies and provide their consent again.

### Requests Table

1.   Here the owner can review or add a user to be reviewed.
1.   If the user has content published on the site of any post type or comments, he will be added to this table.
1.   His content can then be reassigned to another user or deleted.
1.   His comments can also be anonymized.

### Audit Log

1.   Everything the user does regarding their privacy preferences and consents gets encrypted and logged in the database.
1.   Data breach notifications are also logged to all users.
1.   Using the user's email, we can retrieve this information and display it.
1.   If the user has been removed from the site, this encrypted log is deleted from the database and saved as an encrypted file inside the plugin folder.
1.   If in the future, this users makes a complaint, you need his email and a 6 digit token that he got with his deletion email. That will decrypt the file and display the data.

### Right to access

1.   The user can place a request to download their data. They can do this on their wp profile page or on a custom profile page with a shortcode.
1.   After requesting his data, we generate an xml file and it gets downloaded.
1.   There is also a email lookup on the backend so the site owner can get it to his user if the user lost access to his account or isn't tech savvy. ( This method should not be used without the user confirming that they are who they say ).

### Data Breach notification

1.   It's possible to notify everyone of a data breach.
1.   The owner need to fill out some information for his records.
    *   Nature of the personal data breach
    *   Name and contact details of the data protection officer
    *   Likely consequences of the personal data breach
    *   Measures taken or proposed to be taken
1.   A confirmation email is sent to the requesting user to confirm that they in fact wish to notify people of a data breach.
1.   The data breach event and information provided are logged to all users audit logs.
1.   After confirming the email, the owner gets another email with a simple list of user emails. He can then put that into a mailing service and notify his users.

### Right to be forgotten

1.   The user can request that his data be removed from the site
1.   A confirmation by email needs to happen.
1.   After confirmation, we analyze that user data. If he have any posts or comments on the site, he goes to the Requests Table, otherwise he is removed form the site and his data deleted.

## Installation

1. Upload the plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Fill out all sections of the settings page.

## Frequently Asked Questions

#### What is GDPR?

An answer to that question.

#### When is the GDPR coming into effect?

It will be in force May 2018.

#### Who does the GDPR affect?

The GDPR not only applies to organisations located within the EU but it will also apply to organisations located outside of the EU if they offer goods or services to, or monitor the behaviour of, EU data subjects. It applies to all companies processing and holding the personal data of data subjects residing in the European Union, regardless of the company’s location.

#### What are the penalties for non-compliance?

Organizations can be fined up to 4% of annual global turnover for breaching GDPR or €20 Million. This is the maximum fine that can be imposed for the most serious infringements e.g.not having sufficient customer consent to process data or violating the core of Privacy by Design concepts. There is a tiered approach to fines e.g. a company can be fined 2% for not having their records in order (article 28), not notifying the supervising authority and data subject about a breach or not conducting impact assessment. It is important to note that these rules apply to both controllers and processors -- meaning 'clouds' will not be exempt from GDPR enforcement.

#### Am I compliant just by activating this plugin?

No. This plugin is used to assist you on being compliant. You need to know what you need to do and what the law is.

## Screenshots


## Changelog

##### 0.1.0
* Beta version released to the public

## Upgrade Notice

##### 0.1.0
This plugin is in beta. Use it at your own discretion.
