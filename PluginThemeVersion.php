<?php
/**
 * Plugin to show software history registrated in yml file.
 * Check example /data/example.yml.
 */
class PluginThemeVersion{
  function __construct() {
    wfPlugin::includeonce('wf/array');
    wfPlugin::includeonce('wf/yml');
  }
  public function widget_history($data){
    $data = new PluginWfArray($data);
    $version = new pluginwfyml($data->get('data/filename'));
    $version = $version->get('version');
    krsort($version);
    foreach ($version as $key => $value) {
      $item = new PluginWfArray($value);
      $item->set('version', $key);
      $element = new pluginwfyml('/plugin/theme/version/element/history_item.yml');
      $element->setByTag($item->get());
      wfDocument::renderElement($element->get());
    }
  }
}
