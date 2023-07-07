<?php

namespace Drupal\lesroidelareno\Plugin\Mail;

use Drupal\Core\Mail\Plugin\Mail\PhpMail;
use Symfony\Component\Mime\Header\Headers;

/**
 * Defines the default Drupal mail backend, using PHP's native mail() function.
 *
 * @Mail(
 *   id = "test_php_mailer_plugin",
 *   label = @Translation("TestPhpMailerPlugin"),
 *   description = @Translation("Sends the message as plain text, using PHP's native mail() function.")
 * )
 */
class TestPhpMailerPlugin extends PhpMail {
  
}