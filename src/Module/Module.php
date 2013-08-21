<?php
/**
 * Holds Module.
 *
 * @package WALib
 */
namespace WALib\Module;
/**
 * Represents a module.
 *
 * @package WALib
 */
abstract class Module
{
    /**
     * The window title.
     *
     * @var string
     */
    protected $_title = '';

    /**
     * The application head description.
     *
     * @var string
     */
    protected $_description = '';

    /**
     * The current module.
     *
     * Holds data for the module which can also be displayed.
     *
     * @var Module
     */
    protected $_module = null;

    /**
     * The template file which should be called in the view process.
     *
     * The file should call $this->_renderContent() to render the specific
     * index file corresponding to the module and controller.
     *
     * @var string
     */
    protected $_template = null;

    /**
     * An array with the menu entries.
     *
     * Used to display a navigation and to generate the URLs for it.
     *
     * @var \WALib\Menu\Entry[]
     */
    protected $_menu = array();

    /**
     * Returns an array with view variables for the ViewManager.
     *
     * @return mixed[] An array with strings as keys and mixed as value.
     */
    abstract public function getViewVariables();

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Sets the window title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Returns the meta description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**
     * @return mixed[]
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    /**
     * @param mixed[] $menu
     */
    public function setMenu($menu)
    {
        $this->_menu = $menu;
    }
}