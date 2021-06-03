# Buto-Plugin-ThemeVersion
Plugin to show software history registrated in yml file. User with role tester can response on each version.

## Include js
Optional. When click on a row in widget history a modals is shown with an email link
```
type: widget
data:
  plugin: theme/version
  method: include
```

## List widgets

### History
View a list of history.
```
type: widget
data:
  plugin: theme/version
  method: history
  data:
    filename: /theme/[theme]/data/version_history.yml
```
Retrict some items. In this example using role webdeveloper.
```
history:
  1.0.0:
    date: '2020-01-01'
    title: ''
    description: ''
    webmaster: 'Only show this post if user has role webdeveloper. Role webmaster will always be able to see all data.'
    settings:
      role:
        item:
          - webdeveloper
```

#### Email link
By click on a row a modal is shown with an email link.
Set param application/title in theme settings to add it to email subject.
```
application:
  title: Datos
```

### History all

View a list of all history for plugin, theme and sys.

```
type: widget
data:
  plugin: theme/version
  method: history_all
```

Theme must have file /config/manifest.yml with param history.

```
history:
  '1.1':
    date: '2019-10-10'
    title: Improvement
    description: Improvement for this theme.
  '1.0':
    date: '2019-10-09'
    title: First version.
    description: First version of this theme.
```



## Version number

Get current version.

```
type: widget
data:
  plugin: theme/version
  method: version
  data:
    filename: /theme/[theme]/data/version_history.yml
```


## Data
Param description and webmaster will be parsed by PluginReadmeParser. Character € will be replaced by #.
```
history:
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
      - Some list text.
```

## Tester
Optional. Get tester email from table account if user has role tester. One has to set mysql param.
```
plugin:
  theme:
    version:
      data:
        mysql: 'yml:/../buto_data/theme/sit/kanin/mysql.yml'
```

## Page
Page registration to handle response.
```
plugin_modules:
  theme_version:
    plugin: 'theme/version'
```

## Schema
```
/plugin/theme/version/mysql/schema.yml
```
