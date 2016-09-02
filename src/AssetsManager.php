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

	/**
	 * @param array $assets
	 * @param bool $minify
	 * @param Request $request
	 */
	public function __construct(array $assets, $minify, Request $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
		$this->minify = $minify;
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

		$container = "<!-- Minified: " . ($this->basePath . $minified) ." -->\n";
		foreach ($this->assets['css'][$minified] as $file) {
			$container .= "<link rel=\"stylesheet\" href=\"" . ($this->basePath . $file) . "\">\n";
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

		$options = $this->minify ? ' ' . implode(' ', $options) : '';
		$container = "<!-- Minified: " . ($this->basePath . $minified) ." -->\n";
		foreach ($this->assets['js'][$minified] as $file) {
			$container .= "<script src=\"" . ($this->basePath . $file) ."\"$options></script>\n";
		}

		return Html::el()->setHtml($container);
	}

}
