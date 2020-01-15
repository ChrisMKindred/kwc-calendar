<?php
/**
 * Handles all the background processing.
 *
 * @package gci
 */

use ICal\ICal;
use Carbon\Carbon;
use Sabre\VObject;

/**
 * Extends the WP_Background_process class. Handles the individual tasks for
 * the background processes.
 */
class GCI_BackgroundProcess extends WP_Background_Process {

	/**
	 * The name of the action used for transients.
	 *
	 * @var string
	 * @since 0.0.0
	 */
	protected $action = 'gci_importer_background_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		try {
			$ical = new ICal(
				'ical.ics',
				array(
					'defaultSpan'                 => 2,
					'defaultTimeZone'             => 'America/New_York',
					'defaultWeekStart'            => 'MO',
					'disableCharacterReplacement' => false,
					'skipRecurrence'              => false,
					'useTimeZoneWithRRules'       => true,
				)
			);
			$ical->initUrl( $item['feed_url'] );
		} catch ( \Exception $e ) {
			$error = new WP_Error( rand(), 'Feed Error Item:\r\n' . print_r( $item, true ) . '\r\n' . 'Exception:\r\n' . print_r( $e, true ) );
			error_log( print_r( $error, true ) );
			return false;
		}

		$week_start = Carbon::now()->addWeek( $item['current_week'] );
		$week_end   = Carbon::now()->addWeek( $item['current_week'] + 1 );
		$events     = $ical->eventsFromRange( $week_start->toRfc2822String(), $week_end->toRfc2822String() );
		if ( $events ) {
			foreach ( $events as $event ) {
				$dtstart = $ical->iCalDateToDateTime( $event->dtstart_tz, true );
				$dtend   = $ical->iCalDateToDateTime( $event->dtend_tz, true );

				$args = array(
					'post_title'         => wp_strip_all_tags( $event->summary ),
					'post_status'        => 'publish',
					'EventStartDate'     => $dtstart->format( 'Y-m-d' ),
					'EventEndDate'       => $dtend->format( 'Y-m-d' ),
					'EventAllDay'        => 'false',
					'EventStartHour'     => $dtstart->format( 'h' ),
					'EventStartMinute'   => $dtstart->format( 'i' ),
					'EventStartMeridian' => $dtstart->format( 'a' ),
					'EventEndHour'       => $dtend->format( 'h' ),
					'EventEndMinute'     => $dtend->format( 'i' ),
					'EventEndMeridian'   => $dtend->format( 'a' ),
					'EventShowMapLink'   => 'false',
					'EventShowMap'       => 'false',
					'post_author'        => ( key_exists( 'user_id', $item ) ) ? $item['user_id'] : '',
				);

				if ( $item['category_id'] ) {
					$args['tax_input'] = array(
						'tribe_events_cat' => array( $item['category_id'] ),
					);
				}

				$post_returned = tribe_create_event( $args );
				if ( $item['category_id'] ) {
					$cat_returned = wp_set_post_terms( $post_returned, $item['category_id'], 'tribe_events_cat', false );
					wp_update_term_count( $cat_returned, 'tribe_events_cat' );
				}
			}
		}
		$item['current_week']++;
		if ( (int) $item['current_week'] >= (int) $item['weeks_to_sync'] ) {
			return false;
		}
		return $item;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}
}
