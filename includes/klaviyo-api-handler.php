<?php

/**
 * Subscribe an email to a Klaviyo list.
 *
 * @param string $email The email address to subscribe.
 * @param int $list_id The Klaviyo list ID.
 * @param string $api_key The API key for Klaviyo.
 * @param string $url The API endpoint URL.
 * @return array An array containing error messages or success response.
 */
function klaviyo_subscribe_to_list($email, $list_id, $api_key, $url) {
  $response = [];
  
  // Prepare the data for the API request
  $data = json_encode([
    'api_key' => $api_key,
    'profiles' => [
      ['email' => $email]
    ]
  ]);

  // Perform the API request
  $result = wp_remote_post($url, [
    'body'    => $data,
    'headers' => [
      'Content-Type' => 'application/json'
    ]
  ]);

  // Handle the response
  if (is_wp_error($result)) {
    $response[] = $result->get_error_message();
  } else {
    $parsed_body = $result['response'];
    if (isset($parsed_body['message']) && $parsed_body['message'] === 'OK') {
      $response[] = 'Subscription successful';
    } else {
      $response[] = 'Subscription failed';
    }
  }
  return $response;
}
