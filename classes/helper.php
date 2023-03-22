<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Helper functions.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav;

/**
 * Class helper.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 16/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class helper {

    /**
     * Show block and sidebar on activity pages.
     */
    public const SHOW_BLOCK_AND_SIDEBAR = 0;

    /**
     * Show sidebar only on activity pages.
     */
    public const SHOW_SIDEBAR = 1;

    /**
     * Show block only on activity pages.
     */
    public const SHOW_BLOCK_ONLY = 2;

    /**
     * Get file upload information.
     *
     * @param int $maxfiles
     *
     * @return array
     */
    public static function get_file_options(int $maxfiles = 1): array {
        global $CFG;

        return [
            'maxbytes' => $CFG->maxbytes,
            'subdirs' => 0,
            'maxfiles' => $maxfiles,
            'accepted_types' => ['.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'],
        ];
    }

}
