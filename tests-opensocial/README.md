

# Behat Drupal Extension System-wide installation

A system-wide installation allows you to maintain a single copy of the testing toolset and use it for multiple test environments.
Configuration is slightly more complex than the stand-alone installation but many people prefer the flexibility and ease of maintenance this setup provides.

_Follow the Behat Drupal Extension at https://behat-drupal-extension.readthedocs.io/en/latest/globalinstall.html_
_to make a system-wide installation_

## Overview

To install the Drupal Extension globally:

```
1 Install Composer
2 Install the Drupal Extension in /opt/drupalextension
3 Create an alias to the behat binary in /usr/local/bin
4 Create your test folder
```
## Install Composer

Composer is a PHP dependency manager that will make sure all the pieces you need get installed. Full directions for global installation and more information can be found on the Composer website.:
```
curl -sS https://getcomposer.org/installer |
php mv composer.phar /usr/local/bin/composer
```
## Install the Drupal Extension

### 1 Make a directory in /opt (or wherever you choose) for the Drupal Extension:

```
cd /opt/
sudo mkdir drupalextension
cd drupalextension/
```
### 2 Create a file called composer.json and include the following:

```
{
  "require": {
    "drupal/drupal-extension": "^3.2"
  },
  "config": {
    "bin-dir": "bin/"
  }
}
```
_Warning: Requiring "drupal/drupal-extension": "^4.1" throws an error:_
```
Call to undefined method Symfony\Component\Console\Input\InputOption::isNegatable()
```
_See: https://www.drupal.org/project/drupalextension/issues/3269193#comment-14509169_

### 3 Run the install command:
```
composer install
```
It will be a bit before you start seeing any output. It will also suggest that you install additional tools, but they’re not normally needed so you can safely ignore that message.

### 4 Test that your install worked by typing the following:

In the terminal, cd to/path/to/where/resides/the/behat.yml
From this project root, it's tests-opensocial/behat/behat.yml

```
cd tests-opensocial/behat
bin/behat --help
```
If you were successful, you’ll see the help output.

### 5 Make the binary available system-wide:
```
ln -s /opt/drupalextension/bin/behat /usr/local/bin/behat
```

## Set up tests

### 1 Create the directory that will hold your tests.

There is no technical reason this needs to be inside the Drupal directory at all. It is best to keep them in the same version control repository so that the tests match the version of the site they are written for.
One clear pattern is to keep them in the sites folder as follows:

Single site: _sites/default/behat-tests_

Multi-site or named single site: _/sites/my.domain.com/behat-tests_

### 2 Wherever you make your test folder, inside it create the behat.yml file:

Here is an example of the behat.yml file.

```
default:
  suites:
    default:
      contexts:
        - FeatureContext
        - Drupal\DrupalExtension\Context\DrupalContext
        - Drupal\DrupalExtension\Context\MinkContext
        - Drupal\DrupalExtension\Context\MessageContext
        - Drupal\DrupalExtension\Context\DrushContext
  extensions:
    Drupal\MinkExtension:
      goutte: ~
      selenium2: ~
      base_url: http://seven.l
    Drupal\DrupalExtension:
      blackbox: ~
```

Then take a look hereby at the tests-opensocial/behat/behat.yml file we've configured for this project tests.
It's configured to support tests on two different browsers: chrome and firefox.

### 3 Initialize behat.

In the terminal, cd to/path/to/where/resides/the/behat.yml
From this project root, it's tests-opensocial/behat/behat.yml
```
cd tests-opensocial/behat
bin/behat --init
```
This creates the features folder with some basic things to get you started

### 4 This will generate a FeatureContext.php file that looks like this:

```
<?php

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }
}
```
This will make your FeatureContext.php aware of both the Drupal Extension and the Mink Extension,
so you’ll be able to take advantage of their drivers and step definitions and add your custom step definitions here.
The FeatureContext.php file must be in the same directory as your behat.yml file otherwise in step 5 you will get the following error:

[BehatBehatContextExceptionContextNotFoundException] FeatureContext context class not found and can not be used.

### 5 To ensure everything is set up appropriately, type:

```
cd tests-opensocial/behat
behat -dl
```
You’ll see a list of steps like the following, but longer, if you’ve installed everything successfully:
```
 default | Given I am an anonymous user
 default | Given I am not logged in
 default | Given I am logged in as a user with the :role role(s)
 default | Given I am logged in as :name
```

# Download ChromeDriver and GeckoDriver

Go to the below address and download the drivers for your operating system. In this case Mac os.
From https://www.selenium.dev/downloads/ :

### 1 Scroll down to Platforms Supported by Selenium
### 2 Click on [+ Browsers]
### 3 Click on Documentation in front of the browser you want to download the version that matches your computer browser version

For Chromedriver: https://sites.google.com/chromium.org/driver/ > download or  https://chromedriver.chromium.org/ > download
For Gekodriver: https://firefox-source-docs.mozilla.org/testing/geckodriver/Support.html > releases

## Move the drivers to the /usr/local/bin folder

/usr/local/bin folder should be defined as a global path on your system.
To check that, run the following command in terminal
```
sudo nano /etc/paths
```
Go to your downloads folder, find the chromedriver and geckodriver files and unpack them. Move the executable files to /usr/local/bin.
```
mv chromedriver /usr/local/bin
```
```
mv geckodriver /usr/local/bin
```

