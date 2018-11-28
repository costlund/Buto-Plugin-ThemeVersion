# Buto-Plugin-ThemeVersion
Plugin to show software history registrated in yml file.


Widget.
```
type: widget
data:
  plugin: theme/version
  method: history
  data:
    filename: /theme/[theme]/data/version_history.yml
```


Data
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
    description: An improvement.
```


