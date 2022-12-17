<?php
namespace Starbug\Config;

use Starbug\ResourceLocator\ResourceLocatorInterface;

class Config implements ConfigInterface {

  protected $locator;
  protected $dir;
  protected $configs = [];

  public function __construct(ResourceLocatorInterface $locator, $dir = "etc") {
    $this->locator = $locator;
    $this->dir = $dir;
  }

  /**
   * Get configuration value
   *
   * @param string $key the name of the configuration entry.
   *
   * @return string|array configuration value
   */
  public function get($key) {
    if (empty($this->configs[$key])) {
      $resourceTypes = [
        "yml" => $this->locator->locate($key.".yml", $this->dir),
        "json" => $this->locator->locate($key.".json", $this->dir)
      ];
      $result = [];
      foreach ($resourceTypes as $type => $resources) {
        foreach ($resources as $resource) {
          $data = $this->decode($resource, $type);
          $result = $this->merge($result, $data);
        }
      }
      $this->configs[$key] = $result;
    }

    return $this->configs[$key];
  }

  /**
   * Set configuration value
   *
   * @param string $key The name of the configuration entry.
   * @param mixed $value The configuration value.
   */
  public function set($key, $value) {
    $this->configs[$key] = $value;
  }

  /**
   * Decode/parse a configuration file.
   *
   * @param string $resource The path of the file.
   * @param string $format The format (yml or json).
   *
   * @return array The decoded data.
   */
  protected function decode($resource, $format = "yml") {
    if ($format == "json") {
      $text = file_get_contents($resource);
      return json_decode($text, true);
    }
    return yaml_parse_file($resource);
  }

  /**
   * Recursively merge two arrays.
   *
   * NOTE: array_merge_recursive is not used for at least one reason
   * which is that it will combine values for the same array key into
   * an array even if those values are not arrays.
   *
   * @param array $array1 First array.
   * @param array $array2 Second array.
   *
   * @return array The merged array.
   */
  protected function merge(array &$array1, array &$array2) {
    $merged = $array1;
    if (isset($merged[0]) && isset($array2[0])) {
      $merged = array_merge($merged, $array2);
    } else {
      foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
          $merged[$key] = $this->merge($merged[$key], $value);
        } else {
          $merged[$key] = $value;
        }
      }
    }
    return $merged;
  }
}
