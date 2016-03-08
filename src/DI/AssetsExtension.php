<?php

namespace WebChemistry\Assets\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

class AssetsExtension extends CompilerExtension {

	/** @var array */
	private $defaults = [
		'resources' => [],
		'debugMode' => '%debugMode%',
		'baseDir' => '%wwwDir%/'
	];

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$config = Helpers::expand($this->validateConfig($this->defaults, $this->getConfig()), $builder->parameters);
		$assets = $this->getAssets($config['resources'], $config['debugMode'], $config['baseDir']);

		$this->compiler->addDependencies($config['resources']);

		$builder->addDefinition($this->prefix('manager'))
			->setClass('WebChemistry\Assets\Manager', [$assets]);
	}

	/**
	 * @param array $resources
	 * @param bool $debugMode
	 * @return array
	 */
	public function getAssets(array $resources, $debugMode, $baseDir) {
		$config = [];
		$return = [];

		foreach ($resources as $resource) {
			$contents = file_get_contents($resource);
			$decompiled = Strings::endsWith($resource, '.json') ? json_decode($contents, TRUE) : Neon::decode($contents);
			$config = \Nette\DI\Config\Helpers::merge($config, $decompiled);
		}

		foreach ($config as $moduleArray) {
			foreach ($moduleArray as $type => $typeArray) {
				if (!in_array($type, ['css', 'js'])) {
					continue;
				}
				foreach ($typeArray as $minified => $assets) {
					if (!$debugMode) {
						$return[$type][$minified][] = $minified;
						continue;
					}
					foreach ((array) $assets as $row) {
						if (strpos($row, '*') !== FALSE) {
							foreach (Finder::findFiles(basename($row))->in($baseDir . dirname($row)) as $file) {
								$return[$type][$minified][] = dirname($row) . '/' . $file->getBaseName();
							}
						} else {
							$return[$type][$minified][] = $row;
						}
					}
				}
			}
		}

		return $return;
	}

}
