<?php
/**
 * Custom /settings route to update plugin settings.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 4.5
 * @package InstantImages
 */

add_action(
	'rest_api_init',
	function () {
		$my_namespace = 'instant-images';
		$my_endpoint  = '/settings';
		register_rest_route(
			$my_namespace,
			$my_endpoint,
			[
				'methods'             => 'POST',
				'callback'            => 'instant_images_settings',
				'permission_callback' => function () {
					return InstantImages::instant_img_has_settings_access();
				},
			]
		);
	}
);

/**
 * Save plugin settings.
 *
 * @param WP_REST_Request $request API request.
 * @author ConnektMedia <support@connekthq.com>
 * @package InstantImages
 */
function instant_images_settings( WP_REST_Request $request ) {
	if ( InstantImages::instant_img_has_settings_access() ) {

		// Get JSON Data.
		$data = json_decode( $request->get_body(), true ); // Get contents of request body.

		if ( $data ) {
			$option = INSTANT_IMAGES_API_SETTINGS;

			// Global settings.
			$options = get_option( $option );
			$setting = sanitize_text_field( $data['setting'] ); // The setting to update.
			$value   = sanitize_text_field( $data['value'] ); // The value to update.

			if ( $setting ) {
				$options[ $setting ] = $value;
				update_option( $option, $options );

				// Success.
				$response = [
					'success' => true,
					'msg'     => __( 'Settings saved.', 'instant-images' ),
				];
			} else {
				// Error.
				$response = [
					'success' => false,
					'msg'     => __( 'Unable to save settings.', 'instant-images' ),
				];
			}

			// Send response as JSON.
			wp_send_json( $response );

		}
	}
}
