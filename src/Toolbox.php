<?php

namespace smtech\StMarksReflexiveCanvasLTI;

use Battis\HierarchicalSimpleCache;
use Battis\DataUtilities;
use smtech\StMarksSmarty\StMarksSmarty;

/**
 * Add HTML templating and basic caching to the ReflexiveCanvasLTI Toolbox
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 * @version v0.1
 */
class Toolbox extends \smtech\ReflexiveCanvasLTI\Toolbox {

    /**
     * Cache manager
     * @var HierarchicalSimpleCache
     */
    protected $cache;

    /**
     * St. Mark's-styled HTML templating
     * @var StMarksSmarty
     */
    protected $smarty;

    /**
     * @inheritDoc
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/smtech/reflexive-canvas-lti/master/doc/classes/smtech.ReflexiveCanvasLTI.Toolbox.html#method_loadConfiguration `smtech\ReflexiveCanvasLTI\Toolbox::loadConfiguration()`
     *
     * @param string $configFilePath
     * @param boolean $forceRecache (Optional, default `false`)
     * @return void
     */
    protected function loadConfiguration($configFilePath, $forceRecache = false) {
        $result = parent::loadConfiguration($configFilePath, $forceRecache);

        if ($forceRecache || empty($this->config('APP_PATH'))) {
            $this->config('APP_PATH', dirname($this->config('TOOL_CONFIG_FILE')));
        }

        if ($forceRecache || empty($this->config('APP_URL'))) {
            $this->config('APP_URL', DataUtilities::URLfromPath($this->config('APP_PATH')));
        }
    }

    /**
     * Update the cache manager
     *
     * @param HierarchicalSimpleCache $cache
     */
    public function setCache(HierarchicalSimpleCache $cache) {
        $this->cache = $cache;
    }

    /**
     * Get the cache manager
     *
     * @return [type] [description]
     */
    public function getCache() {
        if (empty($this->cache)) {
            $this->cache = new HierarchicalSimpleCache($this->getMySQL());
        }
        return $this->cache;
    }

    /**
     * Push a hierarchical layer to the cache
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/simplecache/master/doc/classes/Battis.HierarchicalSimpleCache.html#method_pushKey Pass-through to `HierarchicalSimpleCache::pushKey()`
     *
     * @param  string $layer
     * @return string
     */
    public function cache_pushKey($layer) {
        return $this->getCache()->pushKey($layer);
    }

    /**
     * Pop a hierarchical layer off of the cache
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/simplecache/master/doc/classes/Battis.HierarchicalSimpleCache.html#method_popKey Pass-through to `HierarchicalSimpleCache::popKey()`
     *
     * @return string|null
     */
    public function cache_popKey() {
        return $this->getCache()->popKey();
    }

    /**
     * Retrieve a cached value
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/simplecache/master/doc/classes/Battis.HierarchicalSimpleCache.html#method_getCache Pass-through to `HierarchicalSimpleCache::getCache()`
     *
     * @param string $key
     * @return mixed
     */
    public function cache_get($key) {
        return $this->getCache()->getCache($key);
    }

    /**
     * Set a cached value
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/simplecache/master/doc/classes/Battis.HierarchicalSimpleCache.html#method_setCache Pass-through to `HierarchicalSimpleCache::setCache()`
     *
     * @param string $key
     * @param mixed $data
     * @return boolean
     */
    public function cache_set($key, $data) {
        return $this->getCache()->setCache($key, $data);
    }

    /**
     * Update the HTML templating engine
     *
     * @param StMarksSmarty $smarty
     */
    public function setSmarty(StMarksSmarty $smarty) {
        $this->smarty = $smarty;
    }

    /**
     * Get the HTML templating engine
     *
     * @return StMarksSmarty
     */
    public function getSmarty() {
        if (empty($this->smarty)) {
            $this->smarty = StMarksSmarty::getSmarty();
            $this->smarty->addTemplateDir(__DIR__ . '/templates', 'starter-canvas-api-via-lti');
        	$this->smarty->setFramed(true);
        }
        return $this->smarty;
    }

    /**
     * Assign a value to a template variables
     *
     * @link http://www.smarty.net/docs/en/api.assign.tpl Pass-through to `Smarty::assign()`
     *
     * @param string $varname
     * @param mixed $var
     * @param boolean $noCache (Optional, default `false`)
     * @return void
     */
    public function smarty_assign($varname, $var, $noCache = false) {
        return $this->getSmarty()->assign($varname, $var, $noCache);
    }

    /**
     * Register another template directory
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/BootstrapSmarty/master/doc/classes/Battis.BootstrapSmarty.BootstrapSmarty.html#method_addTemplateDir Pass-through to `BootstrapSmarty::addTemplateDir()`
     *
     * @param string $template
     * @param string $key (Optional, default `null`)
     * @param boolean $isConfig (Optional, default `false`)
     */
    public function smarty_addTemplateDir($template, $key = null, $isConfig = false) {
        return $this->getSmarty()->assign($template, $key, $isConfig);
    }

    /**
     * Display an HTML template
     *
     * @link https://htmlpreview.github.io/?https://raw.githubusercontent.com/battis/BootstrapSmarty/master/doc/classes/Battis.BootstrapSmarty.BootstrapSmarty.html#method_display Pass-through to `BootstrapSmarty::display()`
     *
     * @param string $template (Optional, default `page.tpl`)
     * @param string $cache_id (Optional, default `null`)
     * @param string $compile_id (Optional, default, `null`)
     * @param string $parent (Optional, default `null`)
     * @return void
     */
    public function smarty_display($template = 'page.tpl', $cache_id = null, $compile_id = null, $parent = null) {
        return $this->getSmarty()->display($template, $cache_id, $compile_id, $parent);
    }
}
