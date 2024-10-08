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
    $this->builder->set_schema_file('/plugin/wf/account2/mysql/schema.yml');
    $this->builder->set_table_name('account_role');
    $criteria = new PluginWfArray();
    $criteria->set('select_filter/0', 'account.email');
    $criteria->set('join/0/field', 'account_id');
    $criteria->set('where/account_role.role/value', 'tester');
    $sql = $this->builder->get_sql_select($criteria->get());
    $this->mysql->execute($sql);
    return $this->mysql->getMany($sql);
  }
  private function db_theme_version_user_all_working(){
    if(!$this->has_mysql){
      return new PluginWfArray();
    }
    $rs = $this->mysql->runSql("select theme_version_user.version, group_concat(concat(account.email, '(', theme_version_user.created_at, ')')) as users from theme_version_user inner join account on theme_version_user.created_by=account.id where theme_version_user.response='Working' group by theme_version_user.version", 'version');
    $rs = new PluginWfArray($rs['data']);
    return $rs;
  }
  private function db_theme_version_user_all_issue(){
    if(!$this->has_mysql){
      return new PluginWfArray();
    }
    $rs = $this->mysql->runSql("select theme_version_user.version, group_concat(concat(account.email, '(', theme_version_user.response, ', ', theme_version_user.created_at, ')') separator ', ') as users from theme_version_user inner join account on theme_version_user.created_by=account.id where theme_version_user.response<>'Working' group by theme_version_user.version", 'version');
    $rs = new PluginWfArray($rs['data']);
    return $rs;
  }
  private function db_theme_version_user_responses(){
    if(!$this->has_mysql){
      return new PluginWfArray();
    }
    $created_by = wfUser::getSession()->get('user_id');
    $rs = $this->mysql->runSql("select theme_version_user.version, theme_version_user.response from theme_version_user where created_by='$created_by'", 'version');
    $rs = ($rs['data']);
    return $rs;
  }
  private function db_theme_version_user_one(){
    $created_by = wfUser::getSession()->get('user_id');
    $version = wfRequest::get('version');
    $rs = $this->mysql->runSql("select * from theme_version_user where created_by='$created_by' and version='$version'", null);
    if($rs['num_rows']){
      $rs = new PluginWfArray($rs['data'][0]);
    }else{
      $rs = new PluginWfArray();
    }
    return $rs;
  }
  private function db_theme_version_user_insert(){
    $created_by = wfUser::getSession()->get('user_id');
    $version = wfRequest::get('version');
    $id = wfCrypt::getUid();
    $this->mysql->runSql("insert into theme_version_user (id, created_by, version) values ('$id', '$created_by', '$version')");
    return null;
  }
  private function db_theme_version_user_update(){
    $created_by = wfUser::getSession()->get('user_id');
    $response = wfRequest::get('response');
    $version = wfRequest::get('version');
    $this->mysql->runSql("update theme_version_user set response='$response' where created_by='$created_by' and version='$version'");
    return null;
  }
  private function db_theme_version_user_delete(){
    $created_by = wfUser::getSession()->get('user_id');
    $version = wfRequest::get('version');
    $this->mysql->runSql("delete from theme_version_user where created_by='$created_by' and version='$version'");
    return null;
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
    return wfPhpfunc::str_replace("\n", '<br>', $v);
  }
  public function widget_history_all($data){
    $widget = new PluginWfYml(__DIR__.'/element/history_all.yml');
    wfDocument::renderElement($widget->get());
  }
  public function page_history_all(){
    /**
     * 
     */
    $plugin_data = wfPlugin::getPluginSettings('theme/version', true);
    $this->protect_by_role($plugin_data);
    /**
     * 
     */
    $history = $this->getHistoryAll();
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    exit($datatable->set_table_data($history));
  }
  public function widget_history($data){
    /**
     * 
     */
    $has_mysql = false;
    if($this->has_mysql){
      $has_mysql = true;
    }
    /**
     * 
     */
    $widget = new PluginWfYml(__DIR__.'/element/history.yml');
    $application = array('title' => wfGlobals::get('settings/application/title'), 'host' => wfServer::getHttpHost());
    $tester = $this->db_account_role_tester();
    $responses = $this->db_theme_version_user_responses();
    $test_users = '';
    foreach($tester as $v){
      $i = new PluginWfArray($v);
      $test_users .= ','.$i->get('account.email');
    }
    $test_users = wfPhpfunc::substr($test_users, 1);
    $widget->setByTag(array('test_users' => $test_users));
    $widget->setByTag(array('application_data' => "if(typeof PluginThemeVersion=='object'){     PluginThemeVersion.data.application=".json_encode($application).";  PluginThemeVersion.data.tester=".json_encode($tester).";   PluginThemeVersion.data.responses=".json_encode($responses).";   PluginThemeVersion.data.has_mysql=".json_encode($has_mysql).";     }"), 'script');
    /**
     * 
     */
    wfDocument::renderElement($widget->get());
  }
  private function protect_by_role($plugin_data){
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    $stop = false;
    if($plugin_data->get('data/settings/role/item')){
      $stop = true;
      foreach($plugin_data->get('data/settings/role/item') as $v){
        if(wfUser::hasRole($v)){
          $stop = false;
          break;
        }
      }
    }
    if($stop){
      exit($datatable->set_table_data(array()));
    }
    return null;
  }
  public function page_history(){
    /**
     * Including Datatable
     */    
    wfPlugin::includeonce('datatable/datatable_1_10_18');
    $datatable = new PluginDatatableDatatable_1_10_18();
    /**
     * Data
     */
    $plugin_data = wfPlugin::getPluginSettings('theme/version', true);
    /**
     * If role item is set and to item matching user role we render empty data.
     */
    $this->protect_by_role($plugin_data);
    /**
     * 
     */
    $history_data = $this->getHistory($plugin_data->get());
    $data = array();
    foreach($history_data->get('item') as $v){
      $data[] = $v;
    }
    /**
     * Render all data
     */    
    exit($datatable->set_table_data($data));
  }
  public function widget_version($data){
    $history = $this->getHistory($data);
    $element = array();
    $element[] = wfDocument::createHtmlElement('text', $history->get('version'));
    wfDocument::renderElement($element);
  }
  private function getHistory($data){
    $data = new PluginWfArray($data);
    if(!wfFilesystem::fileExist(wfGlobals::getAppDir().$data->get('data/history/filename'))){
      throw new Exception("PluginThemeVersion.widget_history says: File ".$data->get('data/history/filename')." does not exist.");
    }
    $yml = new PluginWfYml($data->get('data/history/filename'));
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
        $item->set("description", wfPhpfunc::str_replace("€", '#', $item->get('description')) );
        $item->set("webmaster", wfPhpfunc::str_replace("€", '#', $item->get('webmaster')) );
        /**
         * 
         */
        $history->set("$key/description", $parser->parse_text($item->get('description')) );
        $history->set("$key/webmaster", $parser->parse_text($item->get('webmaster')) );
      }
      /**
       * user_working, user_issue
       */
      $theme_version_user_working = $this->db_theme_version_user_all_working();
      $theme_version_user_issue = $this->db_theme_version_user_all_issue();
      foreach($history->get() as $k => $v){
        $i = new PluginWfArray($v);
        /**
         * Rename param settings to row_settings according to PluginWfTable.
         */
        $history->set("$k/row_settings", $i->get('settings'));
        $history->setUnset("$k/settings");
        /**
         * Add role webmaster if row_settings/role/item is set.
         */
        if($history->get("$k/row_settings/role/item")){
          /**
           * Add string with roles to webmaster param.
           */
          $roles = '';
          foreach($history->get("$k/row_settings/role/item") as $v){
            $roles .= ", $v";
          }
          $roles = wfPhpfunc::substr($roles, 2);
          $history->set("$k/webmaster", $history->get("$k/webmaster").''.$roles);
          /**
           * Add role webmaster.
           */
          $history->set("$k/row_settings/role/item/", 'webmaster');
        }
        /**
          * Add users working/issue
          */
          $history->set("$k/users_working", $theme_version_user_working->get($i->get('version').'/users'));
          $history->set("$k/users_issue", $theme_version_user_issue->get($i->get('version').'/users'));
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
  public function page_response(){
    if(!wfRequest::get('response')){
      $this->db_theme_version_user_delete();
    }else{
      $rs = $this->db_theme_version_user_one();
      if(!$rs->get('id')){
        $this->db_theme_version_user_insert();
      }
      $this->db_theme_version_user_update();
    }
    $plugin_data = wfPlugin::getPluginSettings('theme/version');
    $history_data = $this->getHistory($plugin_data);
    $history_item = new PluginWfArray();
    foreach($history_data->get('item') as $k => $v){
      $i = new PluginWfArray($v);
      if($i->get('version')==wfRequest::get('version')){
        $history_item = new PluginWfArray($i->get());
        break;
      }
    }
    exit(json_encode($history_item->get()));
  }
}
