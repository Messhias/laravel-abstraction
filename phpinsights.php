<?php
/*
 * Copyright (c) 2022.
 *
 * Fabio William Conceição <messhias@gmail.com>
 */

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DiscourageGotoSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\Operators\DisallowEqualOperatorsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;

return [
	/*
	|--------------------------------------------------------------------------
	| Default Preset
	|--------------------------------------------------------------------------
	|
	| This option controls the default preset that will be used by PHP Insights
	| to make your code reliable, simple, and clean. However, you can always
	| adjust the `Metrics` and `Insights` below in this configuration file.
	|
	| Supported: "default", "laravel", "symfony", "magento2", "drupal"
	|
	*/

	'preset' => 'laravel',

	/*
	|--------------------------------------------------------------------------
	| IDE
	|--------------------------------------------------------------------------
	|
	| This options allow to add hyperlinks in your terminal to quickly open
	| files in your favorite IDE while browsing your PhpInsights report.
	|
	| Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
	| "atom", "vscode".
	|
	| If you have another IDE that is not in this list but which provide an
	| url-handler, you could fill this config with a pattern like this:
	|
	| myide://open?url=file://%f&line=%l
	|
	*/

	'ide' => 'phpstorm',

	/*
	|--------------------------------------------------------------------------
	| Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may adjust all the various `Insights` that will be used by PHP
	| Insights. You can either add, remove or configure `Insights`. Keep in
	| mind, that all added `Insights` must belong to a specific `Metric`.
	|
	*/

	'exclude' => [
		//  'path/to/directory-or-file',
		'vendor',
	],

	'add' => [
		//  ExampleMetric::class => [
		//      ExampleInsight::class,
		//  ]
	],

	'remove' => [
		//  ExampleInsight::class,
		SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
		ForbiddenSetterSniff::class,
		ModernClassNameReferenceSniff::class,
		SwitchDeclarationSniff::class,
		ArrayIndentSniff::class,
		DiscourageGotoSniff::class,
		DisallowEmptySniff::class,
		DisallowEqualOperatorsSniff::class,
		UnnecessaryStringConcatSniff::class,
		ExplicitStringVariableFixer::class,
		DisallowArrayTypeHintSyntaxSniff::class,
		DisallowMixedTypeHintSniff::class,
		ParameterTypeHintSniff::class,
		PropertyTypeHintSniff::class,
		ReturnTypeHintSniff::class,
		UselessFunctionDocCommentSniff::class,
		UselessInheritDocCommentSniff::class,
		ForbiddenDefineFunctions::class,
		UnusedParameterSniff::class,
		MethodArgumentSpaceFixer::class,
		ForbiddenTraits::class,
		LineLengthSniff::class,
		DisallowTabIndentSniff::class,
		ParameterTypeHintSpacingSniff::class,
	],

	'config' => [
		//  ExampleInsight::class => [
		//      'key' => 'value',
		//  ],

		SwitchDeclarationSniff::class => [
			'indent' => "\t",
		],

		LineLengthSniff::class => [
			'exclude' => [
				'phpinsights.php',
				'rector.php',
			],
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| Here you may define a level you want to reach per `Insights` category.
	| When a score is lower than the minimum level defined, then an error
	| code will be returned. This is optional and individually defined.
	|
	*/

	'requirements' => [
		'min-quality' => 90,
		'min-complexity' => 0,
		'min-architecture' => 90,
		'min-style' => 90,
//        'disable-security-check' => false,
	],

	/*
	|--------------------------------------------------------------------------
	| Threads
	|--------------------------------------------------------------------------
	|
	| Here you may adjust how many threads (core) PHPInsights can use to perform
	| the analyse. This is optional, don't provide it and the tool will guess
	| the max core number available. This accept null value or integer > 0.
	|
	*/

	'threads' => null,

];
