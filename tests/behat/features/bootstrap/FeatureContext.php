<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ElementNotFoundException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Drupal\locale\SourceString;

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

  /**
   * Get the wysiwyg instance variable to use in Javascript.
   *
   * @param string
   *   The instanceId used by the WYSIWYG module to identify the instance.
   *
   * @throws Exception
   *   Throws an exception if the editor does not exist.
   *
   * @return string
   *   A Javascript expression representing the WYSIWYG instance.
   */
  protected function getWysiwygInstance($instanceId) {
    $instance = "CKEDITOR.instances['$instanceId']";
    if (!$this->getSession()->evaluateScript("return !!$instance")) {
      throw new \Exception(sprintf('The editor "%s" was not found on the page %s', $instanceId, $this->getSession()->getCurrentUrl()));
    }
    return $instance;
  }

  /**
   * @When /^I fill in the "([^"]*)" WYSIWYG editor with "([^"]*)"$/
   */
  public function iFillInTheWysiwygEditor($locator, $text) {
    $field = $this->getSession()->getPage()->findField($locator);

    if (NULL === $field) {
      throw new ElementNotFoundException($this->getDriver(), 'form field', 'id|name|label|value|placeholder', $locator);
    }

    $id = $field->getAttribute('id');
    $instance = $this->getWysiwygInstance($id);
    $this->getSession()->executeScript("$instance.setData(\"$text\");");
  }

  /**
   * @When /^I click on the embed icon in the WYSIWYG editor$/
   */
  public function clickEmbedIconInWysiwygEditor() {

    $cssSelector = 'a.cke_button__social_embed';

    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector)
    );
    if (NULL === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
    }

    $element->click();
  }

}
