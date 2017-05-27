<?php

declare(strict_types=1);

namespace WebChemistry\Assets;

use Latte\Engine;
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

		/** @var Engine $latte */
		$latte = $template->getLatte();
		$latte->addProvider('assetsManager', $this->assetsManager);

		return $template;
	}

}
