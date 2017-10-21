<?php
namespace PaulGibbs\WordpressBehatExtension\Context\Awareness;

/**
 * Provides driver agnostic logic (helper methods) relating to posts and content.
 */
trait ContentAwareContextTrait
{
    use BaseAwarenessTrait;

    /**
     * Create content.
     *
     * @param array $args Set the values of the new content item.
     *
     * @return array {
     *             @type int $id Content ID.
     *             @type string $slug Content slug.
     *             @type string $url Content permalink.
     *         }
     */
    public function createContent($args)
    {
        $post = $this->getDriver()->content->create($args);
        $url  = $this->getDriver()->content->getPermalink($post->ID);

        return array(
            'id'   => (int) $post->ID,
            'slug' => $post->post_name,
            'url'  => $url,
        );
    }

    /**
     * Get content from its title.
     *
     * @param string $title     The title of the content to get.
     * @param string $post_type Post type(s) to consider when searching for the content.
     *
     * @throws \UnexpectedValueException If post does not exist
     *
     * @return array {
     *             @type int    $id   Content ID.
     *             @type string $slug Content slug.
     *             @type string $url  Content url.
     *         }
     */
    public function getContentFromTitle($title, $post_type = '')
    {
        $post = $this->getDriver()->content->get($title, ['by' => 'title', 'post_type' => $post_type]);
        $url = $this->getDriver()->content->getPermalink($post->ID);

        return array(
            'id'   => (int) $post->ID,
            'slug' => $post->post_name,
            'url'  => $url,
        );
    }

    /**
     * Delete specified content.
     *
     * @param int   $contentId    ID of content to delete.
     * @param array $args  Optional. Extra parameters to pass to WordPress.
     */
    public function deleteContent($contentId, $args = [])
    {
        $this->getDriver()->content->delete($id, $args);
    }
}
