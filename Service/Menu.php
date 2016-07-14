<?php

namespace SimpleMenuBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Menu
 * @package AppBundle\Utils
 */
class Menu
{
    /**
     * @var
     */
    private $currentRoute;

    /**
     * @var bool
     */
    private $hasGroups = FALSE;

    /**
     * @var array
     */
    private $menuArray = [];

    /**
     * @var \ArrayIterator
     */
    private $routeIterator;

    /**
     * Menu constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $routeCollection = $router->getRouteCollection();
        $this->routeIterator = $routeCollection->getIterator();
    }

    /**
     * Groups menu items together
     */
    private function doGrouping()
    {
        $unset = [];
        if (!empty($this->menuArray)) {
            foreach ($this->menuArray as $index => $item) {
                if (!empty($item['group'])) {
                    $this->hasGroups = TRUE;
                    $group = $item['group'];

                    if (empty($this->menuArray[$group])) {
                        $this->menuArray[$group] = [];
                    }
                    $this->menuArray[$group][] = $item;
                    $unset[] = $index;
                }
            }
        }
        // Unset the the original items.
        foreach ($unset as $index) {
            unset($this->menuArray[$index]);
        }
    }

    /**
     * Generated a nested array of items based on the parent attribute.
     */
    private function doNesting()
    {
        // @TODO: Implement a function to nest items.
    }

    /**
     * Sorts the menu item.
     */
    private function doSorting()
    {
        // @TODO: Implement a function to sort items based on weight.
        if ($this->hasGroups) {
            // For now sort alphabetically
            // Later should check params for group weights.
            ksort($this->menuArray);
        }
    }

    private function firstClass()
    {
        // @TODO: What about grouped menus
        if (!empty($this->menuArray[0])) {
            if (empty($this->menuArray[0]['class'])) {
                $this->menuArray[0]['class'] = 'first';
            }
            else {
                $this->menuArray[0]['class'] .= ' first';
            }
        }
    }

    private function lastClass()
    {
        // @TODO: What about grouped menus
        $num = count($this->menuArray);
        $last = $num -1;
        if (!empty($this->menuArray[$last])) {
            if (empty($this->menuArray[$last]['class'])) {
                $this->menuArray[$last]['class'] = 'last';
            }
            else {
                $this->menuArray[$last]['class'] .= ' last';
            }
        }
    }

    /**
     * Generates an menu array.
     *
     * @param $menuName
     * @return array
     */
    public function getMenu($menuName) {
        foreach ($this->routeIterator as $route => $item) {
            $defaults = $item->getDefaults();
            if (!empty($defaults)) {
                if (!empty($defaults['menu'])) {
                    if ($defaults['menu'] == $menuName) {
                        // Set active item.
                        $active = $this->currentRoute == $route ? TRUE : FALSE;
                        $this->menuArray[] = [
                            'route' => $route,
                            'path' => $item->getPath(),
                            'title' => empty($defaults['title'])? '' : $defaults['title'],
                            'class' => empty($defaults['class'])? '' : $defaults['class'],
                            'group' => empty($defaults['group'])? '' : $defaults['group'],
                            'active' => $active,
                        ];
                    }
                }
            }
        }

        // Do nesting.
        $this->doNesting();

        // Do grouping.
        $this->doGrouping();

        // Do sorting.
        $this->doSorting();

        // Add first and last classes.
        $this->firstClass();
        $this->lastClass();

        return $this->menuArray;
    }

    /**
     * @param RequestStack $request_stack
     */
    public function setRequest(RequestStack $request_stack) {
        $request = $request_stack->getMasterRequest();
        $this->currentRoute = $request->get('_route');
    }

    public function appendItem()
    {

    }
}