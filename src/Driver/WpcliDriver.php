<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

/**
 * Connect Behat to WordPress using WP-CLI.
 */
class WpcliDriver extends BaseDriver
{
    /**
     * The name of a WP-CLI alias for tests requiring shell access.
     *
     * @var string
     */
    protected $alias = '';

    /**
     * WP-CLI path (to the WordPress files).
     *
     * @var string
     */
    protected $path = '';

    /**
     * WordPress site URL.
     *
     * @var string
     */
    protected $url = '';


    /**
     * Constructor.
     *
     * @param string $alias WP-CLI alias. This or $path must be not falsey.
     * @param string $path  Absolute path to WordPress site files. This or $alias must be not falsey.
     * @param string $url   WordPress site URL.
     */
    public function __construct($alias, $path, $url)
    {
        $this->alias = ltrim($alias, '@');
        $this->path  = realpath($path);
        $this->url   = rtrim(filter_var($url, FILTER_SANITIZE_URL), '/');

        if (! $this->alias && ! $this->path) {
            throw new \RuntimeException('WP-CLI driver requires an `alias` or `root` path.');
        }
    }

    /**
     * Execute a WP-CLI command.
     *
     * @param string $command       Command name.
     * @param string $subcommand    Subcommand name.
     * @param array  $raw_arguments Optional. Associative array of arguments for the command.
     * @return array {
     *     WP-CLI command results.
     *
     *     @type array $cmd_output Command output.
     *     @type int   $exit_code  Returned status code of the executed command.
     * }
     */
    public function wpcli($command, $subcommand, $raw_arguments = array())
    {
        $arguments  = '';
        $cmd_output = array();
        $exit_code  = 0;

        // Build parameter list.
        foreach ($raw_arguments as $name => $value) {
            if (is_int($name)) {
                $arguments .= "{$value} ";
            } else {
                $arguments .= sprintf('%s=%s ', $name, escapeshellarg($value));
            }
        }

        // Support WP-CLI environment alias, or path and URL.
        if ($this->alias) {
            $config = "@{$this->alias}";
        } else {
            // TODO: review best practice with escapeshellcmd() here, and impact on metacharactes.
            $config = sprintf('--path=%s --url=%s', escapeshellarg($this->path), escapeshellarg($this->url));
        }

        exec("wp {$config} {$command} {$subcommand} {$arguments} --no-color", $cmd_output, $exit_code);

        return compact('cmd_output', 'exit_code');
    }

    /**
     * Clear object cache.
     */
    public function clearCache()
    {
        $this->wpcli('cache', 'flush');
    }

    /**
     * Activate a plugin.
     *
     * @param string $plugin
     */
    public function activatePlugin($plugin)
    {
        $this->wpcli('plugin', 'activate', array($plugin));
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $plugin
     */
    public function deactivatePlugin($plugin)
    {
        $this->wpcli('plugin', 'deactivate', array($plugin));
    }

    /**
     * Switch active theme.
     *
     * @param string $theme
     */
    public function switchTheme($theme)
    {
        $this->wpcli('theme', 'activate', array($theme));
    }

    /**
     * Create a term in a taxonomy.
     *
     * @param string $term
     * @param string $taxonomy
     * @param array  $args Optional. Set the values of the new term.
     * @return int Term ID.
     */
   public function createTerm($term, $taxonomy, $args = array())
   {
        $wpcli_args = array($taxonomy, $term, '--porcelain');
        $whitelist  = array('description', 'parent', 'slug');

        foreach ($whitelist as $option) {
            if (isset($args[$option])) {
                $wpcli_args["--{$option}"] = $args[$option];
            }
        }

        return $this->wpcli('term', 'create', $wpcli_args);
   }

    /**
     * Delete a term from a taxonomy.
     *
     * @param int    $term_id
     * @param string $taxonomy
     */
    public function deleteTerm($term_id, $taxonomy)
    {
        $this->wpcli('term', 'delete', array($taxonomy, $term_id));
    }

    /**
     * Export WordPress database.
     *
     * @return string Absolute path to database SQL file.
     */
    public function exportDatabase()
    {
        while (true) {
            $filename = uniqid('database-', true) . '.sql';

            if (! file_exists(getcwd() . "/{$filename}")) {
                break;
            }
        }

        // Protect against WP-CLI changing the filename.
        $filename = $this->wpcli('db', 'export', array($filename, '--porcelain'));

        return getcwd() . "/{$filename}";
    }

    /**
     * Import WordPress database.
     *
     * @param string $filename Absolute path to database SQL file to import.
     */
    public function importDatabase($filename)
    {
        $filename = getcwd() . "/{$filename}";
        $this->wpcli('db', 'import', array($filename));

        // TODO: delete backup?
    }
}
