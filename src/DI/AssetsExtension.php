<?php

namespace WebChemistry\Assets\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use WebChemistry\Assets\AssetsException;
use WebChemistry\Assets\AssetsMacro;
use WebChemistry\Assets\AssetsManager;

class AssetsExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'resources' => [],
		'minify' => NULL,
		'baseDir' => NULL,
	];

	/** @var array */
	private static $supportTypes = ['css' => TRUE, 'js' => TRUE];

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();
		$config = $this->getParsedConfig();
		$assets = $this->getAssets($config['resources'], $config['minify'], $config['baseDir']);

		$this->compiler->addDependencies($config['resources']);

		$builder->addDefinition($this->prefix('manager'))
			->setClass(AssetsManager::class, [$assets, $config['minify']]);
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
	 * @param string $baseDir
	 * @throws AssetsException
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
				if (!isset(self::$supportTypes[$type])) {
					throw new AssetsException("Found section '$type', but expected one of " .
											  implode(', ', array_keys(self::$supportTypes)));
				}
				foreach ($typeArray as $minified => $assets) {
					if ($minify) {
						$return[$type][$minified][] = $minified;
						continue;
					}
					foreach ((array) $assets as $row) {
						if (strpos($row, '*') !== FALSE) {
							/** @var \SplFileInfo $file */
							foreach (Finder::findFiles(basename($row))->in($baseDir . '/' . dirname($row)) as $file) {
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

	/**
	 * @return array
	 */
	private function getParsedConfig() {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		if ($config['minify'] === NULL) {
			$config['minify'] = !$builder->parameters['debugMode'];
		}
		if ($config['baseDir'] === NULL) {
			$config['baseDir'] = $builder->parameters['wwwDir'];
		} else {
			$config['baseDir'] = rtrim($config['baseDir'], '/\\');
		}

		return $config;
	}

}
