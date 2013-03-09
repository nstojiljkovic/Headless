<?php

namespace EssentialDots\Headless;

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

class CLIUtility {

	/**
	 * @param string $app
	 * @return bool
	 */
	public static function applicationExists($app) {
		return trim(`which $app`)!="";
	}

	/**
	 * @param string $app
	 * @return string
	 */
	public static function pathTo($app) {
		return trim(`which $app`);
	}

	/**
	 * @param $pid
	 * @return bool
	 */
	public static function isPidRunning($pid) {
		exec('ps '.$pid,$output,$result);

		if( count( $output ) == 2 ) {
			return true; //daemon is running
		} else {
			return false;
		}
	}

	/**
	 * @param $pidFilename
	 */
	public static function readPid($pidFilename) {
		// @todo: implement this :)
	}

	/**
	 * @param $command
	 * @param $pidFilename
	 * @param string $logFilename
	 */
	public static function forkProcess($command, $pidFilename, $logFilename='/dev/null') {
		// @todo: implement this :)
	}

	/**
	 * @param $pid
	 */
	public static function killProcess($pid) {
		exec("kill $pid", $processState);
	}
}