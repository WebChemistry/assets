<?php

namespace WebChemistry\Assets;

use Nette\Http\Request;
use Nette\Utils\Html;

class AssetsManager {

	/** @var array */
	private $assets;

	/** @var string */
	private $basePath;

	/** @var bool */
	private $minify;

	/** @var int */
	private $timestamp;

	/**
	 * @param array $assets
	 * @param bool $minify
	 * @param int $timestamp
	 * @param Request $request
	 */
	public function __construct(array $assets, $minify, $timestamp, Request $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
		$this->minify = $minify;
		$this->timestamp = '?t=' . $timestamp;
	}

	/**
	 * @param string $minified
	 * @throws AssetsException
	 * @return Html
	 */
	public function getCss($minified) {
		if (!isset($this->assets['css'][$minified])) {
			throw new AssetsException("Index '$minified' not exists.");
		}

		if ($this->minify) {
			$file = current($this->assets['css'][$minified]);
			$container = "<link rel=\"stylesheet\" href=\"" . ($this->basePath . $file) . $this->timestamp . "\">\n";
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
	 * @param array $options
	 * @throws AssetsException
	 * @return Html
	 */
	public function getJs($minified, array $options = []) {
		if (!isset($this->assets['js'][$minified])) {
			throw new AssetsException("Index '$minified' not exists.");
		}

		if ($this->minify) {
			$options = ' ' . implode(' ', $options);
			$file = current($this->assets['js'][$minified]);
			$container = "<script src=\"" . ($this->basePath . $file) . $this->timestamp ."\"$options></script>\n";
		} else {
			$container = "<!-- Minified: " . ($this->basePath . $minified) ." -->\n";
			foreach ($this->assets['js'][$minified] as $file) {
				$container .= "<script src=\"" . ($this->basePath . $file) ."\"$options></script>\n";
			}
		}

		return Html::el()->setHtml($container);
	}

}
