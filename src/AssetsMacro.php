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
		return $writer->write('echo $assets->parse(%node.args);');
	}

}
