<?php declare(strict_types = 1);

namespace WebChemistry\Assets;

final class AssetsHelpers {

	public static function isAbsoluteUrl(string $url): bool {
		return mb_substr($url, 0, 5) === 'http:' || mb_substr($url, 0, 6) === 'https:';
	}

}
