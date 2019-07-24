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
    if(!wfFilesystem::fileExist(wfGlobals::getAppDir().$data->get('data/filename'))){
      throw new Exception("PluginThemeVersion.widget_history says: File ".$data->get('data/filename')." does not exist.");
    }
    $yml = new PluginWfYml($data->get('data/filename'));
    $history = new PluginWfArray($yml->get('history'));
    if($history->get()){
      /**
       * Data fix.
       */
      wfPlugin::includeonce('readme/parser');
      $parser = new PluginReadmeParser();
      foreach ($history->get() as $key => $value) {
        $item = new PluginWfArray($value);
        if(wfUser::hasRole('webmaster') && $item->get('webmaster')){
          $history->set("$key/webmaster_enabled", true);
        }else{
          $history->set("$key/webmaster_enabled", false);
        }
        $history->set("$key/version", $key);
        /**
         * Replace € with #.
         */
        $item->set("description", str_replace("€", '#', $item->get('description')) );
        $item->set("webmaster", str_replace("€", '#', $item->get('webmaster')) );
        /**
         * 
         */
        $history->set("$key/description", $parser->parse_text($item->get('description')) );
        $history->set("$key/webmaster", $parser->parse_text($item->get('webmaster')) );
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
    }else{
      /**
       * If no data.
       */
      $element = new pluginwfyml('/plugin/theme/version/element/history_missing.yml');
      $element->setByTag($data->get('data'));
      wfDocument::renderElement($element->get());
    }
  }
}
