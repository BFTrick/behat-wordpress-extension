This extension offers a simple way to begin testing and driving your WordPress applications with Behat. Some benefits include:

- **Efficient:** Like WordPress' PHPUnit implementation, SQL transactions are used to restore the database to a clean state before each scenario is tested.
- **Environments:** Supports both release and development versions of WordPress.
- **Workflow:** A number of useful traits are available, which will speed up your Behat workflow.

To get started, you only need to follow a few steps:

# 1. Install Dependencies

As always, we need to pull in some dependencies through Composer.

    composer require paulgibbs/behat-wordpress-extension

This will give us access to Behat, Mink, and, of course, the Laravel extension.

# 2. Create the Behat.yml Configuration File

Next, within your project root, create a `behat.yml` file, and add:

```
default:
    extensions:
        Laracasts\Behat:
            # env_path: .env.behat
        Behat\MinkExtension:
            default_session: laravel
            laravel: ~
```

Here, is where we reference the Laravel extension, and tell Behat to use it as our default session. You may pass an optional parameter, `env_path` (currently commented out above) to specify the name of the environment file that should be referenced from your tests. By default, it'll look for a `.env.behat` file.

This file should, like the standard `.env` file in your project root, contain any special environment variables
for your tests (such as a special acceptance test-specific database).

# 3. Write Some Features

TODO

## Feature Context Traits

As a convenience, this package also includes a number of traits to streamline common tasks, such as migrating your database, or using database transactions, or even testing mail.

### Database Transactions

On the other hand, you might prefer to run all of your tests through database transactions. You'll get a nice speed boost out of the deal, as your data will never actually be saved to the database. To take advantage of this, once again, pull in the `Laracasts\Behat\Context\DatabaseTransactions` trait, like so:

```php
// ...

use Laracasts\Behat\Context\DatabaseTransactions;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use DatabaseTransactions;

}
```

Once you pull in this trait, before each scenario runs, it'll begin a new transaction. And when the scenario completes, we'll roll it back for you.

### Service: MailTrap

Especially when functional testing, it can be beneficial to test your mail against a real test server (rather than mocking it out, and hoping that things were formatted correctly). If you're a fan of [MailTrap](https://mailtrap.io/) (highly recommended), this extension can help you!

If you haven't already, create a quick account (free) and inbox at MailTrap.io. Next, update either your `config/mail.php` to use the settings that MailTrap provides you, or modify your `.env.behat` variables to reference them. Once you've configured your app to use MailTrap, the only other thing we need is your MailTrap API key, and the default inbox id that we should use. Makes these available in `config/services.php`. Here's an example:

```php
'mailtrap' => [
    'secret' => 'YOUR API KEY',
    'default_inbox' => 'ID OF THE MAILTRAP INBOX YOU CREATED'
]
```

That should do it! Now, just pull in the trait, and you're ready to go! Let me show you:

```php
<?php

// ...

use Laracasts\Behat\Context\Services\MailTrap;
use PHPUnit_Framework_Assert as PHPUnit;

class DmcaContext extends MinkContext implements SnippetAcceptingContext
{
    use MailTrap;

    /**
     * @Then an email should be sent to YouTube
     */
    public function anEmailShouldBeSentToYoutube()
    {
        $lastEmail = $this->fetchInbox()[0];
        $stub = file_get_contents(__DIR__ . '/../stubs/dmca-complete.txt');

        PHPUnit::assertEquals('DMCA Notice', $lastEmail['subject']);
        PHPUnit::assertContains($stub, $lastEmail['text_body']);
    }

}
```

Notice that call to `fetchInbox()`? That will send an API request to MailTrap, which will return to you an array of all the messages/emails in your inbox. As such, if you want to write some assertions against the most recently received email in your MailTrap inbox, you can do:

```php
$lastEmail = $this->fetchInbox()[0];
```

If working along, you can dump that variable to see all of the various fields that you may write assertions against. In the example above, we're ensuring that the subject was set correctly, and the body of the email matches a stub that we've created.

Even better, after each scenario completes, we'll go ahead and empty out your MailTrap inbox for convenience.
