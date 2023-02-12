<?php

namespace Config;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseConfig;

/**
 * This class holds the configuration options stored from the database so that on launch those settings can be cached
 * once in memory.  The settings are referenced frequently, so there is a significant performance hit to not storing
 * them.
 */
class OSPOS extends BaseConfig
{
	public array $settings;

	public string $commit_sha1 = 'dev';	//TODO: Travis scripts need to be updated to replace this with the commit hash on build

	private CacheInterface $cache;

	public function __construct()
	{
		parent::__construct();
		$this->cache = Services::cache();
		$this->set_settings();
	}

	public function set_settings(): void
	{
		if(!$this->cache->get('settings'))
		{
			$appconfig = model('Appconfig');
			foreach($appconfig->get_all()->getResult() as $app_config)
			{
				$this->settings[$app_config->key] = $app_config->value;
			}
			$this->cache->save('settings', $this->settings);
		}

		$this->settings = $this->cache->get('settings');
	}

	public function update_settings(): void
	{
		$this->cache->delete('settings');
		$this->set_settings();
	}
}
