<?php

namespace EssentialDots\Headless;

use EssentialDots\Headless\Exception\CLIException;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Nikola Stojiljkovic <nikola.stojiljkovic(at)essentialdots.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class Headless {
	const DEFAULT_DISPLAY_NUMBER = 100;
	const MAX_DISPLAY_NUMBER = 10000;
	const DEFAULT_DISPLAY_DIMENSIONS = '1280x1024x24';
	const XVFB_LAUNCH_TIMEOUT = 10;

	/**
	 * @var bool
	 */
	protected $debug = FALSE;

	/**
	 * @var string
	 */
	protected $oldDisplay;

	/**
	 * @var string
	 */
	protected $display;

	/**
	 * @var string
	 */
	protected $forceDisplay;

	/**
	 * @var string
	 */
	protected $dimensions;

	/**
	 * @var int
	 */
	protected $xvfbPid = 0;

	/**
	 * Constructor
	 */
	public function __construct($display = '', $dimensions = '') {
		if (!CLIUtility::applicationExists('Xvfb')) {
			throw new CLIException('Xvfb binary is missing.');
		}
		$this->forceDisplay = $display;
		$this->dimensions = $dimensions ? $dimensions : self::DEFAULT_DISPLAY_DIMENSIONS;
	}

	/**
	 * Starts the server
	 */
	public function start() {
		$this->oldDisplay = getenv('DISPLAY');
		$this->display = $this->forceDisplay ? $this->forceDisplay : self::DEFAULT_DISPLAY_NUMBER;
		$maxDisplayNumber = $this->forceDisplay ? $this->forceDisplay : self::MAX_DISPLAY_NUMBER;
		$xvfbPath = CLIUtility::pathTo('Xvfb');
		$success = false;

		while ($this->display <= $maxDisplayNumber) {
			$command = "$xvfbPath :{$this->display} -screen 0 {$this->dimensions} -ac >/dev/null 2>&1 & echo $!";
			if ($this->debug) {
				print_r("Running Xvfb on display port {$this->display}...\n");
			}
			$pid = trim(shell_exec($command));

			usleep(10);

			if (CLIUtility::isPidRunning($pid)) {
				$success = true;
				$this->xvfbPid = $pid;
				putenv("DISPLAY=:{$this->display}");
				if ($this->debug) {
					print_r("Xvfb successfully started on display port {$this->display}.\n");
				}
				register_shutdown_function(array($this,"registerShutdownHook"));
				break;
			} else {
				$this->display++;
			}
		}

		if (!$success) {
			throw new CLIException('Xvfb could not be started on the selected display port(s).');
		}
	}

	/**
	 * shutdown hook
	 *
	 * prevents unterminated Xvfb process
	 */
	public function registerShutdownHook() {
		$this->destroy();
	}

	/**
	 * Resets the environmental variable
	 */
	public function stop() {
		putenv("DISPLAY=:{$this->oldDisplay}");
	}

	/**
	 * Kills the Xvfb
	 */
	public function destroy() {
		$this->stop();
		if ($this->xvfbPid) {
			if ($this->debug) {
				print_r("Xvfb killed on display port {$this->display}.\n");
			}
			CLIUtility::killProcess($this->xvfbPid);
			$this->xvfbPid = 0;
		}
	}
}