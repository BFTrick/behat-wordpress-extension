# Settings

Behat uses [YAML](https://en.wikipedia.org/wiki/YAML) for its configuration file.


## PaulGibbs\WordpressBehatExtension

Extension `PaulGibbs\WordpressBehatExtension` integrates WordPress into Behat. These are its configuration options:

```YAML
PaulGibbs\WordpressBehatExtension:
  default_driver: wpcli
  path: ~

  # User settings.
  users:
    admin:
      username: admin
      password: admin
    editor:
      username: editor
      password: editor
    author:
      username: author
      password: author
    contributor:
      username: contributor
      password: contributor
    subscriber:
      username: subscriber
      password: subscriber

  # WordPress settings.
  site_url: ~
  permalinks:
    author_archive: author/%s/
  database:
    restore_after_test: false
    backup_path: ~

  # Driver settings.
  wpcli:
    alias: dev
    binary: wp
```

Option           | Default value | Description
-----------------| ------------- | -----------
`default_driver` | "wpcli"       | _Optional_.<br>The driver to use ("wpcli", "wpphp", "blackbox").
`path`           | null          | _Required_.<br>Path to WordPress files.
`users.*`        | _see example_ | _Optional_.<br>Keys must match names of WordPress roles.
`permalinks.*`   | _see example_ | _Optional_.<br>Permalink pattern for the specific kind of link.<br>`%s` is replaced with an ID/object name, as appropriate.
`site_url`       | null          | _Optional_.<br>If your site's `home_url()` and `site_url()` values [mismatch](https://wordpress.stackexchange.com/a/50605),<br>set this to the `site_url()` value. Defaults to `mink.base_url`
`wpcli.alias`    | null          | _Optional_.<br>[WP-CLI alias](https://wp-cli.org/commands/cli/alias/) (preferred over `wpcli.path`).
`wpcli.binary`   | `wp`          | _Optional_.<br>Path and name of WP-CLI binary.
`database.restore_after_test` | false | _Optional_.<br>If <code>true</code>, WordHat will restore your site's database to its initial state between feature tests.
`database.backup_path` | _see example_ | _Optional_.<br>If <code>restore_after_test</code> is true, and the value is a file path, WordHat will use that as the backup to restore the database from. If the path is an absolute directory, then before any tests are run, WordHat will generate a database backup and temporarily store it here. If the path has not been set, WordHat will pick its own temporary folder.


## Behat\MinkExtension

```YAML
Behat\MinkExtension:
  # Recommended settings.
  base_url: ~
```

Option     | Default value | Description
-----------| ------------- | -----------
`base_url` | _null_        | If you use relative paths in your tests, define a URL to use as the basename.

The `Behat\MinkExtension` extension integrates Mink into Behat. [Visit its website](http://mink.behat.org/en/latest/) for more information.


## Per-Environment Settings

Some of the settings in `behat.yml` are environment specific. For example, the `base_url` may be `http://test.example.dev` on your local development environment, while on a test server it might be `http://test.example.com`.

If you intend to run your tests on different environments, these sorts of settings should not be added to your `behat.yml`. Instead, they should be exported in an environment variable.

Before running tests, Behat will check the `BEHAT_PARAMS` environment variable and add these settings to the ones that are present in `behat.yml` (settings from this file takes precedence). This variable should contain a JSON object with your settings.

Example JSON object:

```JavaScript
{
  "extensions": {
    "Behat\\MinkExtension": {
      "base_url": "http://development.dev"
    }
  }
}
```

To export this into the ``BEHAT_PARAMS`` environment variable, squash the JSON object into a single line and surround with single quotes:

```Shell
export BEHAT_PARAMS='{"extensions":{"Behat\\MinkExtension":{"base_url":"http://development.dev"}}}'
```

# Drivers

WordHat provides a range of drivers for interacting with the WordPress site you are testing. A driver represents and manages the connection between the Behat and WordPress environments. Different drivers support different features.

* The **WP-CLI** driver -- the default -- uses [WP-CLI](http://wp-cli.org/) to communicate with WordPress.
* The **WordPress PHP** driver loads WordPress in the same PHP context as Behat.
* The **Blackbox** driver interacts with WordPress through a web browser, in an unpriviledged context.

To specify which driver to use for your tests, set [`default_driver`](/configuration/settings.md) in your `behat.yml` file.
