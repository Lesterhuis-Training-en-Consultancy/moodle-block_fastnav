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
 * @package   moodle-block_fastnav
 * @copyright 21/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\output;
defined('MOODLE_INTERNAL') || die;

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
 * @package   moodle-block_fastnav
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
     * @param renderer_base $output
     *
     * @return array|stdClass
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