<?php

namespace WebChemistry\Assets;

use Nette\Application\UI\ITemplate;

trait TPresenter {

	/** @var AssetsManager */
	private $assetsManager;

	/**
	 * @param AssetsManager $assetsManger
	 */
	public function injectAssetsManager(AssetsManager $assetsManger) {
		$this->assetsManager = $assetsManger;
	}

	/**
	 * @param ITemplate $template
	 * @return ITemplate
	 */
	public function createTemplate($template = NULL) {
		$template = $template ? : parent::createTemplate();
		$template->assets = $this->assetsManager;

		return $template;
	}

}
