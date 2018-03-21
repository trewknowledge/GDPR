<?php
/**
 * The telemetry post type registration file.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 */

/**
 * The telemetry post type registration file.
 *
 * Defines the custom post type and edit the look and feel of the page.
 *
 * @package    GDPR
 * @subpackage GDPR/admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Telemetry {

	protected static $options;

	public function __construct( $plugin_name, $version ) {

	}

	public function register_post_type() {
		register_post_type(
			'telemetry',
			array(
				'label' => 'Telemetry',
				'labels' => array(
					'not_found' => esc_html__( 'No items found. Future connections will be shown at this place.', 'gdpr' ),
					'not_found_in_trash' => esc_html__( 'No items found in trash.', 'gdpr' ),
					'search_items' => esc_html__( 'Search in destination', 'gdpr' ),
				),
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'show_in_nav_menus' => false,
				'query_var' => true, // try setting to false
				'hierarchical' => false,
				'capability_type' => 'post',
				'publicly_queryable' => false,
				'exclude_from_search' => true
			)
		);
	}

	public function log_request( $response, $type, $class, $args, $url ) {
		/* Only response type */
		if ( 'response' !== $type ) {
			return false;
		}

		/* Empty url */
		if ( empty( $url ) ) {
			return false;
		}

		/* Validate host */
		$host = parse_url( $url, PHP_URL_HOST );

		if ( ! $host ) {
			return false;
		}

		/* Backtrace data */
		$backtrace = self::_debug_backtrace();

		/* No reference file found */
		if ( empty( $backtrace['file'] ) ) {
			return false;
		}

		/* Show your face, file */
		$meta = self::_face_detect( $backtrace['file'] );

		/* Extract backtrace data */
		$file = str_replace( ABSPATH, '', $backtrace['file'] );
		$line = ( int ) $backtrace['line'];

		/* Response code */
		$code = ( is_wp_error( $response ) ? -1 : wp_remote_retrieve_response_code( $response ) );

		$postdata = self::_get_postdata( $args );

		if ( ! $postdata ) {
			return false;
		}

		/* Insert CPT */
		$this->insert_post( array(
			'url'      => esc_url_raw($url),
			'code'     => $code,
			'host'     => $host,
			'file'     => $file,
			'line'     => $line,
			'meta'     => $meta,
			'postdata' => $postdata,
		) );
	}

	private function insert_post( $meta ) {
		/* Empty? */
		if ( empty( $meta ) ) {
			return;
		}

		/* Create post */
		$post_id = wp_insert_post(
			array(
				'post_status' => 'publish',
				'post_type'   => 'telemetry'
			)
		);

		/* Add meta values */
		foreach( $meta as $key => $value ) {
			add_post_meta( $post_id, '_gdpr_telemetry_' .$key, $value, true );
		}

		return $post_id;
	}

	public static function actions_above_table( $post_type ) {
		if ( 'telemetry' !== $post_type ) {
			return;
		}

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action'    => 'delete_all',
					'post_type' => 'telemetry',
					'post_status' => 'publish'
				),
				admin_url('edit.php')
			),
			'bulk-posts'
		);
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="button"><?php echo esc_html__('Delete all', 'gdpr'); ?></a>
		<?php
	}

	public function manage_columns( $columns ) {
		return array(
			'url'      => esc_html__( 'Destination', 'gdpr' ),
			'file'     => esc_html__( 'File', 'gdpr' ),
			'code'     => esc_html__( 'Code', 'gdpr' ),
			'created'  => esc_html__( 'Time', 'gdpr' ),
			'postdata' => esc_html__( 'Data', 'gdpr')
		);
	}

	public static function custom_column( $column, $post_id ) {
		/* Column types */
		$types = array(
			'url'      => array( __CLASS__, '_html_url' ),
			'file'     => array( __CLASS__, '_html_file' ),
			'code'     => array( __CLASS__, '_html_code' ),
			'created'  => array( __CLASS__, '_html_created' ),
			'postdata' => array( __CLASS__, '_html_postdata' )
		);

		/* If type exists */
		if ( ! empty( $types[ $column ] ) ) {
			/* Callback */
			$callback = $types[ $column ];

			/* Execute */
			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $post_id );
			}
		}
	}

	private static function _html_url( $post_id ) {
		/* Init data */
		$url = self::_get_post_meta( $post_id, 'url' );
		$host = self::_get_post_meta( $post_id, 'host' );

		/* Print output */
		echo sprintf(
			'<div>%s</div>',
			str_replace( $host, '<code>' .$host. '</code>', esc_url( $url ) )
		);
	}

	private static function _html_file( $post_id ) {
		$file = self::_get_post_meta( $post_id, 'file' );
		$line = self::_get_post_meta( $post_id, 'line' );
		$meta = self::_get_post_meta( $post_id, 'meta' );

		/* Print output */
		echo sprintf(
			'<div>%s: %s<br /><code>/%s:%d</code></div>',
			$meta['type'],
			$meta['name'],
			$file,
			$line
		);
	}

	private static function _html_code( $post_id ) {
		echo self::_get_post_meta( $post_id, 'code' );
	}

	private static function _html_created( $post_id ) {
		echo sprintf(
			esc_html__( '%s ago' ),
			human_time_diff( get_post_time( 'G', true, $post_id ) )
		);
	}

	private static function _html_postdata( $post_id ) {
		/* Item post data */
		$postdata = self::_get_post_meta( $post_id, 'postdata' );

		/* Empty data? */
		if ( empty( $postdata ) ) {
			return;
		}

		/* Parse POST data */
		if ( ! is_array( $postdata ) ) {
			wp_parse_str( $postdata, $postdata );
		}

		/* Empty array? */
		if ( empty( $postdata ) ) {
			return;
		}

		/* Thickbox content start */
		echo sprintf(
			'<div id="gdpr-telemetry-thickbox-%d" class="gdpr-hidden"><pre>',
			$post_id
		);

		/* POST data */
		print_r( $postdata );

		/* Thickbox content end */
		echo '</pre></div>';

		/* Thickbox button */
		echo sprintf(
			'<a href="#TB_inline?width=400&height=300&inlineId=gdpr-telemetry-thickbox-%d" class="button thickbox">%s</a>',
			$post_id,
			esc_html__( 'Show', 'gdpr' )
		);
	}

	private static function _get_post_meta( $post_id, $key ) {
		if ( $value = get_post_meta( $post_id, '_gdpr_telemetry_' .$key, true ) ) {
			return $value;
		}

		return get_post_meta( $post_id, $key, true );
	}

	private static function _debug_backtrace() {
		/* Reverse items */
		$trace = array_reverse( debug_backtrace() );

		/* Loop items */
  	foreach( $trace as $index => $item ) {
  		if ( ! empty( $item['function'] ) && strpos( $item['function'], 'wp_remote_' ) !== false ) {
  			/* Use prev item */
  			if ( empty( $item['file'] ) ) {
  				$item = $trace[-- $index];
  			}

  			/* Get file and line */
  			if ( ! empty( $item['file'] ) && ! empty( $item['line'] ) ) {
  				return $item;
  			}
  		}
  	}
	}

	private static function _face_detect( $path ) {
		/* Default */
		$meta = array(
			'type' => 'WordPress',
			'name' => 'Core'
		);

		/* Empty path */
		if ( empty( $path ) ) {
			return $meta;
		}

		/* Search for plugin */
		if ( $data = self::_localize_plugin( $path ) ) {
			return array(
				'type' => 'Plugin',
				'name' => $data['Name'],
			);

		/* Search for theme */
		} else if ( $data = self::_localize_theme( $path ) ) {
			return array(
				'type' => 'Theme',
				'name' => $data->get( 'Name' ),
			);
		}

		return $meta;
	}

	private static function _localize_plugin( $path ) {
		/* Check path */
		if ( false === strpos( $path, WP_PLUGIN_DIR ) ) {
			return false;
		}

		/* Reduce path */
		$path = ltrim( str_replace( WP_PLUGIN_DIR, '', $path ), DIRECTORY_SEPARATOR );

		/* Get plugin folder */
		$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) ) . DIRECTORY_SEPARATOR;

		/* Frontend */
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH. 'wp-admin/includes/plugin.php' );
		}

		/* All active plugins */
		$plugins = get_plugins();

		/* Loop plugins */
		foreach( $plugins as $path => $plugin ) {
			if ( 0 === strpos( $path, $folder ) ) {
				return $plugin;
			}
		}
	}

	private static function _localize_theme( $path ) {
		/* Check path */
		if ( false === strpos( $path, get_theme_root() ) ) {
			return false;
		}

		/* Reduce path */
		$path = ltrim( str_replace( get_theme_root(), '', $path ), DIRECTORY_SEPARATOR );

		/* Get theme folder */
		$folder = substr( $path, 0, strpos( $path, DIRECTORY_SEPARATOR ) );

		/* Get theme */
		$theme = wp_get_theme( $folder );

		/* Check & return theme */
		if ( $theme->exists() ) {
			return $theme;
		}

		return false;
	}

	private static function _get_postdata( $args ) {
		/* No POST data? */
		if ( empty( $args['method'] ) OR 'POST' !== $args['method'] ) {
			return NULL;
		}

		/* No body data? */
		if ( empty( $args['body'] ) ) {
			return NULL;
		}

		return $args['body'];
	}
}
