<?php
namespace PaulGibbs\WordpressBehatExtension\Context\Initializer;

use Behat\Behat\Context\Context,
    Behat\Behat\Context\Initializer\ContextInitializer;

use PaulGibbs\WordpressBehatExtension\WordpressDriverManager;

/**
 * Behat Context initializer.
 */
class WordpressAwareInitializer implements ContextInitializer
{
    /**
     * WordPress driver manager.
     *
     * @var WordpressDriverManager
     */
    protected $wordpress;

    /**
     * WordPress context parameters.
     *
     * @var array
     */
    protected $parameters = [];


    /**
     * Constructor.
     *
     * @param WordpressDriverManager $wordpress
     * @param array               $wordpressParams
     */
    public function __construct(WordpressDriverManager $wordpress, array $parameters)
    {
        $this->wordpress  = $wordpress;
        $this->parameters = $parameters;
    }

    /**
     * Prepare everything that the Context needs.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (! $context instanceof WordpressAwareInterface) {
            return;
        }

        $context->setWordpress($this->wordpress);
        $context->setWordpressParameters($this->parameters);
    }
}
