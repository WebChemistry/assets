<?php

declare(strict_types=1);

namespace WebChemistry\Assets\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use WebChemistry\Assets\AssetsException;
use WebChemistry\Assets\AssetsMacro;
use WebChemistry\Assets\AssetsManager;
use WebChemistry\Assets\AssetsWrapper;
use WebChemistry\Assets\HttpAssets;

class AssetsExtension extends CompilerExtension {

	private const SUPPORT_TYPES = [
		'css' => TRUE,
		'js' => TRUE
	];

	/** @var array */
	public $defaults = [
		'resources' => [],
		'minify' => NULL,
		'baseDir' => NULL,
	];

	/** @var array */
	private $parameters = [];

	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();
		$config = $this->getParsedConfig();
		$assets = $this->getAssets($config['resources'], $config['minify'], $config['baseDir']);

		$this->compiler->addDependencies($config['resources']);

		$builder->addDefinition($this->prefix('manager'))
			->setType(AssetsManager::class);

		$builder->addDefinition($this->prefix('wrapper'))
			->setFactory(AssetsWrapper::class, [$builder->parameters['wwwDir'], $assets, $config['minify']]);

		$builder->addDefinition($this->prefix('httpAssets'))
			->setType(HttpAssets::class);
	}

	public function beforeCompile(): void {
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
	public function getAssets(array $resources, bool $minify, string $baseDir): array {
		$config = [];
		$return = [];

		foreach ($resources as $resource) {
			$decompiled = Neon::decode(file_get_contents($resource));
			if ($config) {
				$config = Helpers::merge($config, $decompiled);
			} else {
				$config = $decompiled;
			}
		}

		$this->processParameters($config);

		foreach ($config as $module => $moduleArray) {
			foreach ($moduleArray as $type => $typeArray) {
				if (!isset(self::SUPPORT_TYPES[$type])) {
					throw new AssetsException("Found section '$type', but expected one of " .
						implode(', ', array_keys(self::SUPPORT_TYPES)));
				}
				foreach ($typeArray as $minified => $assets) {
					$this->parseParameters($minified);
					if ($minify) {
						$return[$type][$minified][] = $minified;
						$return['meta'][$module][$type][] = $minified;
						continue;
					}
					foreach ((array) $assets as $row) {
						$this->parseParameters($row);
						if (strpos($row, '*') !== FALSE) {
							/** @var \SplFileInfo $file */
							foreach (Finder::findFiles(basename($row))->in($baseDir . '/' . dirname($row)) as $file) {
								$return[$type][$minified][] = $return['meta'][$module][$type][] = dirname($row) . '/' . $file->getBasename();
							}
						} else {
							$return[$type][$minified][] = $return['meta'][$module][$type][] = $row;
						}
					}
				}
			}
		}

		return $return;
	}

	private function processParameters(array &$config): void {
		if (!isset($config['parameters'])) {
			return;
		}

		foreach ($config['parameters'] as $key => $val) {
			$this->parameters['%' . $key . '%'] = $val;
		}
		unset($config['parameters']);
	}

	private function parseParameters(string &$str): void {
		if (!$this->parameters) {
			return;
		}

		$str = strtr($str, $this->parameters);
	}

	/**
	 * @return array
	 */
	private function getParsedConfig(): array {
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
