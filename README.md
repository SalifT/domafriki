# Setup localhost

After cloning the repo

## Generating a .env file

### Run the following drupal console command:

```
/vendor/bin/drupal dotenv:init
```

Follow the .env.example file in project root directory and answer to the questions
according to your environment settings.
Two new files will be automatically created:
  - settings.php
  - settings.php.original

Edit the settings.php file and make sure it ends like the following:

```
/**
 * Entity update backup.
 *
 * This is used to inform the entity storage handler that the backup tables as
 * well as the original entity type and field storage definitions should be
 * retained after a successful entity update process.
 */
$settings['entity_update_backup'] = TRUE;

$config_directories['sync'] = '../config/sync';

// Load .env file if exists
if (file_exists(dirname(DRUPAL_ROOT) . '/.env')) {
  // Load environment
  $dotenv = new \Dotenv\Dotenv(dirname(DRUPAL_ROOT));
  $dotenv->load();
}

# Load environment
$env = getenv('ENVIRONMENT');

# Load key/value settings
$settings_drupal = array_filter(
  $_SERVER,
  function($key) {
      return  strpos($key, 'SETTINGS_') === 0;
  },
  ARRAY_FILTER_USE_KEY
);

# Set key/value settings
foreach ($settings_drupal as $name => $value) {
  if (substr($name, 0, 9) === 'SETTINGS_') {
    $key = strtolower(substr($name, 9));
    $settings[$key] = $value;
  }
}

$base_path = $app_root . '/' . $site_path;
$servicesFile = $base_path . '/services.'.$env.'.yml';
$settingsFile = $base_path . '/settings.'.$env.'.php';

// Load services definition file.
if (file_exists($servicesFile)) {
    $settings['container_yamls'][] = $servicesFile;
}

// Load settings file.
if (file_exists($settingsFile)) {
  include $settingsFile;
}

$databases['default']['default'] = array (
  'database' =>  getenv('DATABASE_NAME'),
  'username' => getenv('DATABASE_USER'),
  'password' => getenv('DATABASE_PASSWORD'),
  'prefix' => '',
  'host' => getenv('DATABASE_HOST'),
  'port' => getenv('DATABASE_PORT'),
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

/**
 * Load local development override configuration, if available.
 *
 * Use settings.local.php to override variables on secondary (staging,
 * development, etc) installations of this site. Typically used to disable
 * caching, JavaScript/CSS compression, re-routing of outgoing emails, and
 * other things that should not happen on development and testing sites.
 *
 * Keep this code block at the end of this file to take full effect.
 */
#
#if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
#  include $app_root . '/' . $site_path . '/settings.local.php';
#}

```

## Create settings.local.php

If you need to use the settings.local.php file in your environment.
Change to the "dev" or feature branch.
Create the settings.local.php and move to it the database credential part from settings.php as following :

```
<?php

$databases['default']['default'] = array(
  'database' => '[PUT_YOUR_DB_NAME]',
  'username' => '[PUT_YOUR_DB_USER]',
  'password' => '[PUT_YOUR_DB_PASS]',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

$settings['cache']['bins']['page'] = 'cache.backend.null';

```

## Local development services

Ensure sites/development.services.yml is configured properly:

````
# Local development services.
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    debug: true
    auto_reload: true
    cache: true
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
````
Import initdb.sql located at the project root.
Run composer install && drush cr && drush cim -y

### NOTES:

To access as admin use drush uli.
If you need files dir you can target it with stage_file_proxy module or asking for a zipped copy.
Activate "Coming soon" landing page
Just set Event "coming soon" landing page 1.0.0 at /admin/appearance as default theme.

If you want to modify the landing page go to themes/coming_soon_th/templates/layout. There you will find all HTML and used assets for the landing page.

# Open Social

This is a composer based installer for the [Open Social distribution](http://www.drupal.org/project/social).

## Prerequisites for installing Open Social

1. [Composer](https://getcomposer.org/download/)

It's just composer, isn't it awesome? :)

## Installation of Open Social

```
composer create-project goalgorilla/social_template:dev-master DIRECTORY --no-interaction
```

Composer will create a new directory called DIRECTORY. Inside you will find the html directory with the entire code base of [Open Social distribution](http://www.drupal.org/project/social). You should be able to install it like any other Drupal site.

## Learn more about Composer for Drupal

Checkout this [presentation](https://docs.google.com/presentation/d/1gxcxT6o47xVrfsZ7ZSQKjBRT-gfE54A1Z9kjvvGHwCo/edit#slide=id.p) from @ModsUnraveled.

## Issues

### Install issues for Open Social
[documentation](https://www.drupal.org/docs/8/distributions/open-social/installing-and-updating)

### Installing outside of HTML folder
[See this issue for more information](https://www.drupal.org/project/social/issues/2792543#comment-11591981)

### Open Social issues & Support
For any issues with the platform we kindly ask you to use the [drupal.org](http://www.drupal.org/project/issues/social) issue queue. This way we can centralise all the information and make the feedback available for other users for documentation purposes. Next to giving people the credit they deserve.

### **Slack**
We are also available on Slack. Visit https://www.drupal.org/slack to see how you can join Drupal Slack. After that you can find us in the #opensocial channel. Our team will be available to answer your questions during our community hours there too.

### **Community hours**
 Every week we are available on Slack on:
 - Wednesday between 16:00 and 17:00 Europe/Amsterdam Timezone
- Friday between 10:00 and 11:00 Europe/Amsterdam Timezone
