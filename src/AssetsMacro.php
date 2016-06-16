<?php

namespace WebChemistry\Assets;

use Latte\Compiler;
use Latte\IMacro;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Strings;

class AssetsMacro extends MacroSet {

	public static function install(Compiler $compiler) {
		$me = new static($compiler);

		$me->addMacro('assets', [$me, 'assetsMacro']);
	}

	public function assetsMacro(MacroNode $node, PhpWriter $writer) {
		if (Strings::endsWith($node->args, '.js')) {
			return $writer->write('echo $assets->getJs(%word);', $node->args);
		}
		if (Strings::endsWith($node->args, '.css')) {
			return $writer->write('echo $assets->getCss(%word);', $node->args);
		}

		throw new \Exception("Assets must ends with .js or .css, '{$node->args}' given.");
	}

}
