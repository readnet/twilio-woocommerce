<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://giannisftaras.dev/
 * @since      1.0.0
 *
 * @package    Rtwilio
 * @subpackage Rtwilio/admin/partials
 */
?>

<form method="POST" action='options.php'>
   <?php
         settings_fields($this->plugin_name);
         do_settings_sections('rtwilio-settings-page');

         submit_button();
   ?>
</form>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
