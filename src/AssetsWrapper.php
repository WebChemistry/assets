<?php declare(strict_types = 1);

namespace WebChemistry\Assets;

use Nette\Http\IRequest;

class AssetsWrapper {

	/** @var array */
	private $assets;

	/** @var string */
	private $basePath;

	/** @var string */
	private $baseUrl;

	/** @var bool */
	private $minify;

	/** @var string */
	private $wwwDir;

	/** @var array */
	private $timestampsCache = [];

	public function __construct(string $wwwDir, array $assets, bool $minify, IRequest $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
		$this->baseUrl = $request->getUrl()->getBaseUrl();
		$this->minify = $minify;
		$this->wwwDir = $wwwDir;
	}

	public function timestamp(string $file): string {
		if (!$this->minify) {
			return $file;
		}
		if (!isset($this->timestampsCache[$file])) {
			$this->timestampsCache[$file] = $file . '?t=' . filemtime($this->wwwDir . '/' . $file);
		}

		return $this->timestampsCache[$file];
	}

	public function getCssByModule(string $module, bool $basePath = true): iterable {
		if (!isset($this->assets['meta'][$module])) {
			throw new AssetsException("Module assets '$module' not exists.");
		}

		foreach ($this->assets['meta'][$module]['css'] as $css) {
			if (AssetsHelpers::isAbsoluteUrl($css)) {
				yield $css;
			} else {
				yield ($basePath ? $this->basePath : $this->baseUrl) . $this->timestamp($css);
			}
		}
	}

	public function getJsByModule(string $module, bool $basePath = true): iterable {
		if (!isset($this->assets['meta'][$module])) {
			throw new AssetsException("Module assets '$module' not exists.");
		}

		foreach ($this->assets['meta'][$module]['js'] as $js) {
			if (AssetsHelpers::isAbsoluteUrl($js)) {
				yield $js;
			} else {
				yield ($basePath ? $this->basePath : $this->baseUrl) . $this->timestamp($js);
			}
		}
	}

	public function getCssByMinified(string $file, bool $basePath = true): iterable {
		if (!isset($this->assets['css'][$file])) {
			throw new AssetsException("Minified assets '$file' not exists.");
		}

		foreach ($this->assets['css'][$file] as $css) {
			if (AssetsHelpers::isAbsoluteUrl($css)) {
				yield $css;
			} else {
				yield ($basePath ? $this->basePath : $this->baseUrl) . $this->timestamp($css);
			}
		}
	}

	public function getJsByMinified(string $file, bool $basePath = true): iterable {
		if (!isset($this->assets['js'][$file])) {
			throw new AssetsException("Minified assets '$file' not exists.");
		}

		foreach ($this->assets['js'][$file] as $js) {
			if (AssetsHelpers::isAbsoluteUrl($js)) {
				yield $js;
			} else {
				yield ($basePath ? $this->basePath : $this->baseUrl) . $this->timestamp($js);
			}
		}
	}

}
