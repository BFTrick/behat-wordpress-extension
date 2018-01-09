<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use PaulGibbs\WordpressBehatExtension\WordpressDriverManager;
use PaulGibbs\WordpressBehatExtension\Context\Traits\PageObjectAwareContextTrait;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;

/**
 * Base Behat context.
 *
 * Does not contain any step defintions.
 */
class RawWordpressContext extends RawMinkContext implements WordpressAwareInterface, SnippetAcceptingContext, PageObjectAware
{
    use PageObjectAwareContextTrait;

    /**
     * WordPress driver manager.
     *
     * @var WordpressDriverManager
     */
    protected $wordpress;

    /**
     * WordPress parameters.
     *
     * @var array[]
     */
    protected $wordpress_parameters;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Build URL, based on provided path.
     *
     * @param string $path Relative or absolute URL.
     *
     * @return string
     */
    public function locatePath(string $path): string
    {
        if (stripos($path, 'http') === 0) {
            return $path;
        }

        $url = $this->getMinkParameter('base_url');

        if (strpos($path, 'wp-admin') !== false || strpos($path, '.php') !== false) {
            $url = $this->getWordpressParameter('site_url');
        }

        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Set WordPress instance.
     *
     * @param WordpressDriverManager $wordpress
     */
    public function setWordpress(WordpressDriverManager $wordpress)
    {
        $this->wordpress = $wordpress;
    }

    /**
     * Get WordPress instance.
     *
     * @return WordpressDriverManager
     */
    public function getWordpress(): \PaulGibbs\WordpressBehatExtension\WordpressDriverManager
    {
        return $this->wordpress;
    }

    /**
     * Set parameters provided for WordPress.
     *
     * IMPORTANT: this only sets the variable for the current Context!
     * Each Context exists independently.
     *
     * @param array $parameters
     */
    public function setWordpressParameters(array $parameters)
    {
        $this->wordpress_parameters = $parameters;
    }

    /**
     * Get a specific WordPress parameter.
     *
     * IMPORTANT: this only sets the variable for the current Context!
     * Each Context exists independently.
     *
     * @param string $name Parameter name.
     *
     * @return mixed
     */
    public function getWordpressParameter(string $name)
    {
        return ! empty($this->wordpress_parameters[$name]) ? $this->wordpress_parameters[$name] : null;
    }

    /**
     * Get all WordPress parameters.
     *
     * @return array
     */
    public function getWordpressParameters(): array
    {
        return $this->wordpress_parameters;
    }

    /**
     * Get active WordPress Driver.
     *
     * @param string $name Optional. Name of specific driver to retrieve.
     *
     * @return \PaulGibbs\WordpressBehatExtension\Driver\DriverInterface
     */
    public function getDriver(string $name = ''): \PaulGibbs\WordpressBehatExtension\Driver\DriverInterface
    {
        return $this->getWordpress()->getDriver($name);
    }

    /**
     * Clear Mink's browser environment.
     */
    public function resetBrowser()
    {
        $this->getSession()->reset();
    }
}
