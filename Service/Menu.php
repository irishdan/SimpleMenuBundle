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

    public function activeItem()
    {
        foreach ($this->menuArray as $key => $item) {
            $route = empty($item['route']) ? '' : $item['route'];
            $active = $this->currentRoute == $route ? TRUE : FALSE;
            $this->menuArray[$key]['active'] = $active;
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
                        $this->menuArray[] = [
                            'title' => empty($defaults['title'])? '' : $defaults['title'],
                            'route' => $route,
                            'href' => $item->getPath(),
                            'class' => empty($defaults['class'])? '' : $defaults['class'],
                            'group' => empty($defaults['group'])? '' : $defaults['group'],
                            'active' => FALSE,
                        ];
                    }
                }
            }
        }
        // Set active item.
        $this->activeItem();
        // $active = $this->currentRoute == $route ? TRUE : FALSE;

        // Do nesting.
        $this->doNesting();

        // Do grouping.
        $this->doGrouping();

        // Do sorting.
        $this->doSorting();

        // Ad first and last classes.
        $this->firstClass();
        $this->lastClass();

        return $this->menuArray;
    }

    /**
     * @param RequestStack $request_stack
     */
    public function setRequest(RequestStack $request_stack)
    {
        $request = $request_stack->getMasterRequest();
        $this->currentRoute = $request->get('_route');
    }

    public function appendItem($title = '', $route = '', $href = '', $class = '', $group ='')
    {
        $this->menuArray[] = [
            'title' => $title,
            'route' => $route,
            'href' => $href,
            'class' => $class,
            'group' => $group,
        ];

        $this->activeItem();

        // Ad first and last classes.
        $this->firstClass();
        $this->lastClass();

        return $this->menuArray;
    }
}