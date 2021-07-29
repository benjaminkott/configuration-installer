# Project Configuration Installer

Installer for Configuration Packages.  

Examples:
- https://github.com/benjaminkott/config-commitmessage
- https://github.com/benjaminkott/config-typo3-editorconfig
- https://github.com/benjaminkott/config-typo3-stylelint
- https://github.com/benjaminkott/config-typo3-phpcsfixer

## Build your own configuration package

Adapt the `composer.json` of your **configuration package**.

1. Ensure the type is set to `project-configuration`.
1. Ensure `bk2k/configuration-installer` is required in any version.

```json
{
    "type": "project-configuration",
    "require": {
        "bk2k/configuration-installer": "*"
    }
}
   ```

### Add a manifest to your configuration package root.

The `manifest.json` file instructs the installer.

1. It defines which `files` should be copied to your project
1. It defines which `gitignore` entries will be added to your projects .gitignore file.


```json
{
    "files": {
        ".php_cs.dist": ".php_cs.dist"
    },
    "gitignore": [
        "/.php_cs.dist",
        "/.php_cs.cache"
    ]
}
```

### FileHandler possibilities

In addition to adding local files in your configuration package you could also require 
files from other packages. In order to do so add a dependency
```json
{
    "type": "project-configuration",
    "require": {
        "bk2k/configuration-installer": "*",
        "fancy/package": "*"
    }
}
   ```
and use the package name as prefix:
```json
{
    "files": {
        "fancy/package:somefolder/somefile": "somefolder/somefile"
    }
}
```
Remember that if you want to include files only available in source but not in dist, that you need to set prefer-source 
in the projects `composer.json`, not in the configuration package
```json
  "config": {
    "preferred-install": {
      "fancy/package": "source"
    }
  }
```
