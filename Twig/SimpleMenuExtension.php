<?php

namespace SimpleMenuBundle\Twig;

use SimpleMenuBundle\Service\Menu;

/**
 * Class SimpleMenuExtension
 * @package SimpleMenuBundle\Twig
 */
class SimpleMenuExtension extends \Twig_Extension
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * SimpleMenuExtension constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_menu', [$this, 'createMenu'], [
                'is_safe' => ['html'],
                'needs_environment' => TRUE
            ])
        );
    }

    /**
     * @param string $menuName
     * @param null $template
     * @return mixed
     */
    public function createMenu(\Twig_Environment $environment, $menuName = 'default', $template = NULL)
    {
        $menu = $this->menu->getMenu($menuName);
        //return $menu;
        return $environment->render(
            'views/menu.html.twig'
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'simple_menu_extension';
    }
}