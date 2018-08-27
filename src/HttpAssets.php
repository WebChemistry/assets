<?php declare(strict_types = 1);

namespace WebChemistry\Assets;

use Nette\Http\IResponse;

class HttpAssets {

	/** @var AssetsWrapper */
	private $wrapper;

	/** @var IResponse */
	private $response;

	public function __construct(AssetsWrapper $wrapper, IResponse $response) {
		$this->wrapper = $wrapper;
		$this->response = $response;
	}

	public function createHttpLinks(): void {
		foreach ($this->wrapper->getCssByModule('front') as $asset) {
			$this->response->addHeader('Link', "<$asset>; rel=preload; as=style");
		}

		foreach ($this->wrapper->getJsByModule('front') as $asset) {
			$this->response->addHeader('Link', "<$asset>; rel=preload; as=script");
		}
	}

}
