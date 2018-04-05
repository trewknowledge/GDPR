<?php
/**
 * This file is responsible for adding help sections to the plugin pages.
 *
 * @link       http://trewknowledge.com
 * @since      0.1.0
 *
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * A class that adds help tabs to the plugin pages.
 *
 * @since      1.0.0
 * @package    GDPR
 * @subpackage includes
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Help {

	/**
	 * Add the requests page help tabs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 */
	public static function add_requests_help() {
		$overview = '<h2>' . esc_html__( 'Overview', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'This page has multiple request tables. Users can request multiple things like getting deleted from the site or having their data rectified. All requests will come to these tables.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'overview',
			'title' => esc_html__( 'Overview', 'gdpr' ),
			'content' => $overview,
		) );

		$rectify_help = '<h2>' . esc_html__( 'Rectify Data', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'Users may request to have their data rectified. They can place a request somewhere on your site and those requests will show up here.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'When you complete the request, mark it as resolved and the requester will get a notification email confirming that their request was resolved.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'rectify-data',
			'title' => esc_html__( 'Rectify Data', 'gdpr' ),
			'content' => $rectify_help,
		) );

		$complaint_help = '<h2>' . esc_html__( 'Complaints', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'Users may complain about something that happened. They can place a complaint somewhere on your site and those complaints will show up here.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'When you resolve the problem, mark it as resolved and the requester will get a notification email confirming that his complaint was resolved.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'complaint',
			'title' => esc_html__( 'Complaints', 'gdpr' ),
			'content' => $complaint_help,
		) );

		$erasure_help = '<h2>' . esc_html__( 'Erasure', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'Users may request to be deleted from the site. If they don\'t have any content published on the site (including comments) they will be removed from the site automatically. Otherwise, they will show up at this review table where you can reassign or delete their published content and anonymize his comments.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'When you are ready to delete the user, they will get a notification that their account has been closed. According to GDPR, you have 30 days to fulfill this request. On some occasions, you can ask to extend this time.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'erasure',
			'title' => esc_html__( 'Erasures', 'gdpr' ),
			'content' => $erasure_help,
		) );
	}

	/**
	 * Add the tools page help tabs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 */
	public static function add_tools_help() {
		$overview = '<h2>' . esc_html__( 'Overview', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'We added tools to make your life easier when you need to perform administrative tasks like notify all your users of a possible data breach.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'overview',
			'title' => esc_html__( 'Overview', 'gdpr' ),
			'content' => $overview,
		) );

		$access_data_help = '<h2>' . esc_html__( 'Access Data', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'Use this page to look for all known data about a user. You can look it up using the user\'s email address and are able to download it in XML and JSON formats.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'access-data',
			'title' => esc_html__( 'Access Data', 'gdpr' ),
			'content' => $access_data_help,
		) );

		$data_breach_help = '<h2>' . esc_html__( 'Data Breach Notification', 'gdpr' ) . '</h2>' .
			'<p><strong>' . esc_html__( 'Use this carefully.', 'gdpr' ) . '</strong></p>' .
			'<p>' . esc_html__( 'This will send a mass email to all your users with the information provided on these fields. This email is throttled based on the hourly limit set on the plugin settings page. ', 'gdpr' ) . '</p>' .
			'<p><strong>' . esc_html__( 'Only use this tool if you believe your site has been compromised and that your user\'s personal data might have been leaked.', 'gdpr' ) . '</strong></p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'data-breach',
			'title' => esc_html__( 'Data Breach', 'gdpr' ),
			'content' => $data_breach_help,
		) );

		$audit_log_help = '<h2>' . esc_html__( 'Audit Log', 'gdpr' ) . '</h2>' .
			'<p><strong>' . esc_html__( 'We do not log any of the user\'s personal data.', 'gdpr' ) . '</strong></p>' .
			'<p>' . esc_html__( 'All logs are encrypted before saving to the database. An encrypted log file is created whenever a user gets removed from the site.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'This tool will keep a record of some actions such as changing consent preferences, placing a request, data breach notifications received, etc…', 'gdpr' ) . '<br />' .
			esc_html__( 'The only way to read the logs is to search for the user email. If the data subject is not a registered site user anymore, you need to ask for the 6 digit token that was provided during deletion. That will allow this tool to look for a log file with his information.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'audit-log',
			'title' => esc_html__( 'Audit Log', 'gdpr' ),
			'content' => $audit_log_help,
		) );
	}

	/**
	 * Add the settings page help tabs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 */
	public static function add_settings_help() {
		$general_settings_help = '<h2>' . esc_html__( 'General Settings', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'This plugin needs to know your privacy policy page to track updates to it and ask users to re-consent to your new terms.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'When sending a data breach notification to your users, we need to throttle the emails because of server limitations. This is an hourly limit. Check with your hosting provider before changing this value.', 'gdpr' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'general',
			'title' => esc_html__( 'General Settings', 'gdpr' ),
			'content' => $general_settings_help,
		) );

		$cookies_settings_help = sprintf( '<h2>' . esc_html__( 'Cookie Management', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'Fill out every information you can about the cookies your site uses. Set the cookies that you set under Cookies Used and cookies used and set by third parties under the hosts section.', 'gdpr' ) . '</p>' .
			/* translators: the function */
			'<p>' . esc_html__( 'You must ask your developer to wrap the code that sets the cookies with our helper function %s.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'Some services like Google Analytics provide a way to opt out from their code with an extra parameter to their snippet.', 'gdpr' ) . '</p>' .
			'<h3>' . esc_html__( 'External Links', 'gdpr' ) . '</h3>' .
			'<ul>' .
				'<li><a href="https://codex.wordpress.org/WordPress_Cookies" title="' . esc_attr__( 'WordPress cookies', 'gdpr' ) . '" target="_blank">'. esc_html__( 'WordPress cookies', 'gdpr' ) .'</a></li>' .
			'</ul>',
			'<code>is_allowed_cookie( $cookie_name )</code>'
		);
		get_current_screen()->add_help_tab( array(
			'id' => 'cookies',
			'title' => esc_html__( 'Cookie Management', 'gdpr' ),
			'content' => $cookies_settings_help,
		) );

		$consent_settings_help = sprintf( '<h2>' . esc_html__( 'Consent Management ( Coming Soon )', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'All consents are disabled by default. On first registration, your users will need to consent to your privacy policy. Depending on your privacy policy you should register multiple types of consent on this page and allow them to be toggled on/off.', 'gdpr' ) . '</p>' .
			/* translators: the function */
			'<p>' . esc_html__( 'If you have an optional consent type, you must have a developer wrap the functionality in our helper function %s.', 'gdpr' ) . '</p>' .
			'<p><strong>' . esc_html__( 'i.e.', 'gdpr' ) . '</strong><br />' . esc_html__( 'You registered email marketing as an optional consent but the user did not actively opt into it on their profile page. You should have your email capture form wrapped in our helper function to block registration or better yet, not even display the email capture form. Same goes for blocking adding the user to your mailing system on registration if consent is not given.', 'gdpr' ) . '</p>' .
			'<h3>' . esc_html__( 'External Links', 'gdpr' ) . '</h3>' .
			'<ul>' .
				'<li><a href="https://gdpr-info.eu/art-7-gdpr/" title="' . esc_attr__( 'Article 7 - Conditions for consent', 'gdpr' ) . '" target="_blank">'. esc_html__( 'Article 7 - Conditions for consent', 'gdpr' ) .'</a></li>' .
				'<li><a href="https://gdpr-info.eu/art-8-gdpr/" title="' . esc_attr__( "Article 8 - conditions applicable to child's consent in relation to information society services", 'gdpr' ) . '" target="_blank">'. esc_html__( "Article 8 - conditions applicable to child's consent in relation to information society services", 'gdpr' ) .'</a></li>' .
				'<li><a href="https://gdpr-info.eu/recitals/no-42/" title="' . esc_attr__( 'Recital 42 - Burden of proof and requirements for consent', 'gdpr' ) . '" target="_blank">'. esc_html__( 'Recital 42 - Burden of proof and requirements for consent', 'gdpr' ) .'</a></li>' .
				'<li><a href="https://gdpr-info.eu/recitals/no-43/" title="' . esc_attr__( 'Recital 43 - Freely Given consent', 'gdpr' ) . '" target="_blank">'. esc_html__( 'Recital 43 - Freely Given consent', 'gdpr' ) .'</a></li>' .
			'</ul>',
			'<code>have_consent( $consent_id )</code>'
		);

		get_current_screen()->add_help_tab( array(
			'id' => 'consents',
			'title' => esc_html__( 'Consent Management', 'gdpr' ),
			'content' => $consent_settings_help,
		) );
	}

	/**
	 * Add the telemetry page help tabs.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 * @static
	 */
	public static function add_telemetry_help() {
		if ( 'edit-telemetry' !== get_current_screen()->id ) {
			return;
		}

		$telemetry_help = '<h2>' . esc_html__( 'Overview', 'gdpr' ) . '</h2>' .
			'<p>' . esc_html__( 'This is all data that are being sent outside of your site. WordPress send some data to it\'s servers to be able to do automatic updates. You can reduce the amount of data being sent using filters.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'Some plugins also capture data and send it to their servers. Such practice is not allowed for plugins hosted on wordpress.org plugin repository. In case this is a Premium plugin, you should have been given the option to choose which type of data you want to send.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'Use this tool to identify plugins or themes sending potential personal data outside of your server and take action if necessary.', 'gdpr' ) . '</p>' .
			'<p>' . esc_html__( 'All information on this page is automatically deleted every 12 hours so this doesn\'t grow too large and slow your site.' ) . '</p>';
		get_current_screen()->add_help_tab( array(
			'id' => 'overview',
			'title' => esc_html__( 'Overview', 'gdpr' ),
			'content' => $telemetry_help,
		) );
	}
}
