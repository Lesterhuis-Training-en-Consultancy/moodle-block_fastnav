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
 * Output link items
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 21/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\output;

use ArrayIterator;
use block_fastnav\item;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Class output_items
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 21/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class output_items implements renderable, templatable {

    /**
     * @var \context_block
     */
    private $context;

    /**
     * output_items constructor.
     *
     * @param \context_block $context
     */
    public function __construct(\context_block $context) {
        $this->context = $context;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     *
     * @return stdClass|array
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output) {
        $dataobject = new stdClass();

        $dataobject->items = new ArrayIterator(item::get_items([
            'blockinstanceid' => $this->context->instanceid,
        ]));

        return $dataobject;
    }

}
