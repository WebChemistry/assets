<?php

namespace WebChemistry\Assets;

use Nette\Http\IRequest;
use Nette\Utils\Html;
use Nette\Utils\Strings;

class AssetsManager {

	/** @var AssetsWrapper */
	private $wrapper;

	public function __construct(AssetsWrapper $wrapper) {
		$this->wrapper = $wrapper;
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
		$container = '';
		foreach ($this->wrapper->getCssByMinified($minified) as $css) {
			$container .= "<link rel=\"stylesheet\" href=\"" . $css . "\">\n";
		}

		return Html::el()->setHtml($container);
	}

	/**
	 * @param string $minified
	 * @throws AssetsException
	 * @return Html
	 */
	public function getJs(string $minified): Html {
		$container = '';
		foreach ($this->wrapper->getJsByMinified($minified) as $js) {
			$container .= "<script src=\"" . $js . "\"></script>\n";
		}

		return Html::el()->setHtml($container);
	}

}
