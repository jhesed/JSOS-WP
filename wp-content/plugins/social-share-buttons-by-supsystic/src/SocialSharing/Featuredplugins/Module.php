<?php


class SocialSharing_Featuredplugins_Module extends SocialSharing_Core_BaseModule
{

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        $environment = $this->getEnvironment();
        $config = $environment->getConfig();

        $this->registerMenu();

        $prefix = $config->get('hooks_prefix');

        add_action($prefix . 'after_ui_loaded', array(
            $this, 'loadAssets'
        ));
    }

    /**
     * Loads the assets required by the module
     */
    public function loadAssets(SocialSharing_Ui_Module $ui)
    {
		if($this->getEnvironment()->isModule('featuredplugins')) {
			$ui->addAsset(
				$ui->create('style', 'sss-bootstrap-simple')
					->setModuleSource($this, 'css/bootstrap-simple.css')
					->setHookName('admin_enqueue_scripts')
			);
			$ui->addAsset(
				$ui->create('style', 'sss-featured-plugins')
					->setModuleSource($this, 'css/admin.featured-plugins.css')
					->setHookName('admin_enqueue_scripts')
			);
		}
    }

    public function registerMenu()
    {
        $menu = $this->getMenu();
        $plugin_menu = $this->getConfig()->get('plugin_menu');
        $capability = $plugin_menu['capability'];
        $submenu = $menu->createSubmenuItem();

        $submenu->setCapability($capability)
            ->setMenuSlug('supsystic-social-sharing&module=featuredplugins')
            ->setMenuTitle($this->translate('Featured Plugins'))
            ->setPageTitle($this->translate('Featured Plugins'))
            ->setModuleName('featuredplugins');
		// Avoid conflicts with old vendor version
		if(method_exists($submenu, 'setSortOrder')) {
			$submenu->setSortOrder(99);
		}

        $menu->addSubmenuItem('featuredplugins', $submenu);
    }
} 