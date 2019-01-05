<?php
/**
 * Copyright 2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl.
 *
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @license  https://www.horde.org/licenses/bsd BSD
 * @package  GitTools
 */

namespace Horde\GitTools\Action\Git;

use Horde\GitTools\Cli;
use Horde\GitTools\Exception;

/**
 * Permform arbitrary command(s) in all repositories.
 *
 * @author    Michael J Rubinsky <mrubinsk@horde.org>
 * @category  Horde
 * @copyright 2017 Horde LLC
 * @license   https://www.horde.org/licenses/bsd BSD
 * @package   GitTools
 */
class Command extends Base
{

    protected $_failures = array();

    /**
     * Pulls and rebases.
     *
     * @param  array $commands  An array of commands. All ommands are performed
     *                          in each repository before moving on to the next.
     *
     */
    public function run(array $commands = array())
    {
        $results = array();

        // Ensure the base directory exists.
        if (!strlen($this->_params['git_base']) ||
            !file_exists($this->_params['git_base'])) {
            throw new Exception("Target directory for git checkouts does not exist.");
        }

        $this->_dependencies->getOutput()->info('Handling git command.');
        foreach (scandir($this->_params['git_base']) as $dir) {
            if (!$this->_includeRepository($this->_params['git_base'] . '/' . $dir)) {
                continue;
            }
            foreach ($commands as $cmd) {
                if (empty($this->_params['quiet'])) {
                    $this->_dependencies->getOutput()->plain(
                        '   >>>GIT COMMAND: ' . $cmd
                    );
                }
                $results[$dir] = $this->_callGit($cmd, $this->_params['git_base'] . '/' . $dir);

                if (empty($this->_params['quiet'])) {
                    $this->_dependencies->getOutput()->plain(
                        '   >>>RESULTS: ' . implode("\n", $results[$dir])
                    );
                }

                $this->_dependencies->getOutput()->ok('Repository: ' . $dir . ' completed.');
            }
        }

        foreach ($results as $name => $result) {
            $this->_dependencies->getOutput()->bold($name);
            $this->_dependencies->getOutput()->plain(implode("\n", $result));
        }
    }

}
