<?php
// Remove the original bpfwp_get_opening_hours_array function that was registered with the 'init' action hook
	remove_action( 'init', 'bpwfwp_print_opening_hours' );
	remove_action( 'init', 'bpfwp_get_opening_hours_array' );

// Define your own version of the bpfwp_get_opening_hours_array function
if ( ! function_exists( 'bpwfwp_print_opening_hours' ) ) {
	/**
	 * Print the opening hours.
	 *
	 * @since  0.0.1
	 * @access public
	 * @param  string $location The location associated with the hours.
	 * @return string|void Returns an empty string if no hours exist.
	 */
	function bpwfwp_print_opening_hours( $location = false ) {
		global $bpfwp_controller;

		$hours = bpfwp_setting( 'opening-hours', $location );

		if ( empty( $hours ) ) {
			return '';
		}

		// Get the opening hours in a returnable format
		$return_data = bpfwp_get_opening_hours_array( $hours );

		if ( ! bpfwp_get_display( 'show_opening_hours' ) ) {
			return;
		}

		$tz = new DateTimeZone( wp_timezone_string() );

		// Output display format.
		if ( bpfwp_get_display( 'show_opening_hours_brief' ) ) :
		?>

		<div class="bp-opening-hours-brief">

			<?php
			$slots = array();
			foreach ( $hours as $slot ) {

				// Skip this entry if no weekdays are set.
				if ( empty( $slot['weekdays'] ) ) {
					continue;
				}

				$days = array();
				$weekdays_i18n = array(
//					'monday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-monday-abbreviation' ) ),
//					'tuesday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-tuesday-abbreviation' ) ),
//					'wednesday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-wednesday-abbreviation' ) ),
//					'thursday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-thursday-abbreviation' ) ),
//					'friday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-friday-abbreviation' ) ),
//					'saturday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-saturday-abbreviation' ) ),
//					'sunday'	=> esc_html( $bpfwp_controller->settings->get_setting( 'label-sunday-abbreviation' ) ),
// add wordpress values
			'monday' => substr( __( 'Monday', 'default' ), 0, 2 ),
			'tuesday' => substr( __( 'Tuesday', 'default' ), 0, 2 ),
			'wednesday' => substr( __( 'Wednesday', 'default' ), 0, 2 ),
			'thursday' => substr( __( 'Thursday', 'default' ), 0, 2 ),
			'friday' => substr( __( 'Friday', 'default' ), 0, 2 ),
			'saturday' => substr( __( 'Saturday', 'default' ), 0, 2 ),
			'sunday' => substr( __( 'Sunday', 'default' ), 0, 2 ),
				);
				foreach ( $slot['weekdays'] as $day => $val ) {
					$days[] = $weekdays_i18n[ $day ];
				}
				$days_string = ! empty( $days ) ? join( _x( ',', 'Separator between days of the week when displaying opening hours in brief. Example: Mo,Tu,We', 'business-profile' ), $days ) : '';

				if ( empty( $slot['time'] ) ) {
					$string = sprintf( _x( '%s all day', 'Brief opening hours description which lists days_strings when open all day. Example: Mo,Tu,We all day', 'business-profile' ), $days_string );
				} else {
					unset( $start );
					unset( $end );
					if ( ! empty( $slot['time']['start'] ) ) {
						$start = new DateTime( $slot['time']['start'], $tz );
					}
					if ( ! empty( $slot['time']['end'] ) ) {
						$end = new DateTime( $slot['time']['end'], $tz );
					}

					if ( empty( $start ) ) {
						$string = sprintf( _x( '%s open until %s', 'Brief opening hours description which lists the days followed by the closing time. Example: Mo,Tu,We open until 9:00pm', 'business-profile' ), $days_string, $end->format( get_option( 'time_format' ) ) );
					} elseif ( empty( $end ) ) {
						$string = sprintf( _x( '%s open from %s', 'Brief opening hours description which lists the days followed by the opening time. Example: Mo,Tu,We open from 9:00am', 'business-profile' ), $days_string, $start->format( get_option( 'time_format' ) ) );
					} else {
						$string = sprintf( _x( '%s %s&thinsp;&ndash;&thinsp;%s', 'Brief opening hours description which lists the days followed by the opening and closing times. Example: Mo,Tu,We 9:00am&thinsp;&ndash;&thinsp;5:00pm', 'business-profile' ), $days_string, $start->format( get_option( 'time_format' ) ),  $end->format( get_option( 'time_format' ) ) );
					}
				}

				$slots[] = $string;
			}

			echo join( _x( '; ', 'Separator between multiple opening times in the brief opening hours. Example: Mo,We 9:00 AM&thinsp;&ndash;&thinsp;5:00 PM; Tu,Th 10:00 AM&thinsp;&ndash;&thinsp;5:00 PM', 'business-profile' ), $slots );
			?>

		</div>

		<?php
			return $return_data;
		endif; // Brief opening hours.

		$weekdays_display = array(
// remove fixed values
//			'monday'	=> __( 'Monday' ),
//			'tuesday'	=> __( 'Tuesday' ),
//			'wednesday'	=> __( 'Wednesday' ),
//			'thursday'	=> __( 'Thursday' ),
//			'friday'	=> __( 'Friday' ),
//			'saturday'	=> __( 'Saturday' ),
//			'sunday'	=> __( 'Sunday' ),
// add wordpress values
			'monday' => __( 'Monday', 'default' ),
			'tuesday' => __( 'Tuesday', 'default' ),
			'wednesday' => __( 'Wednesday', 'default' ),
			'thursday' => __( 'Thursday', 'default' ),
			'friday' => __( 'Friday', 'default' ),
			'saturday' => __( 'Saturday', 'default' ),
			'sunday' => __( 'Sunday', 'default' ),			
		);

		$weekdays = array();
		foreach ( $hours as $rule ) {

			// Skip this entry if no weekdays are set.
			if ( empty( $rule['weekdays'] ) ) {
				continue;
			}

			if ( empty( $rule['time'] ) ) {
				$time = __( 'Open', 'business-profile' );

			} else {

				if ( ! empty( $rule['time']['start'] ) ) {
					$start = new DateTime( $rule['time']['start'], $tz );
				}
				if ( ! empty( $rule['time']['end'] ) ) {
					$end = new DateTime( $rule['time']['end'], $tz );
				}

				if ( empty( $start ) ) {
					$time = __( 'Open until ', 'business-profile' ) . $end->format( get_option( 'time_format' ) );
				} elseif ( empty( $end ) ) {
					$time = __( 'Open from ', 'business-profile' ) . $start->format( get_option( 'time_format' ) );
				} else {
					$time = $start->format( get_option( 'time_format' ) ) . _x( '&thinsp;&ndash;&thinsp;', 'Separator between opening and closing times. Example: 9:00am&thinsp;&ndash;&thinsp;5:00pm', 'business-profile' ) . $end->format( get_option( 'time_format' ) );
				}
			}

			foreach ( $rule['weekdays'] as $day => $val ) {

				if ( ! array_key_exists( $day, $weekdays ) ) {
					$weekdays[ $day ] = array();
				}

				$weekdays[ $day ][] = $time;
			}
		}

		if ( count( $weekdays ) ) {

			// Order the weekdays and add any missing days as "closed".
			$weekdays_ordered = array();
			foreach ( $weekdays_display as $slug => $name ) {
				if ( ! array_key_exists( $slug, $weekdays ) ) {
					$weekdays_ordered[ $slug ] = array( __( 'Closed', 'business-profile' ) );
				} else {
					$weekdays_ordered[ $slug ] = $weekdays[ $slug ];
				}
			}

			$data = array(
				'weekday_hours' => $weekdays_ordered,
				'weekday_names' => $weekdays_display,
			);

			$template = new bpfwpTemplateLoader;
			$template->set_template_data( $data );

			if ( bpfwp_get_display( 'location' ) ) {
				$template->get_template_part( 'opening-hours', bpfwp_get_display( 'location' ) );
			} else {
				$template->get_template_part( 'opening-hours' );
			}
		}

		return $return_data;
	}
}

