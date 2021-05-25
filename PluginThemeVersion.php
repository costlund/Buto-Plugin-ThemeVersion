<?php
class PluginThemeVersion{
  private $data = null;
  private $has_mysql = false;
  private $mysql = null;
  private $builder = null;
  function __construct() {
    $this->data = wfPlugin::getPluginSettings('theme/version', true);
    wfPlugin::includeonce('mysql/builder');
    $this->builder = new PluginMysqlBuilder();
    $this->builder->set_schema_file('/plugin/wf/account2/mysql/schema.yml');
    $this->builder->set_table_name('account_role');
    if($this->data->get('data/mysql')){
      $this->has_mysql = true;
    }
    if($this->has_mysql){
      wfPlugin::includeonce('wf/mysql');
      $this->mysql = new PluginWfMysql();
      $this->mysql->open($this->data->get('data/mysql'));
    }
    wfPlugin::includeonce('wf/array');
    wfPlugin::includeonce('wf/yml');
    wfPlugin::enable('wf/table');
  }
  private function db_account_role_tester(){
    if(!$this->has_mysql){
      return array();
    }
    $criteria = new PluginWfArray();
    $criteria->set('select_filter/0', 'account.email');
    $criteria->set('join/0/field', 'account_id');
    $criteria->set('where/account_role.role/value', 'tester');
    $sql = $this->builder->get_sql_select($criteria->get());
    $this->mysql->execute($sql);
    return $this->mysql->getMany($sql);
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
        $history[] = array('name' => wfGlobals::getVersion(), 'version' => $key, 'date' => $i2->get('date'), 'description' => $this->replace_line_break($i2->get('description')), 'type' => 'system', 'title' => $i2->get('title'), 'webmaster' => $this->replace_line_break($i2->get('webmaster')));
      }
    }
    /**
     * Theme
     */
    $theme_manifest = new PluginWfYml(wfGlobals::getAppDir().'/theme/'.wfGlobals::getTheme().'/config/manifest.yml');
    if($theme_manifest->get('history')){
      foreach ($theme_manifest->get('history') as $key => $value) {
        $i2 = new PluginWfArray($value);
        $history[] = array('name' => wfGlobals::getTheme(), 'version' => $key, 'date' => $i2->get('date'), 'description' => $this->replace_line_break($i2->get('description')), 'type' => 'theme', 'title' => $i2->get('title'), 'webmaster' => $this->replace_line_break($i2->get('webmaster')));
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
          $history[] = array('name' => $i->get('name'), 'version' => $key2, 'date' => $i2->get('date'), 'description' => $this->replace_line_break($i2->get('description')), 'type' => 'plugin', 'title' => $i2->get('title'), 'webmaster' => $this->replace_line_break($i2->get('webmaster')));
        }
      }
    }
    /**
     * 
     */
    return $history;
  }
  private function replace_line_break($v){
    return str_replace("\n", '<br>', $v);
  }
  public function widget_history_all($data){
    $history = $this->getHistoryAll();
    $widget = new PluginWfYml(__DIR__.'/element/history_all.yml');
    /**
     * 
     */
    if(wfUser::hasRole('webmaster')){
      $widget->set('0/data/data/field/webmaster', 'Webmaster');
    }
    /**
     * 
     */
    $widget->setByTag(array('data' => $history));
    wfDocument::renderElement($widget->get());
  }
  public function widget_history($data){
    $history = $this->getHistory($data);
    if($history->get('item')){
      /**
       * 
       */
      foreach($history->get('item') as $k => $v){
        $i = new PluginWfArray($v);
        /**
         * Rename param settings to row_settings according to PluginWfTable.
         */
        $history->set("item/$k/row_settings", $i->get('settings'));
        $history->setUnset("item/$k/settings");
        /**
         * Add role webmaster if row_settings/role/item is set.
         */
        if($history->get("item/$k/row_settings/role/item")){
          /**
           * Add string with roles to webmaster param.
           */
          $roles = '';
          foreach($history->get("item/$k/row_settings/role/item") as $v){
            $roles .= ", $v";
          }
          $roles = substr($roles, 2);
          $roles = "($roles)";
          $history->set("item/$k/webmaster", $history->get("item/$k/webmaster").' '.$roles);
          /**
           * Add role webmaster.
           */
          $history->set("item/$k/row_settings/role/item/", 'webmaster');
        }
        /**
         * Add webmaster text to description if user has role webmaster.
         */
        if(wfUser::hasRole('webmaster') && $history->get("item/$k/webmaster")){
          $history->set("item/$k/description", $history->get("item/$k/description").' Webmaster: '.$history->get("item/$k/webmaster"));
        }
      }
      /**
       * 
       */
      $widget = new PluginWfYml(__DIR__.'/element/history.yml');
      $application = array('title' => wfGlobals::get('settings/application/title'), 'host' => wfServer::getHttpHost());
      $tester = $this->db_account_role_tester();
      $test_users = '';
      foreach($tester as $v){
        $i = new PluginWfArray($v);
        $test_users .= ','.$i->get('account.email');
      }
      $test_users = substr($test_users, 1);
      $widget->setByTag(array('test_users' => $test_users));
      $widget->setByTag(array('application_data' => "if(typeof PluginThemeVersion=='object'){     PluginThemeVersion.data.application=".json_encode($application).";  PluginThemeVersion.data.tester=".json_encode($tester).";        }"), 'script');
      /**
       * 
       */
      $widget->setByTag(array('data' => $history->get('item')));
      wfDocument::renderElement($widget->get());
    }else{
      /**
       * If no data.
       */
      $data = new PluginWfArray($data);
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
  public function widget_include(){
    wfDocument::renderElementFromFolder(__DIR__, __FUNCTION__);
  }
}
