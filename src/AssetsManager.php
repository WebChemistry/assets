<?php

namespace WebChemistry\Assets;

use Nette\Http\IRequest;
use Nette\Utils\Html;
use Nette\Utils\Strings;

class AssetsManager {

	/** @var array */
	private $assets;

	/** @var string */
	private $basePath;

	/** @var bool */
	private $minify;

	/** @var string */
	private $wwwDir;

	public function __construct(string $wwwDir, array $assets, bool $minify, IRequest $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
		$this->minify = $minify;
		$this->wwwDir = $wwwDir;
	}

	/**
	 * @param string $name
	 * @return string
	 * @throws AssetsException
	 */
	public function parse(string $name, bool $timestamp = false): string {
		if (Strings::endsWith($name, '.css')) {
			return $this->getCss($name, $timestamp);

		} else if (Strings::endsWith($name, '.js')) {
			return $this->getJs($name, $timestamp);

		} else {
			throw new AssetsException("Assets must ends with .js or .css, '$name' given.");
		}
	}

	/**
	 * @param string $minified
	 * @throws AssetsException
	 * @return Html
	 */
	public function getCss(string $minified, bool $timestamp = false): Html {
		if (!isset($this->assets['css'][$minified])) {
			throw new AssetsException("Minified assets '$minified' not exists.");
		}

		if ($this->minify) {
			if ($timestamp) {
				$timestamp = filemtime($this->wwwDir . '/' . $minified);
				$minified = $minified . '?t=' . $timestamp;
			}

			$container = "<link rel=\"stylesheet\" href=\"" . ($this->basePath . $minified) . "\">\n";
		} else {
			$container = "<!-- Minified: " . ($this->basePath . $minified) . " -->\n";
			foreach ($this->assets['css'][$minified] as $file) {
				$container .= "<link rel=\"stylesheet\" href=\"" . ($this->basePath . $file) . "\">\n";
			}
		}

		return Html::el()->setHtml($container);
	}

	/**
	 * @param string $minified
	 * @throws AssetsException
	 * @return Html
	 */
	public function getJs(string $minified, bool $timestamp = false): Html {
		if (!isset($this->assets['js'][$minified])) {
			throw new AssetsException("Minified assets '$minified' not exists.");
		}

		if ($this->minify) {
			if ($timestamp) {
				$timestamp = filemtime($this->wwwDir . '/' . $minified);
				$minified = $minified . '?t=' . $timestamp;
			}

			$container = "<script src=\"" . ($this->basePath . $minified) ."\"></script>\n";

		} else {
			$container = "<!-- Minified: " . ($this->basePath . $minified) . " -->\n";
			foreach ($this->assets['js'][$minified] as $file) {
				$container .= "<script src=\"" . ($this->basePath . $file) . "\"></script>\n";
			}
		}

		return Html::el()->setHtml($container);
	}

}
