<?php

namespace WebChemistry\Assets;

use Nette\Http\Request;
use Nette\Utils\Html;

class Manager {

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
	 * @param string $minified
	 * @return Html|null
	 */
	public function getCss($minified) {
		if (!isset($this->assets['css'][$minified])) {
			return NULL;
		}

		$container = Html::el();
		foreach ($this->assets['css'][$minified] as $file) {
			$container->add(
				Html::el('link')->rel('stylesheet')->href($this->basePath . $file)
			);
		}

		return $container;
	}

	/**
	 * @param string $minified
	 * @return Html|null
	 */
	public function getJs($minified) {
		if (!isset($this->assets['js'][$minified])) {
			return NULL;
		}

		$container = Html::el();
		foreach ($this->assets['js'][$minified] as $file) {
			$container->add(
				Html::el('script')->src($this->basePath . $file)
			);
		}

		return $container;
	}

}