if ( ! function_exists( 'bpfwp_get_opening_hours_array' ) ) {
	/**
	 * Returns an array of opening hours, outputable for json+ld
	 *
	 * @since  2.1.0
	 * @access public
	 * @param  array $hours A list of opening hours.
	 * @return array
	 */
	function bpfwp_get_opening_hours_array( $hours ) {
	if (!empty($hours)) {
	
		$opening_hours = array();

		$weekdays_schema = array(
//			'monday'	=> 'Mo',
//			'tuesday'	=> 'Tu',
//			'wednesday'	=> 'We',
//			'thursday'	=> 'Th',
//			'friday'	=> 'Fr',
//			'saturday'	=> 'Sa',
//			'sunday'	=> 'Su',
//				replace with wordpress first 2 characters of the week
			'monday' => substr( __( 'Monday', 'default' ), 0, 2 ),
			'tuesday' => substr( __( 'Tuesday', 'default' ), 0, 2 ),
			'wednesday' => substr( __( 'Wednesday', 'default' ), 0, 2 ),
			'thursday' => substr( __( 'Thursday', 'default' ), 0, 2 ),
			'friday' => substr( __( 'Friday', 'default' ), 0, 2 ),
			'saturday' => substr( __( 'Saturday', 'default' ), 0, 2 ),
			'sunday' => substr( __( 'Sunday', 'default' ), 0, 2 ),
		);

		// Output proper schema.org format.
		foreach ( $hours as $slot ) {

			// Skip this entry if no weekdays are set.
			if ( empty( $slot['weekdays'] ) ) {
				continue;
			}

			$days = array();
			foreach ( $slot['weekdays'] as $day => $val ) {
				$days[] = $weekdays_schema[ $day ];
			}
			$string = ! empty( $days ) ? join( ',', $days ) : '';

			if ( ! empty( $string ) && ! empty( $slot['time'] ) ) {

				if ( empty( $slot['time']['start'] ) ) {
					$start = '00:00';
				} else {
					$start = trim( substr( $slot['time']['start'], 0, -2 ) );
					if ( 'PM' === substr( $slot['time']['start'], -2 ) ) {
						$split = explode( ':', $start );
						$split[0] += intval($split[0]) == 12 ? 0 : 12;
						$start = join( ':', $split );
					}
					if ( 'AM' === substr( $slot['time']['start'], -2 ) && '12:00' === $start ) {
						$start = '00:00';
					}
				}

				if ( empty( $slot['time']['end'] ) ) {
					$end = '24:00';
				} else {
					$end = trim( substr( $slot['time']['end'], 0, -2 ) );
					if ( 'PM' === substr( $slot['time']['end'], -2 ) ) {
						$split = explode( ':', $end );
						$split[0] += intval($split[0]) == 12 ? 0 : 12;
						$end = join( ':', $split );
					}
					if ( ! empty( $slot['time']['end'] ) && 'AM' === substr( $slot['time']['end'], -2 ) && '12:00' === $end ) {
						$end = '24:00';
					}
				}

				$string .= ' ' . $start . '-' . $end;
			}
			
			$opening_hours[] = '"' . $string . '"';
		}

		return array( 'openingHours' => '[' . implode( ',', $opening_hours ) . ']' );
	}
	}
}
// Register your custom bpfwp_get_opening_hours_array function with the 'init' action hook
add_action( 'init', 'bpwfwp_print_opening_hours' );
add_action( 'init', 'bpfwp_get_opening_hours_array' );

