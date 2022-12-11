<?php
namespace Starbug\Config;

use Starbug\ResourceLocator\ResourceLocatorInterface;

class Config implements ConfigInterface {

  protected $locator;
  protected $configs = [];

  public function __construct(ResourceLocatorInterface $locator) {
    $this->locator = $locator;
  }

  /**
   * Get configuration value(s)
   *
   * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'.
   * @param string $scope the scope/category of the configuration item.
   *
   * @return string|array configuration value(s)
   */
  public function get($key, $scope = "etc") {
    $parts = explode(".", $key);

    $key = array_shift($parts);

    if (empty($this->configs[$key])) {
      $resourceTypes = [
        "yml" => $this->locator->locate($key.".yml", $scope),
        "json" => $this->locator->locate($key.".json", $scope)
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

    $value = $this->configs[$key];

    while (!empty($parts)) {
      $next = array_shift($parts);
      $value = $value[$next];
    }

    return $value;
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
