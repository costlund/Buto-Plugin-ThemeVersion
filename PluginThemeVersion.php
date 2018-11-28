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
    $data = new PluginWfYml($data->get('data/filename'));
    $history = new PluginWfArray($data->get('history'));
    /**
     * Data fix.
     */
    foreach ($history->get() as $key => $value) {
      $item = new PluginWfArray($value);
      if(wfUser::hasRole('webmaster') && $item->get('webmaster')){
        $history->set("$key/webmaster_enabled", true);
      }else{
        $history->set("$key/webmaster_enabled", false);
      }
      $history->set("$key/version", $key);
    }
    /**
     * New key to sort on.
     */
    $temp = array();
    foreach ($history->get() as $key => $value) {
      $a = preg_split('/[.]/', $key);
      $new_key = null;
      foreach ($a as $value2) {
        $new_key .= ($value2+1000);
      }
      if(sizeof($a)==2){
        $new_key .= 1000;
      }
      $temp[$new_key] = $value;
    }
    krsort($temp);
    $history = new PluginWfArray($temp);
    /**
     * Render elements.
     */
    foreach ($history->get() as $key => $value){
      $item = new PluginWfArray($value);
      $element = new pluginwfyml('/plugin/theme/version/element/history_item.yml');
      $element->setByTag($item->get());
      wfDocument::renderElement($element->get());
    }
  }
}
