<?php

namespace WebChemistry\Assets;

use Nette\Http\IRequest;
use Nette\Utils\Html;
use Nette\Utils\Strings;

class AssetsManager {

	/** @var array */
	private static $allowedOptions = ['defer' => TRUE, 'async' => TRUE, 'ifMinified' => TRUE, 'ifNotMinified' => TRUE];

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

	public function parse(string $name): string {
		$args = array_slice(func_get_args(), 1);
		if (Strings::endsWith($name, '.css')) {
			return $this->getCss($name);
		} else if (Strings::endsWith($name, '.js')) {
			foreach ($args as $i => $option) {
				if (!isset(self::$allowedOptions[$option])) {
					throw new AssetsException("Option '$option' is not allowed.");
				}
				if ($option === 'ifMinified') {
					if (!$this->minify) {
						return '';
					}
					unset($args[$i]);
				} else if ($option === 'ifNotMinified') {
					if ($this->minify) {
						return '';
					}
					unset($args[$i]);
				}
			}
			return $this->getJs($name, $args);
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
	 * @param array $options
	 * @throws AssetsException
	 * @return Html
	 */
	public function getJs(string $minified, array $options = []): Html {
		if (!isset($this->assets['js'][$minified])) {
			throw new AssetsException("Minified assets '$minified' not exists.");
		}

		if ($this->minify) {
			$options = $options ? ' ' . implode(' ', $options) : '';
			$container = "<script src=\"" . ($this->basePath . $minified) ."\"$options></script>\n";
		} else {
			$container = "<!-- Minified: " . ($this->basePath . $minified) . " -->\n";
			foreach ($this->assets['js'][$minified] as $file) {
				$container .= "<script src=\"" . ($this->basePath . $file) . "\"></script>\n";
			}
		}

		return Html::el()->setHtml($container);
	}

}
