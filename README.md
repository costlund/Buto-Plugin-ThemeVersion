# Buto-Plugin-ThemeVersion
Plugin to show software history registrated in yml file.


## Widget

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

### History

View a list of all history for plugin, theme.

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



### Version

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


