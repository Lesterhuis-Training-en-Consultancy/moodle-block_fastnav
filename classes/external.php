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
 * Class containing the external API functions
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 21/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav;

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;

/**
 * Class external.
 *
 * The external API
 *
 * @package   block_fastnav
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 21/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class external extends external_api {

    /**
     * Get items
     *
     * @param int $instanceid
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     */
    public static function get_items(int $instanceid): array {
        $warnings = [];
        $params = external_api::validate_parameters(self::get_items_parameters(), ['instanceid' => $instanceid]);
        $items = item::get_items([
            'blockinstanceid' => $params['instanceid'],
        ]);

        return [
            'open' => get_user_preferences('block_fastnav_open', false),
            'items' => array_map(static function($item) {
                return [
                    'id' => $item->get_id(),
                    'icon' => $item->icon(),
                    'name' => $item->name(),
                    'link' => $item->link(),
                ];
            }, $items),
            'warnings' => $warnings,
        ];
    }

    /**
     * Get item parameters
     *
     * @return external_function_parameters
     */
    public static function get_items_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'instanceid' => new external_value(PARAM_INT, 'Block instance id'),
            ]
        );
    }

    /**
     * Get item returns structure
     *
     * @return external_single_structure
     */
    public static function get_items_returns(): external_single_structure {
        return new external_single_structure(
            [
                'open' => new external_value(PARAM_BOOL, 'Check if fastnav is locked (open) state'),
                'items' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'The item ID.'),
                            'icon' => new external_value(PARAM_RAW, 'The item icon.'),
                            'name' => new external_value(PARAM_TEXT, 'The item name'),
                            'link' => new external_value(PARAM_URL, 'The item link'),
                        ]
                    )
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }

}
