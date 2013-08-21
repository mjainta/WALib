<?php
/**
 * Holds ControllerInterface.
 *
 * @package WALib
 */
namespace WALib\Mvc\Controllers;
/**
 * Interface for controller classes.
 *
 * Every controller for the app should use this interface.
 *
 * @package WALib
 */
interface ControllerInterface
{
    /**
     * The standard action, will be called if no action was defined by the request.
     */
    public function indexAction();
}