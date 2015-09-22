<?php

/**
 * @package patternlab
 */
class PatternLab extends Controller {
    public function init() {
		parent::init();
    }

	public function index() {
        Requirements::css(PATTERN_DIR . '/css/patternlab.css');
		if(!Director::isDev() && !Permission::check('CMS_ACCESS_CMSMain')) {
			return Security::permissionFailure($this);
		}

		return $this->renderWith(array(
			__CLASS__,
			'Page'
		));
	}

	public function getPatterns() {
        $config = SiteConfig::current_site_config();
		if ($config->Theme) {
			Config::inst()->update('SSViewer', 'theme_enabled', true);
			Config::inst()->update('SSViewer', 'theme', $config->Theme);
		}
		$theme = $config->Theme;

		$manifest = SS_TemplateLoader::instance()->getManifest();

		$templates = array();

		foreach($manifest->getTemplates() as $templateName => $templateInfo) {
			$themeexists = $theme && isset($templateInfo['themes'][$theme]) && isset($templateInfo['themes'][$theme]['Patterns']);
            //always use project template files, and grab template files if not already used
			if ($themeexists && !isset($templates[$templateName])) {
				$templates[$templateName] = array(
                    'Name' => $templateName,
                    'Layout' => $this->renderWith(array($templateName))
                );
			}
		}

		ksort($templates);

		return new ArrayList($templates);
	}
}