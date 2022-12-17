<?php
namespace Starbug\Config;

interface ConfigInterface {
  /**
   * Get a configuration value
   *
   * @param string $key the name of the configuration entry
   */
  public function get($key);
  /**
   * Set a configuration value
   *
   * @param string $key
   * @param mixed $value
   */
  public function set($key, $value);
}