remove_action( 'init', 'bpwfwp_print_exceptions' );
if ( ! function_exists( 'bpwfwp_print_exceptions' ) ) {
	/**
	 * Print the exceptions, special opening hours or holidays.
	 *
	 * @since  2.1.0
	 * @access public
	 * @param  string $location The location associated with the exceptions.
	 * @return string|void Returns an empty string if no exceptions exist.
	 */
	function bpwfwp_print_exceptions( $location = false ) {
		global $bpfwp_controller;

		$exceptions = bpfwp_setting( 'exceptions', $location );
		
		if ( empty( $exceptions ) || ! bpfwp_get_display( 'show_exceptions' ) || ! function_exists( 'wp_date' ) ) {
			return '';
		}

		// Print the metatags with proper schema formatting.
		$return_data = bpfwp_get_exceptions_array( $exceptions );

		$date_format = get_option('date_format');
		$time_format = get_option('time_format');

		$tz = new DateTimeZone( wp_timezone_string() );

		$data = array(
			'special_hours' => array(),
			'holiday'       => array()
		);

		foreach ( $exceptions as $exception ) {

			if ( empty( $exception['date'] ) ) { continue; }

			if ( time() > strtotime( $exception['date'] ) + 24*3600 ) { continue; }
			
			if ( array_key_exists( 'time', $exception ) ) {
				// special opening-hours
				$data['special_hours'][] = $exception;
			}
			else {
				// holiday
				$data['holiday'][] = $exception;
			}
		}

		usort( $data['special_hours'], function( $a, $b ) {
			return strcasecmp( $a['date'], $b['date'] );
		});
		usort( $data['holiday'], function( $a, $b ) {
			return strcasecmp( $a['date'], $b['date'] );
		});

		if ( 0 < count( $data['special_hours'] ) ) { ?>
			
			<div class="bp-opening-hours special">
				<span class="bp-title"><?php echo esc_html( $bpfwp_controller->settings->get_setting( 'label-special-opening-hours' ) ); ?></span>
			
				<?php foreach ( $data['special_hours'] as $exception ) { ?>
				
					<?php 
						$date  = new DateTime( $exception['date'], $tz );
						$start = new DateTime( $exception['time']['start'], $tz );
						$end   = new DateTime( $exception['time']['end'], $tz );
					?>

					<div class="bp-date">
						<span class="label"><?php echo wp_date( $date_format, $date->format( 'U' ) ); ?></span>
						<span class="bp-times">
							<span class="bp-time">
								<?php 
									echo wp_date( $time_format, $start->format( 'U' ) ) 
										. ' – ' 
										. wp_date( $time_format, $end->format( 'U' ) );
								?>
							</span>
						</span>
					</div>
				<?php } ?>

			</div>
		<?php }

		if ( 0 < count( $data['holiday'] ) ) { ?>

			<div class="bp-opening-hours holiday">
				<span class="bp-title"><?php echo esc_html( $bpfwp_controller->settings->get_setting( 'label-holidays' ) ); ?></span>

				<?php foreach ( $data['holiday'] as $exception ) { ?>
				
					<?php $date = new DateTime( $exception['date'], $tz ); ?>
				
					<div class="bp-date">
						<span class="label">
							<?php echo wp_date( $date_format, $date->format( 'U' ) ); ?>
							</span>
						<span class="bp-times">
							<span class="bp-time">
								<?php echo esc_html( $bpfwp_controller->settings->get_setting( 'label-closed' ) ); ?>
							</span>
						</span>
					</div>
				<?php } ?>

			</div>
		<?php }

		return $return_data;
	}
}
add_action( 'init', 'bpwfwp_print_exceptions' );