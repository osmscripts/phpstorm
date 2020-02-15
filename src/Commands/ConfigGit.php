<?php

namespace OsmScripts\PhpStorm\Commands;

use OsmScripts\Core\Command;
use OsmScripts\Core\Project;
use OsmScripts\Core\Script;
use OsmScripts\PhpStorm\PhpStormProject;

/** @noinspection PhpUnused */

/**
 * `config:git` shell command class.
 *
 * @property Project $project Composer project in the current directory
 * @property PhpStormProject $phpstorm PhpStorm project
 *      in the current directory
 */
class ConfigGit extends Command
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'project': return new Project(['path' => $script->cwd]);
            case 'phpstorm': return new PhpStormProject(['path' => $script->cwd]);
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        $this->setDescription("Adds all Composer packages to PhpStorm Settings -> Version Control");
    }

    protected function handle() {
        $changed = false;

        foreach ($this->project->packages as $package) {
            if ($this->config($package->path)) {
                $this->output->writeln("'{$package->path}' added to PhpStorm Setting -> Version Control");
                $changed = true;
            }
        }

        if ($changed) {
            $this->phpstorm->saveXml('vcs');
        }
    }

    protected function config($path) {
        if (!is_dir("{$path}/.git")) {
            return false;
        }

        if (in_array($path, $this->phpstorm->getVcsPaths())) {
            return false;
        }

        $this->phpstorm->addVcsPath($path);
        return true;
    }
}