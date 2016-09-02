<?php

namespace WebChemistry\Assets;

use Nette\Http\Request;
use Nette\Utils\Html;

class AssetsManager {

	/** @var array */
	private $assets;

	/** @var string */
	private $basePath;

	/**
	 * @param array $assets
	 * @param Request $request
	 */
	public function __construct(array $assets, Request $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
	}

	/**
	 * @param $minified
	 * @return Html
	 * @throws \Exception
	 */
	public function getCss($minified) {
		if (!isset($this->assets['css'][$minified])) {
			throw new \Exception("Index '$minified' not exists.");
		}

		$container = "<!-- Minified: " . ($this->basePath . $minified) ." -->\n";
		foreach ($this->assets['css'][$minified] as $file) {
			$container .= "<link rel=\"stylesheet\" href=\"" . ($this->basePath . $file) . "\">\n";
		}

		return Html::el()->setHtml($container);
	}

	/**
	 * @param $minified
	 * @return Html
	 * @throws \Exception
	 */
	public function getJs($minified) {
		if (!isset($this->assets['js'][$minified])) {
			throw new \Exception("Index '$minified' not exists.");
		}

		$container = "<!-- Minified: " . ($this->basePath . $minified) ." -->\n";
		foreach ($this->assets['js'][$minified] as $file) {
			$container .= "<script src=\"" . ($this->basePath . $file) ."\"></script>\n";
		}

		return Html::el()->setHtml($container);
	}

}
