<?php

namespace Drupal\lesroidelareno\HandlerClass;

use Drupal\webform\WebformEntityAccessControlHandler;

/**
 * Le webform est à revoir par example les conditions de delete et aussi les
 * autres $operation.
 *
 * @author stephane
 *        
 */
class WebformAccess extends WebformEntityAccessControlHandler {
  use AccessDefault;
  
}