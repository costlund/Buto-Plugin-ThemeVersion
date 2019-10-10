<?php
/**
 * Plugin to show software history registrated in yml file.
 * Check example /data/example.yml.
 */
class PluginThemeVersion{
  function __construct() {
    wfPlugin::includeonce('wf/array');
    wfPlugin::includeonce('wf/yml');
    wfPlugin::enable('wf/table');
  }
  private function getHistoryAll(){
    $history = array();
    /**
     * Sys
     */
    $sys_manifest = new PluginWfYml(wfGlobals::getSysDir().'/manifest.yml');
    if($sys_manifest->get('history')){
      foreach ($sys_manifest->get('history') as $key => $value) {
        $i2 = new PluginWfArray($value);
        $history[] = array('name' => wfGlobals::getVersion(), 'version' => $key, 'date' => $i2->get('date'), 'description' => $i2->get('description'), 'type' => 'system', 'title' => $i2->get('title'));
      }
    }
    /**
     * Theme
     */
    $theme_manifest = new PluginWfYml(wfGlobals::getAppDir().'/theme/'.wfGlobals::getTheme().'/config/manifest.yml');
    if($theme_manifest->get('history')){
      foreach ($theme_manifest->get('history') as $key => $value) {
        $i2 = new PluginWfArray($value);
        $history[] = array('name' => wfGlobals::getTheme(), 'version' => $key, 'date' => $i2->get('date'), 'description' => $i2->get('description'), 'type' => 'theme', 'title' => $i2->get('title'));
      }
    }
    /**
     * Plugin
     */
    wfPlugin::includeonce('plugin/analysis');
    $plugin_analysis = new PluginPluginAnalysis();
    wfRequest::set('theme', wfGlobals::getTheme());
    $plugin_analysis->setPlugins();
    foreach ($plugin_analysis->plugins->get() as $key => $value) {
      $i = new PluginWfArray($value);
      if($i->get('manifest/history')){
        foreach ($i->get('manifest/history') as $key2 => $value2) {
          $i2 = new PluginWfArray($value2);
          $history[] = array('name' => $i->get('name'), 'version' => $key2, 'date' => $i2->get('date'), 'description' => $i2->get('description'), 'type' => 'plugin', 'title' => $i2->get('title'));
        }
      }
    }
    /**
     * 
     */
    return $history;
  }
  public function widget_history_all($data){
    $history = $this->getHistoryAll();
    $widget = new PluginWfYml(__DIR__.'/element/history_all.yml');
    $widget->setByTag(array('data' => $history));
    wfDocument::renderElement($widget->get());
  }
  public function widget_history($data){
    $history = $this->getHistory($data);
    $data = new PluginWfArray($data);
    if($history->get('item')){
      /**
       * Render elements.
       */
      foreach ($history->get('item') as $key => $value){
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
  public function widget_version($data){
    $history = $this->getHistory($data);
    $element = array();
    $element[] = wfDocument::createHtmlElement('text', $history->get('version'));
    wfDocument::renderElement($element);
  }
  private function getHistory($data){
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
      /**
       * Current version.
       */
      $version = null;
      foreach ($temp as $key => $value) {
        $version = $value['version'];
        break;
      }
      /**
       * 
       */
      $history = new PluginWfArray(array('item' => $temp, 'version' => $version));
    }
    return $history;
  }
}
