<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('vendor')
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$fixers = [
    '@PSR2'                                      => true,
    'single_quote'                               => true,
    'no_unused_imports'                          => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_empty_statement'                         => true,
    'no_extra_blank_lines'                       => true,
    'no_blank_lines_after_phpdoc'                => true,
    'no_empty_phpdoc'                            => true,
    'phpdoc_indent'                              => true,
    'no_blank_lines_after_class_opening'         => true,
    'include'                                    => true,
    'no_trailing_comma_in_list_call'             => true,
    'no_leading_namespace_whitespace'            => true,
    'standardize_not_equals'                     => true,
    'blank_line_after_opening_tag'               => true,
    'indentation_type'                           => true,
    'concat_space'                               => [
        'spacing' => 'one',
    ],
    'space_after_semicolon' => [
        'remove_in_empty_for_expressions' => true,
    ],
    'binary_operator_spaces'          => ['default' => 'align_single_space_minimal'],
    'whitespace_after_comma_in_array' => true,
    'array_syntax'                    => ['syntax' => 'short'],
    'ternary_operator_spaces'         => true,
    'yoda_style'                      => true,
    'normalize_index_brace'           => true,
    'short_scalar_cast'               => true,
    'function_typehint_space'         => true,
    'function_declaration'            => true,
    'return_type_declaration'         => true

];
$config = new \PhpCsFixer\Config();
return $config
    ->setRules($fixers)
    ->setFinder($finder)
    ->setUsingCache(false);
