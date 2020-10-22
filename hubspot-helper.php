<?php

class hubspot_helper {
  private const API_BASE_URL = "https://api.hubapi.com";

  function __construct ($api_key) {
    if (!function_exists('wp_remote_request')) throw new Exception("Global function required: wp_remote_request");

    $this->api_key = $api_key;
  }

  /**
   * Make a GET request and return the response.
   */
  function get ($url, $args = array()) {
    if (!$this->api_key)
      throw new Exception("Hubspot API key required");
      
    return wp_remote_get(self::API_BASE_URL . $url . '?hapikey=' . $this->api_key, $args);
  }

  /**
   * Get all forms from the account.
   */
  function get_forms () {
    try {
      $response = $this->get('/forms/v2/forms');
      return json_decode($response['body']);
    }
    catch (Exception $err) {
      throw $err;
    }
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