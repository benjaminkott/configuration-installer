# Project Configuration Installer

Installer for Configuration Packages.  

Examples:
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
