<?php
/**
 * The telemetry post type registration file.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */

/**
 * The telemetry post type registration file.
 *
 * Defines the custom post type and edit the look and feel of the page.
 *
 * @package    GDPR
 * @subpackage admin
 * @author     Fernando Claussen <fernandoclaussen@gmail.com>
 */
class GDPR_Telemetry {

	/**
	 * Registers the telemetry post type.
	 * @since  1.0.0
	 * @author Fernando Claussen <fernandoclaussen@gmail.com>
	 */
	public function register_post_type() {
		$telemetry_enabled = get_option( 'gdpr_enable_telemetry_tracker', false );
		if ( ! $telemetry_enabled ) {
			wp_clear_scheduled_hook( 'telemetry_cleanup' );
			return;
		}

		if ( ! wp_next_scheduled( 'telemetry_cleanup' ) ) {
			wp_schedule_event(
				time(),
				'hourly',
				'telemetry_cleanup'
			);
		}

		register_post_type(
			'telemetry',
			array(
				'label' => esc_html__( 'Telemetry', 'gdpr' ),
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

	/**
	 * Log the call request.
	 * @param  object $response The call response.
	 * @param  [type] $type     Context under which the hook is fired.
	 * @param  [type] $class    HTTP transport used.
	 * @param  [type] $args     HTTP request arguments.
	 * @param  [type] $url      The request URL.
	 * @since  1.0.0
	 */
	public function log_request( $response, $type, $class, $args, $url ) {
		$telemetry_enabled = get_option( 'gdpr_enable_telemetry_tracker', false );
		if ( ! $telemetry_enabled ) {
			return;
		}
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

	/**
	 * Insert the telemetry post.
	 * @since  1.0.0
	 * @access private
	 * @param  array  $meta  Meta values.
	 * @return int           The post ID.
	 */
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
		foreach( (array) $meta as $key => $value ) {
			add_post_meta( $post_id, '_gdpr_telemetry_' .$key, $value, true );
		}

		return $post_id;
	}

	/**
	 * Add a Delete All button on top of the table.
	 * @param  string $post_type The post type.
	 * @static
	 * @since  1.0.0
	 */
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

	/**
	 * Adding custom columns.
	 * @since  1.0.0
	 * @param  array $columns The columns array.
	 * @return array          The new columns.
	 */
	public function manage_columns( $columns ) {
		return array(
			'url'      => esc_html__( 'Destination', 'gdpr' ),
			'file'     => esc_html__( 'File', 'gdpr' ),
			'code'     => esc_html__( 'Code', 'gdpr' ),
			'created'  => esc_html__( 'Time', 'gdpr' ),
			'postdata' => esc_html__( 'Data', 'gdpr')
		);
	}

	/**
	 * Custom columns hook.
	 * @since  1.0.0
	 * @static
	 * @param  string $column  The column ID.
	 * @param  int    $post_id The post ID.
	 */
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

	/**
	 * The URL column callback.
	 * @since  1.0.0
	 * @static
	 * @access private
	 * @param  int $post_id The post ID.
	 */
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

	/**
	 * The file column callback.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  int $post_id The post ID.
	 */
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

	/**
	 * The response code column callback.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  int $post_id The post ID.
	 */
	private static function _html_code( $post_id ) {
		echo self::_get_post_meta( $post_id, 'code' );
	}

	/**
	 * The created column callback.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  int $post_id The post ID.
	 */
	private static function _html_created( $post_id ) {
		/* translators: Amount of time  */
		echo sprintf(
			esc_html__( '%s ago' ),
			human_time_diff( get_post_time( 'G', true, $post_id ) )
		);
	}

	/**
	 * The post data column callback.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  int $post_id The post ID.
	 */
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

	/**
	 * Get the post meta we care about.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  int    $post_id The post ID.
	 * @param  string $key     The key that matters to us.
	 * @return mixed           The post meta.
	 */
	private static function _get_post_meta( $post_id, $key ) {
		if ( $value = get_post_meta( $post_id, '_gdpr_telemetry_' .$key, true ) ) {
			return $value;
		}

		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * The debug backtrace of the call. This gives us the file and line of origin of the call.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @return array Extra information about the call like File and Line.
	 */
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

	/**
	 * Is the call coming from a theme or plugin?
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  string $path Path to the file.
	 * @return array        The name of the plugin or theme that made the call.
	 */
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

	/**
	 * Figures out if the file that made the call belongs to a plugin.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  string $path The path to the file that made the call.
	 * @return string       The plugin name.
	 */
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

	/**
	 * Figures out if the file that made the call belongs to a theme.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  string $path The path to the file that made the call.
	 * @return string       The theme name.
	 */
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

	/**
	 * The data that was transmitted.
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @param  array $args The http call arguments.
	 * @return mixed       The request body.
	 */
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
