<?php

namespace OsmScripts\PhpStorm;

use OsmScripts\Core\Files;
use OsmScripts\Core\Object_;
use OsmScripts\Core\Script;

/**
 * @property string $path
 * @property \SimpleXMLElement $vcs
 * @property Files $files @required Helper for generating files.
 */
class PhpStormProject extends Object_
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'vcs': return $this->loadXml('vcs');
            case 'files': return $script->singleton(Files::class);
        }

        return parent::default($property);
    }
    #endregion

    protected function loadXml($config) {
        $filename = ".idea/{$config}.xml";

        if (!is_file($filename)) {
            return null;
        }

        return simplexml_load_string(file_get_contents($filename));
    }

    public function saveXml($config) {
        $filename = ".idea/{$config}.xml";

        if (!isset($this->$config)) {
            $this->files->delete($filename);
            return;
        }

        $this->files->save($filename, $this->$config->asXML());
    }

    public function addProjectDirVariable($path) {
        return $path ? "\$PROJECT_DIR\$/{$path}" : '$PROJECT_DIR$';
    }


}