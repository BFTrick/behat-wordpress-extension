# WordPress Behat Extension

The WordPress Behat Extension is an integration layer between [Behat](http://behat.org), [Mink Extension](https://github.com/Behat/MinkExtension), and WordPress. It provides step definitions for common testing scenarios specific to WordPress sites.

[![Build Status](https://travis-ci.org/paulgibbs/behat-wordpress-extension.svg?branch=master)](https://travis-ci.org/paulgibbs/behat-wordpress-extension)

The WordPress Behat Extension supports WordPress versions 4.7 onwards.

[![Latest Stable Version](https://poser.pugx.org/paulgibbs/behat-wordpress-extension/v/stable.svg)](https://packagist.org/packages/paulgibbs/behat-wordpress-extension)
[![Total Downloads](https://poser.pugx.org/paulgibbs/behat-wordpress-extension/downloads.svg)](https://packagist.org/packages/paulgibbs/behat-wordpress-extension)
[![Latest Unstable Version](https://poser.pugx.org/paulgibbs/behat-wordpress-extension/v/unstable.svg)](https://packagist.org/packages/paulgibbs/behat-wordpress-extension)
[![License](https://poser.pugx.org/paulgibbs/behat-wordpress-extension/license.svg)](https://packagist.org/packages/paulgibbs/behat-wordpress-extension)


## Use it for testing your WordPress site.

If you're new to the WordPress Behat Extension, we recommend starting with the [full documentation](https://example.com)


### Quick start

1. Install using [Composer](https://getcomposer.org/):

    ``` bash
    mkdir projectdir
    cd projectdir
    curl -sS https://getcomposer.org/installer | php
    php composer.phar require paulgibbs/behat-wordpress-extension='*'
    ```

1.  In the projectdir, create a file called `behat.yml`. Below is the minimal configuration. Many more options are covered in the [full documentation](https://example.com).

  ``` yaml
  default:
    suites:
      default:
        contexts:
          - PaulGibbs\WordpressBehatExtension\Context\WordpressContext

    extensions:
      Behat\MinkExtension:
        base_url: http://wordpress-develop.dev  # Replace with your site's URL
        goutte: ~

      PaulGibbs\WordpressBehatExtension:
        path: /srv/www/wordpress-develop.dev/src  # Replace with your site's path
  ```

1. In the projectdir, run

    ``` bash
    bin/behat --init
    ```

1. Find pre-defined steps to work with using:

    ```bash
    bin/behat -di
    ```

1. Define your own steps in `projectdir\features\FeatureContext.php`

1. Start adding your [feature files](http://docs.behat.org/en/latest/guides/1.gherkin.html) to the `features` directory of your repository.
