<?php

namespace spec\Starbug\Config;

use Starbug\Config\Config;
use PhpSpec\ObjectBehavior;
use Starbug\ResourceLocator\ResourceLocatorInterface;

/**
 * Spec test for Starbug\Config\Config.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class ConfigSpec extends ObjectBehavior {
  public function let(ResourceLocatorInterface $locator) {
    $locator->locate("test.yml", "etc")->willReturn([
      "examples/example1/test.yml",
      "examples/example2/test.yml"
    ]);
    $locator->locate("test.json", "etc")->willReturn([]);
    $this->beConstructedWith($locator);
  }
  public function it_is_initializable() {
    $this->shouldHaveType(Config::class);
  }
  public function it_can_load_yml_data() {
    $this->get("test")->shouldReturn([
      "missing" => [
        "controller" => ["Starbug\Core\Controller", "missing"]
      ],
      "forbidden" => [
        "controller" => ["Starbug\Core\Controller", "forbidden"],
        "groups" => "user"
      ],
      "profile" => [
        "view" => "profile.html"
      ]
    ]);
  }
}
