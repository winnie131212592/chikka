<?php namespace Borla\Chikka\Models;

use Borla\Chikka\Base\Model;
use Borla\Chikka\Models\Cost;

use Borla\Chikka\Exceptions\InvalidAttribute;

use Borla\Chikka\Support\Loader;
use Borla\Chikka\Support\Utilities;
use Carbon\Carbon;

/**
 * Notification
 */

class Notification extends Model {

  /**
   * Message types
   */
  const OUTGOING  = 1;

  /**
   * Constructor
   */
  function __construct(array $data) {
    // Set attributes
    $this->setAttributes(Utilities::arrayExtract([
      'message_type',
      'type',
      'shortcode',
      'message_id',
      'id',
      'status',
      'credits_cost',
      'cost',
      'timestamp',
    ], $data));
  }
  
  /**
   * When an attribute is set
   */
  protected function onSetAttribute($name, $value) {
    // Listen to these attributes
    $listen = [
      'message_type'=> 'type',
      'message_id'=> 'id',
      'credits_cost'=> 'cost',
    ];
    // If set
    if (isset($listen[$name])) {
      // If not yet set
      if ( ! isset($this->{$listen[$name]})) {
        // Set corresponding attribute
        $this->{$listen[$name]} = $value;
      }
    }
  }

  /**
   * Types
   */
  static function types() {
    // Return
    return [
      static::OUTGOING  => 'outgoing',
    ];
  }

  /**
   * Set id
   */
  protected function setIdAttribute($value) {
    // If there's no value
    if ( ! $value) {
      // Return md5 of current time
      return md5(microtime(true));
    }
    // Otherwise
    else {
      // Return only first 32
      return substr($value, 0, 32);
    }
  }

  /**
   * Set type
   */
  protected function setTypeAttribute($value) {
    // If null
    if ($value === null) {
      // Return null
      return null;
    }
    // If numeric
    if (is_int($value) || is_numeric($value)) {
      // If not valid
      if (isset(static::types()[$value])) {
        // Return int value
        return (int) $value;
      }
    }
    // Else
    else {
      // Find 
      if (($type = array_search(strtolower($value), static::types())) !== false) {
        // Return type
        return $type;
      }
    }
    // Throw error
    throw new InvalidAttribute('Invalid type value: ' . $value);
  }

  /**
   * Cost
   */
  protected function setCostAttribute($value) {
    // Return cost
    return ($value instanceof Cost) ? $value : Loader::cost($value);
  }

  /**
   * Set timestamp
   */
  protected function setTimestampAttribute($value) {
    // If nothing
    if ( ! $value) {
      // Creat new 
      return new Carbon(null, $this->getTimezone());
    }
    // Use carbon
    return ($value instanceof Carbon) ? $value : new Carbon($value, $this->getTimezone());
  }

}