<?php

namespace WebChemistry\Assets;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class AssetsMacro extends MacroSet {

	public static function install(Compiler $compiler): void {
		$me = new static($compiler);

		$me->addMacro('assets', [$me, 'assetsMacro']);
	}

	public function assetsMacro(MacroNode $node, PhpWriter $writer): string {
		return $writer->write('echo $assets->parse(%node.args);');
	}

}
