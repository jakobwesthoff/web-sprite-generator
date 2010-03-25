<?php
$namespace = "org\\westhoffswelt\\wsgen";

return array(  
    $namespace . '\\DefinitionReader'                       => 'classes/definition_reader.php',
    $namespace . '\\DefinitionReader\\BasicArray'           => 'classes/definition_reader/basic_array.php',
    $namespace . '\\DefinitionReader\\Directory'            => 'classes/definition_reader/directory.php',
    $namespace . '\\DefinitionReader\\CssLike'              => 'classes/definition_reader/css_like.php',
    $namespace . '\\DefinitionReader\\CssLike\\Token'       => 'classes/definition_reader/css_like/token.php',
    $namespace . '\\DefinitionReader\\CssLike\\Tokenizer'   => 'classes/definition_reader/css_like/tokenizer.php',
    $namespace . '\\DefinitionReader\\CssLike\\TokenFilter' => 'classes/definition_reader/css_like/token_filter.php',
    $namespace . '\\DefinitionReader\\CssLike\\Parser'      => 'classes/definition_reader/css_like/parser.php',
    $namespace . '\\Renderer'                               => 'classes/renderer.php',
    $namespace . '\\Renderer\\GD'                           => 'classes/renderer/gd.php',
    $namespace . '\\MetaImage'                              => 'classes/meta_image.php',
    $namespace . '\\MetaImage\\GD'                          => 'classes/meta_image/gd.php',
    $namespace . '\\LayoutManager'                          => 'classes/layout_manager.php',
    $namespace . '\\LayoutManager\\Vertical'                => 'classes/layout_manager/vertical.php',
    $namespace . '\\DefinitionWriter'                       => 'classes/definition_writer.php',
    $namespace . '\\DefinitionWriter\\CssBackground'        => 'classes/definition_writer/css_background.php',
    $namespace . '\\Logger'                                 => 'classes/logger.php',
    $namespace . '\\Logger\\Console'                        => 'classes/logger/console.php',
    $namespace . '\\Application'                            => 'classes/application.php',
    $namespace . '\\LayoutManager\\ColorGroup'              => 'classes/layout_manager/color_group.php',
    $namespace . '\\LayoutManager\\ColorGroup\\Classifier'  => 'classes/layout_manager/color_group/classifier.php',
);
