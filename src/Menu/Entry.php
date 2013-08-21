<?php
/**
 * Holds Entry.
 *
 * @package WALib
 */
namespace WALib\Menu;
/**
 * Represents an entry for the menu.
 *
 * @package WALib
 */
class Entry
{
    /**
     * The key for the menu entry.
     *
     * Used to identify the controllers and actions for the URL generation.
     *
     * @var string
     */
    protected $_key = '';

    /**
     * The displayed name for the menu entry.
     *
     * @var string
     */
    protected $_name = '';

    /**
     * Tells whether the menu entry is active.
     *
     * An active menu entry gets displayed highlighted.
     *
     * @var boolean
     */
    protected $_active = false;

    /**
     * Tells whether the menu entry is a dropdown with more entries.
     *
     * @var boolean
     */
    protected $_isDropdown = false;

    /**
     * The sub entries for the menu.
     *
     * Used if the menu entry is a dropdown.
     *
     * @var \WALib\Menu\Entry[]
     */
    protected $_subEntries = array();

    /**
     * Sets necessary variables for the menu entry.
     *
     * @param string $key
     * @param string $name
     * @param boolean $isDropdown
     * @param \WALib\Menu\Entry[] $subEntries
     */
    public function __construct($key, $name, $isDropdown = false, $subEntries = array())
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_isDropdown = $isDropdown;
        $this->_subEntries = $subEntries;
    }

    /**
     * Returns the key for the menu entry.
     *
     * Used to identify the controllers and actions for the URL generation.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Sets the key for the menu entry.
     *
     * Used to identify the controllers and actions for the URL generation.
     *
     * @param string $key
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * Returns the displayed name for the menu entry.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the displayed name for the menu entry.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns whether the menu entry is active.
     *
     * An active menu entry gets displayed highlighted.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->_active;
    }

    /**
     * Sets whether the menu entry is active.
     *
     * An active menu entry gets displayed highlighted.
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->_active = $active;
    }

    /**
     * Returns whether the menu entry is a dropdown with more entries.
     *
     * @return boolean
     */
    public function getIsDropdown()
    {
        return $this->_isDropdown;
    }

    /**
     * Sets whether the menu entry is a dropdown with more entries.
     *
     * @param boolean $isDropdown
     */
    public function setIsDropdown($isDropdown)
    {
        $this->_isDropdown = $isDropdown;
    }

    /**
     * Returns the sub entries for the menu.
     *
     * Used if the menu entry is a dropdown.
     *
     * @return \WALib\Menu\Entry[]
     */
    public function getSubEntries()
    {
        return $this->_subEntries;
    }

    /**
     * Sets the sub entries for the menu.
     *
     * Used if the menu entry is a dropdown.
     *
     * @param \WALib\Menu\Entry[] $subEntries
     */
    public function setSubEntries($subEntries)
    {
        $this->_subEntries = $subEntries;
    }
}