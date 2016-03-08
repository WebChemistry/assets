<?php

namespace WebChemistry\Assets;

use Nette\Application\UI\ITemplate;

trait TPresenter {

	/** @var Manager */
	private $assetsManager;

	/**
	 * @param Manager $assetsManger
	 */
	public function injectAssetsManager(Manager $assetsManger) {
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
