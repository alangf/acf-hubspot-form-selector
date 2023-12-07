<?php

class hubspot_helper {
  private const API_BASE_URL = "https://api.hubapi.com/forms/v2/forms";

  function __construct ($hubspot_access_token) {
    if (!function_exists('wp_remote_request')) throw new Exception("Global function required: wp_remote_request");

    $this->hubspot_access_token = $hubspot_access_token;
  }

  /**
   * Make a GET request and return the response.
   */
  function getForms() {

  }

  /**
   * Get all forms from the account.
   */
  function get_forms () {
    if (!$this->hubspot_access_token)
      throw new Exception("Hubspot access token required");
    $args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $this->hubspot_access_token,
            'Content-Type'  => 'application/json'
        )
    );
      
    $response = wp_remote_get(self::API_BASE_URL, $args);
    return json_decode($response['body']);
  }

  public static function get_form_embed_html($guid, $portalId) {
    return sprintf('
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          hbspt.forms.create({
            portalId: "%s",
            formId: "%s"
          });
        });
      </script>', $portalId, $guid);
  }
}