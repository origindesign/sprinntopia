<?php
/**
 * @file
 * Contains ProviderManager.
 */

namespace Drupal\meteo;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Gathers the provider plugins.
 */
class ProviderManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {

      parent::__construct('Plugin/Provider', $namespaces, $module_handler, 'Drupal\meteo\ProviderInterface', 'Drupal\meteo\Annotation\MeteoProvider');

      $this->alterInfo('meteo_provider_info');
      $this->setCacheBackend($cache_backend, 'meteo_provider');

  }

}
