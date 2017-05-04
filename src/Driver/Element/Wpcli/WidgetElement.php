<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;
use UnexpectedValueException;

/**
 * WP-API driver element for managing user accounts.
 */
class WidgetElement extends BaseElement
{

    /**
     * Adds a widget to the sidebar with the specified arguments
     *
     * @param string $widget_name The ID base of the widget (e.g. 'meta', 'calendar'). Case insensitive.
     * @param string $sidebar_id The ID of the sidebar to the add the widget to
     * @param array $args Associative array of widget settings for this widget
     * @throws \Exception If the widget is not registered.
     */
    public function addToSidebar($widget_name, $sidebar_id, $args)
    {
        $widget_name = strtolower($widget_name);

        foreach ($args as $key => $value) {
            $args['--' . $key] = $value;
            unset($args[$key]);
        }

        $wpcli_args = [
                $widget_name,
                $sidebar_id
            ] + $args;

        $this->drivers->getDriver()->wpcli('widget', 'add', $wpcli_args);
    }

    /**
     * Gets a sidebar ID from its human-readable name
     *
     * @param string $sidebar_name The name of the sidebar (e.g. 'Footer', 'Widget Area', 'Right Sidebar')
     * @return string The sidebar ID
     * @throws \Exception If the sidebar is not registered
     */
    public function getSidebar($sidebar_name)
    {
        $registered_sidebars = json_decode($this->drivers->getDriver()->wpcli('sidebar', 'list', [
            '--format' => 'json',
        ])['stdout']);

        $sidebar_id = null;
        foreach ($registered_sidebars as $sidebar) {
            if ($sidebar_name == $sidebar->name) {
                $sidebar_id = $sidebar->id;
                break;
            }
        }

        if (is_null($sidebar_id)) {
            throw new UnexpectedValueException(sprintf('Sidebar "%s" does not exist', $sidebar_name));
        }
        return $sidebar_id;
    }
}
