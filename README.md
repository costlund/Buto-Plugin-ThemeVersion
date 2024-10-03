# Buto-Plugin-ThemeVersion

<p>Plugin to show software history registrated in yml file. User with role tester can response on each version.</p>

<a name="key_0"></a>

## Settings

<p>Page registration to handle response and widget history.</p>
<pre><code>plugin_modules:
  theme_version:
    plugin: 'theme/version'</code></pre>
<pre><code>plugin:
  theme:
    version:
      enabled: true
      data:</code></pre>
<p>Set filename where history file is.</p>
<pre><code>        history:
          filename: /theme/[theme]/config/manifest.yml</code></pre>
<p>Tester.
Optional. 
Get tester email from table account if user has role tester. 
One has to set mysql param.</p>
<pre><code>        mysql: 'yml:/../buto_data/theme/sit/kanin/mysql.yml'</code></pre>
<p>Restrict page json request to user role.</p>
<pre><code>        settings:
          role:
            item:
              - webmaster</code></pre>
<p>Email link.
By click on a row a modal is shown with an email link.
Set param application/title in theme settings to add it to email subject.</p>
<pre><code>application:
  title: Datos</code></pre>

<a name="key_1"></a>

## Usage

<p>Data.
Param description and webmaster will be parsed by PluginReadmeParser. 
Character € will be replaced by #.</p>
<pre><code>history:
  '1.0':
    date: '2018-11-01'
    title: First version
    description: Add files.
    webmaster: This is a comment only to be seen by webmaster.
  '1.0.1':
    date: '2018-11-02'
    title: Bug fix
    description: A bug fix.
  '1.1':
    date: '2018-11-03'
    title: Improvement
    description: |
      €€€H3 headline
      - Some list text.

      €€€H3 headline
      - Some list text.
      - Some list text.</code></pre>
<p>Schema.</p>
<pre><code>/plugin/theme/version/mysql/schema.yml</code></pre>

<a name="key_2"></a>

## Pages



<a name="key_2_0"></a>

### page_history



<a name="key_2_1"></a>

### page_history_all



<a name="key_2_2"></a>

### page_response



<a name="key_3"></a>

## Widgets



<a name="key_3_0"></a>

### widget_history

<p>View a list of theme history.</p>
<pre><code>type: widget
data:
  plugin: theme/version
  method: history</code></pre>
<p>Retrict some items. In this example using role webdeveloper.</p>
<pre><code>history:
  1.0.0:
    date: '2020-01-01'
    title: ''
    description: ''
    webmaster: 'Only show this post if user has role webdeveloper. Role webmaster will always be able to see all data.'
    settings:
      role:
        item:
          - webdeveloper</code></pre>

<a name="key_3_1"></a>

### widget_history_all

<p>View a list of all history for plugin, theme and sys.</p>
<pre><code>type: widget
data:
  plugin: theme/version
  method: history_all</code></pre>
<p>Theme must have file /config/manifest.yml with param history.</p>
<pre><code>history:
  '1.1':
    date: '2019-10-10'
    title: Improvement
    description: Improvement for this theme.
  '1.0':
    date: '2019-10-09'
    title: First version.
    description: First version of this theme.</code></pre>

<a name="key_3_2"></a>

### widget_include

<p>Include js.
Optional. When click on a row in widget history a modals is shown with an email link</p>
<pre><code>type: widget
data:
  plugin: theme/version
  method: include</code></pre>

<a name="key_3_3"></a>

### widget_version

<p>Version number.
Get current version.</p>
<pre><code>type: widget
data:
  plugin: theme/version
  method: version
  data:
    filename: /theme/[theme]/data/version_history.yml</code></pre>

<a name="key_4"></a>

## Event



<a name="key_5"></a>

## Construct



<a name="key_5_0"></a>

### __construct



<a name="key_6"></a>

## Methods



<a name="key_6_0"></a>

### db_account_role_tester



<a name="key_6_1"></a>

### db_theme_version_user_all_working



<a name="key_6_2"></a>

### db_theme_version_user_all_issue



<a name="key_6_3"></a>

### db_theme_version_user_responses



<a name="key_6_4"></a>

### db_theme_version_user_one



<a name="key_6_5"></a>

### db_theme_version_user_insert



<a name="key_6_6"></a>

### db_theme_version_user_update



<a name="key_6_7"></a>

### db_theme_version_user_delete



<a name="key_6_8"></a>

### getHistoryAll



<a name="key_6_9"></a>

### replace_line_break



<a name="key_6_10"></a>

### getHistory



