<?php

namespace WebChemistry\Assets\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use WebChemistry\Assets\AssetsMacro;
use WebChemistry\Assets\AssetsManager;

class AssetsExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'resources' => [],
		'minify' => '%debugMode%',
		'baseDir' => '%wwwDir%/',
	];

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$config = Helpers::expand($this->validateConfig($this->defaults), $builder->parameters);
		$assets = $this->getAssets($config['resources'], !$config['minify'], $config['baseDir']);

		$this->compiler->addDependencies($config['resources']);

		$builder->addDefinition($this->prefix('manager'))
			->setClass(AssetsManager::class, [$assets]);
	}

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		if ($builder->hasDefinition('latte.latteFactory')) {
			$builder->getDefinition('latte.latteFactory')
				->addSetup(AssetsMacro::class . '::install(?->getCompiler());', ['@self']);
		}
	}

	/**
	 * @param array $resources
	 * @param bool $minify
	 * @return array
	 */
	public function getAssets(array $resources, $minify, $baseDir) {
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
					if ($minify) {
						$return[$type][$minified][] = $minified;
						continue;
					}
					foreach ((array) $assets as $row) {
						if (strpos($row, '*') !== FALSE) {
							/** @var \SplFileInfo $file */
							foreach (Finder::findFiles(basename($row))->in($baseDir . dirname($row)) as $file) {
								$return[$type][$minified][] = dirname($row) . '/' . $file->getBasename();
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
