<?php

/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
Yii::setAlias('@webroot', __DIR__ . '/../web');
Yii::setAlias('@web', '/assets/');

return [
  // Adjust command/callback for JavaScript files compressing:
  'jsCompressor' => 'java -jar compiler.jar --js {from} --js_output_file {to}',
  // Adjust command/callback for CSS files compressing:
  'cssCompressor' => 'java -jar yuicompressor.jar --type css {from} -o {to}',
  // Whether to delete asset source after compression:
  'deleteSource' => false,
  // The list of asset bundles to compress:
  'bundles' => [
    'app\assets\AppAsset',  // AppAsset bundle
    'yii\web\YiiAsset',     // Yii core assets
    'yii\bootstrap5\BootstrapAsset', // Bootstrap CSS
    'yii\bootstrap5\BootstrapPluginAsset', // Bootstrap JS plugins
    'yii\bootstrap5\BootstrapIconAsset', // Bootstrap Icons
  ],
  // Asset bundle for compression output:
  'targets' => [
    'all' => [
      'class' => 'yii\web\AssetBundle',
      'basePath' => '@webroot/assets',
      'baseUrl' => '@web/assets',
      'js' => 'js/all-{hash}.js',
      'css' => 'css/all-{hash}.css',
    ],
  ],
  // Asset manager configuration:
  'assetManager' => [
    'basePath' => '@webroot/assets',
    'baseUrl' => '@web/assets',
  ],
];
