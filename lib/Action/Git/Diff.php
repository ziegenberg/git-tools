<?php
/**
 * Copyright 2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  https://www.horde.org/licenses/bsd BSD
 * @package  GitTools
 */

namespace Horde\GitTools\Action\Git;

use Horde\GitTools\Cli;
use Horde\GitTools\Exception;

/**
 * Shows diffs of all locally checked out repositories.
 *
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2017 Horde LLC
 * @license   https://www.horde.org/licenses/bsd BSD
 * @package   GitTools
 */
class Diff extends Base
{
    /**
     * Outputs diffs of all available locally checkout out repositories.
     *
     * @param  string  $package  The repository name.
     */
    public function run()
    {
        // Ensure the base directory exists.
        if (!strlen($this->_params['git_base']) ||
            !file_exists($this->_params['git_base'])) {
            throw new Exception(
                "Target directory for git checkouts does not exist."
            );
        }

        $this->_dependencies->getOutput()
            ->yellow('Showing diffs of repositories.');
        foreach (scandir($this->_params['git_base']) as $dir) {
            if (!$this->_includeRepository($this->_params['git_base'] . '/' . $dir)) {
                continue;
            }
            $results = $this->_callGit(
                '-c color.ui=always diff',
                $this->_params['git_base'] . '/' . $dir
            );
            if (!$results[0]) {
                continue;
            }
            $this->_dependencies->getOutput()->plain('');
            $this->_dependencies->getOutput()->blue('Diff of ' . $dir);
            $this->_dependencies->getOutput()->plain($results[0]);
        }
    }

}
