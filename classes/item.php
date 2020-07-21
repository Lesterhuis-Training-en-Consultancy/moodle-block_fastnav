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
 * Class item (link items)
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav;

use block_fastnav\traits\database_model;
use context_block;
use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class item (link items)
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class item {

    use database_model;

    /**
     * @var string
     */
    static protected $table = 'block_fastnav';

    /**
     * linkitem constructor.
     *
     * @param int $id
     *
     * @throws dml_exception
     */
    public function __construct(int $id = 0) {
        global $DB;

        if (empty($id)) {
            return;
        }

        $record = $DB->get_record(self::$table, ['id' => $id], '*', MUST_EXIST);
        $this->set_record($record);
    }

    /**
     * @param int $instanceid
     *
     * @return int
     * @throws dml_exception
     */
    public function get_new_sortorder(int $instanceid = 0) : int {
        global $DB;

        return (int)$DB->get_field_select(self::$table,
                'MAX(sortorder)',
                "blockinstanceid = ?",
                [$instanceid]) + 1;
    }

    /**
     * @param stdClass $formdata
     * @param          $context
     *
     * @return bool
     * @throws dml_exception
     */
    public function save(stdClass $formdata, context_block $context) : bool {

        if (empty($formdata->id)) {
            $formdata->blockinstanceid = $context->instanceid;
            $formdata->sortorder = $this->get_new_sortorder($context->instanceid);
        }

        $this->set($formdata);
        $id = ($formdata->id > 0) ? $this->update() : $this->create();

        if (empty($id)) {
            return false;
        }

        file_save_draft_area_files($formdata->link_icon,
            $context->id,
            'block_fastnav',
            'link_icon',
            $id,
            helper::get_file_options());

        return true;
    }

    /**
     * @param array $filter
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_items(array $filter) : array {
        global $DB;
        $items = [];

        $rs = $DB->get_recordset(self::$table, $filter);
        foreach ($rs as $item) {

            $iteminstance = new self();
            $items[$item->id] = $iteminstance->set_record($item);
        }
        $rs->close();

        return $items;
    }

}