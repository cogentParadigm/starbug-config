<?php
namespace Starbug\Config\Helper;

use Starbug\Config\ConfigInterface;

class ConfigHelper {
  public function __construct(
    protected ConfigInterface $config
  ) {
  }
  public function helper() {
    return $this->config;
  }
}
