<?php
/**
 * Holds MenuHelper
 *
 * @package WALib
 */
namespace WALib\Mvc\Views\Helper;
/**
 * A helper for setting up an ul/li structure for a menu.
 *
 * Standard this class is set up to work with bootstrap css.
 * Remind that this class only works with a hierarchy of one submenu step.
 *
 * @package WALib
 */
class MenuHelper
{
    /**
     * The class which will be used for the root <ul>.
     *
     * @var string
     */
    protected $_ulRootClass = 'nav';

    /**
     * The class which will be used for the active <li>.
     *
     * @var string
     */
    protected $_liActiveClass = 'active';

    /**
     * The class for the <li> which will be hold a dropdown.
     *
     * @var string
     */
    protected $_dropdownClass = 'dropdown';

    /**
     * The class for the <a> which will hold the link to toggle a dropdown.
     *
     * @var string
     */
    protected $_dropdownToggleClass = 'dropdown-toggle';

    /**
     * The class for the <ul> which holds the <li> entries of a dropdown.
     *
     * @var string
     */
    protected $_dropdownMenuClass = 'dropdown-menu';

    /**
     * Renders the menu using an array with menu entries.
     *
     * The navs array is an array which can include navKey/navName pairs or
     * navKey/navs pairs where navs has this keys and values:
     * "name" - The name of the nav, gonna be displayed.
     * "entries" - Subnavs of the nav. Only if subnavs exist.
     * "active" - Either of the nav is active. True or false.
     *
     * If "active" is in the array, "entries" wont be checked, "active" is only
     * for navs without subnavs.
     *
     * @param \WALib\Menu\Entry[] $navs
     */
    public function renderMenu($navs)
    {
        ?>
        <ul class="<?=$this->getUlRootClass()?>">
            <?php
            foreach($navs as $nav)
            {
                $this->renderLi($nav);
            }
            ?>
        </ul>
        <?php
    }

    /**
     * Renders a single <li> entry.
     *
     * @param \WALib\Menu\Entry $nav
     */
    public function renderLi($nav)
    {
        if($nav->getIsDropdown())
        {
            /*
             * The nav has entries and therefore is a dropdown nav.
             */
            ?>
            <li class="dropdown">
                <a href="#"
                   class="dropdown-toggle"
                   data-toggle="dropdown">
                    <?=$nav->getName()?>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <?php
                    /*
                     * Render the subnavs.
                     */
                    foreach($nav->getSubEntries() as $subNav)
                    {
                        $this->renderLi($subNav);
                    }
                    ?>
                </ul>
            </li>
            <?php
        }
        else
        {
            $liClass = '';

            if($nav->getActive())
            {
                $liClass = $this->getLiActiveClass();
            }

            ?>
            <li class="<?=$liClass?>">
                <a href="<?=$nav->getKey()?>"><?=htmlspecialchars($nav->getName())?></a>
            </li>
            <?php
        }
    }

    /**
     * @return string
     */
    public function getUlRootClass()
    {
        return $this->_ulRootClass;
    }

    /**
     * @param string $ulRootClass
     */
    public function setUlRootClass($ulRootClass)
    {
        $this->_ulRootClass = $ulRootClass;
    }

    /**
     * @return string
     */
    public function getLiActiveClass()
    {
        return $this->_liActiveClass;
    }

    /**
     * @param string $liActiveClass
     */
    public function setLiActiveClass($liActiveClass)
    {
        $this->_liActiveClass = $liActiveClass;
    }

    /**
     * @return string
     */
    public function getDropdownClass()
    {
        return $this->_dropdownClass;
    }

    /**
     * @param string $dropdownClass
     */
    public function setDropdownClass($dropdownClass)
    {
        $this->_dropdownClass = $dropdownClass;
    }

    /**
     * @return string
     */
    public function getDropdownToggleClass()
    {
        return $this->_dropdownToggleClass;
    }

    /**
     * @param string $dropdownToggleClass
     */
    public function setDropdownToggleClass($dropdownToggleClass)
    {
        $this->_dropdownToggleClass = $dropdownToggleClass;
    }

    /**
     * @return string
     */
    public function getDropdownMenuClass()
    {
        return $this->_dropdownMenuClass;
    }

    /**
     * @param string $dropdownMenuClass
     */
    public function setDropdownMenuClass($dropdownMenuClass)
    {
        $this->_dropdownMenuClass = $dropdownMenuClass;
    }
}