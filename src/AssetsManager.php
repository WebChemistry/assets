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

	public function __construct(array $assets, bool $minify, IRequest $request) {
		$this->assets = $assets;
		$this->basePath = $request->getUrl()->getBasePath();
		$this->minify = $minify;
	}

	/**
	 * @param string $name
	 * @return string
	 * @throws AssetsException
	 */
	public function parse(string $name): string {
		if (Strings::endsWith($name, '.css')) {
			return $this->getCss($name);

		} else if (Strings::endsWith($name, '.js')) {
			return $this->getJs($name);

		} else {
			throw new AssetsException("Assets must ends with .js or .css, '$name' given.");
		}
	}

	/**
	 * @param string $minified
	 * @throws AssetsException
	 * @return Html
	 */
	public function getCss(string $minified): Html {
		if (!isset($this->assets['css'][$minified])) {
			throw new AssetsException("Minified assets '$minified' not exists.");
		}

		if ($this->minify) {
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
	public function getJs(string $minified): Html {
		if (!isset($this->assets['js'][$minified])) {
			throw new AssetsException("Minified assets '$minified' not exists.");
		}

		if ($this->minify) {
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