If you face “Error: “chromedriver” cannot be opened because the developer cannot be verified. Unable to launch the chrome browser“,
you need to go to the usr/local/bin folder and right-click the chromedriver file and open it.
After this step, re-run your tests, chrome driver will work.
Same for geckodriver.

# Download and configure Selenium

https://www.selenium.dev/downloads/
For this use case, we are using selenium server standalone version 3.141.59
Scroll down to Previous Releases and click releases
https://github.com/SeleniumHQ/selenium/releases/download/selenium-3.141.59/selenium-server-standalone-3.141.59.jar

## Create a seleniumgrid folder under username

Unpack and copy the selenium-server-standalone-3.141.59.jar file from Downloads and paste it under the username/seleniumgrid folder

## Configure Selenium

Selenium can be configured to run as a hub, a node, or standalone. For this tutorial, we'll configure the hub first, then add a node.

## What are Hub and Nodes?

We have a Hub which is a server that we connect from our tests and we have Nodes.
Nodes can be on different machines and they register with the hub.
Simply, we have a hub and several nodes.
Nodes are registered in our hub and the hub knows which browsers are available.
Hub sends requests to the nodes based on desired capabilities and executes the tests.

## download the hub and the node config files from the below addresses

Download the Hub config at https://github.com/SeleniumHQ/selenium/blob/selenium-3.141.59/java/server/src/org/openqa/grid/common/defaults/DefaultHub.json
Save the bellow file as hub.json under the seleniumgrid folder.
```
{
  "port": 4444,
  "newSessionWaitTimeout": -1,
  "servlets" : [],
  "withoutServlets": [],
  "custom": {},
  "capabilityMatcher": "org.openqa.grid.internal.utils.DefaultCapabilityMatcher",
  "registry": "org.openqa.grid.internal.DefaultGridRegistry",
  "throwOnCapabilityNotPresent": true,
  "cleanUpCycle": 5000,
  "role": "hub",
  "debug": false,
  "browserTimeout": 0,
  "timeout": 1800
}
```

Download the Node config at https://github.com/SeleniumHQ/selenium/blob/selenium-3.141.59/java/server/src/org/openqa/grid/common/defaults/DefaultNodeWebDriver.json
Save the bellow file as node.json under the seleniumgrid folder.
```
{
  "capabilities":
  [
    {
      "browserName": "firefox",
      "marionette": true,
      "maxInstances": 5,
      "seleniumProtocol": "WebDriver"
    },
    {
      "browserName": "chrome",
      "maxInstances": 5,
      "seleniumProtocol": "WebDriver"
    },
    {
      "browserName": "internet explorer",
      "platform": "WINDOWS",
      "maxInstances": 1,
      "seleniumProtocol": "WebDriver"
    },
    {
      "browserName": "safari",
      "technologyPreview": false,
      "platform": "MAC",
      "maxInstances": 1,
      "seleniumProtocol": "WebDriver"
    }
  ],
  "proxy": "org.openqa.grid.selenium.proxy.DefaultRemoteProxy",
  "maxSession": 5,
  "port": -1,
  "register": true,
  "registerCycle": 5000,
  "hub": "http://localhost:4444",
  "nodeStatusCheckTimeout": 5000,
  "nodePolling": 5000,
  "role": "node",
  "unregisterIfStillDownAfter": 60000,
  "downPollingLimit": 2,
  "debug": false,
  "servlets" : [],
  "withoutServlets": [],
  "custom": {}
}
```

## To start Selenium Grid on MAC

save the below script as the startgrid.command under the seleniumgrid folder.
```
#! /bin/bash
osascript -e 'tell app "Terminal"
    do script "cd seleniumgrid/ &&
    java -jar selenium-server-standalone.jar -role hub -hubConfig hub.json"
end tell'
osascript -e 'tell app "Terminal"
    do script "cd seleniumgrid/ &&
    java -jar -Dwebdriver.chrome.driver=/usr/local/bin/chromedriver selenium-server-standalone.jar -role node -nodeConfig node.json"
end tell'
```
This script automatically starts the node and the hub.

Note: If you also download and move the firefox driver (geckodriver) to the /usr/local/bin folder, you can use the below script. It registers both Firefox and Chrome drivers.

```
#! /bin/bash
osascript -e 'tell app "Terminal"
    do script "cd seleniumgrid/ &&
    java -jar selenium-server-standalone.jar -role hub -hubConfig hub.json"
end tell'
osascript -e 'tell app "Terminal"
    do script "cd seleniumgrid/ &&
    java -jar -Dwebdriver.gecko.driver=/usr/local/bin/geckodriver -Dwebdriver.chrome.driver=/usr/local/bin/chromedriver selenium-server-standalone.jar -role node -nodeConfig node.json"
end tell'
```

Give the script executable permissions
```
chmod u+x ./seleniumgrid/startgrid.command
```

Run the script willstart the selenium grid
```
./seleniumgrid/startgrid.command
```

# Run behat test

cd to/path/to/where/resides/the/behat.yml
From this project root, it's tests-opensocial/behat/behat.yml

Run test on default profile:
```
bin\behat
```

Run test on chrome:
```
bin\behat --profile=chrome
```

Run test on firefox:
```
bin\behat --profile=firefox
```
