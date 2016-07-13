# SimpleMenuBundle

A bundle to create menus from route definitions in routing.yml. 

Menu items are created by adding a menu and title key value pair to the defaults array of a routing item:

```php
admin_sample_1:
    path:     /admin/sample1
    defaults:
        _controller: AppBundle:Admin:sample1
        title: 'Dashboard'
        class: 'fa-dashboard'
        menu: admin

admin_sample_2:
    path:     /admin/sample2
    defaults:
        _controller: AppBundle:Admin:sample2
        menu: admin
        title: 'Sample 2'
        class: 'fa-bold'
        group: 'Settings'

admin_sample_3:
    path:     /admin/sample3
    defaults:
        _controller: AppBundle:Admin:sample3
        menu: admin
        title: 'Sample 3'
        class: 'fa-book' 
```
By calling the simple_menu.menu service and passing in a menu name like so:

```php
$menu = $this->get('simple_menu.menu')->getMenu('admin');
```

All routing items with defaults menu set to 'admin' are used to generate a menu.


1: Installation
---------------------------

Clone the repo to your src directory.

Step 2: Enable the Bundle
-------------------------

Enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new SimpleMenuBundle\SimpleMenuBundle(),
        );
        // ...
    }
    // ...
}
```

Import the service definition in your config.yml file
```php
imports:
    - { resource: "@SimpleMenuBundle/Resources/config/services.yml" }
```

Note: work in progress