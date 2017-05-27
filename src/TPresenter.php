<?php

declare(strict_types=1);

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
	public function createTemplate(?ITemplate $template = NULL): ITemplate {
		$template = $template ? : parent::createTemplate();
		$template->assetsManager = $this->assetsManager;

		return $template;
	}

}
