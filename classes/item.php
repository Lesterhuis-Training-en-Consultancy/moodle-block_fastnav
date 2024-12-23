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
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav;

use block_fastnav\traits\database_model;
use context_block;
use dml_exception;
use moodle_url;
use stdClass;

/**
 * Class item (link items)
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class item {

    use database_model;

    /**
     * @var string
     */
    static protected string $table = 'block_fastnav';

    /**
     * linkitem constructor.
     *
     * @param int $id
     */
    public function __construct(int $id = 0) {
        global $DB;

        if (empty($id)) {
            return;
        }

        $record = $DB->get_record(table: self::$table, conditions: ['id' => $id], strictness: MUST_EXIST);
        $this->set_record($record);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function name(): string {
        return $this->record->name ?? '';
    }

    /**
     * Get name
     *
     * @return string
     */
    public function id(): string {
        return $this->record->id ?? '';
    }

    /**
     * get_new_sortorder
     *
     * @param int $instanceid
     *
     * @return int
     */
    public function get_new_sortorder(int $instanceid = 0): int {
        global $DB;

        return (int) $DB->get_field_select(
            table: self::$table,
            return: 'MAX(sortorder)',
            select: "blockinstanceid = ?",
            params:[$instanceid]
            ) + 1;
    }

    /**
     * Save item
     *
     * @param stdClass $formdata
     * @param context_block $context
     *
     * @return bool
     */
    public function save(stdClass $formdata, context_block $context): bool {

        if (empty($formdata->id)) {
            $formdata->blockinstanceid = $context->instanceid; // Maybe we can only use context id.
            $formdata->contextid = $context->id;
            $formdata->sortorder = $this->get_new_sortorder($context->instanceid);
        }

        $this->set($formdata);
        $id = ($formdata->id > 0) ? $this->update() : $this->create();

        if (empty($id)) {
            return false;
        }

        file_save_draft_area_files(
            draftitemid: $formdata->link_icon,
            contextid: $context->id,
            component: 'block_fastnav',
            filearea: 'link_icon',
            itemid: $id,
            options: helper::get_file_options());

        return true;
    }

    /**
     * Used for getting formdata
     *
     * @return stdClass
     */
    public function get_data(): stdClass {

        return $this->record;
    }

    /**
     * Get item icon
     *
     * @return string
     */
    public function icon(): string {

        $fs = get_file_storage();
        $files = $fs->get_area_files(
            contextid: $this->get('contextid'),
            component: 'block_fastnav',
            filearea: 'link_icon',
            itemid: $this->get_id()
        );
        foreach ($files as $file) {

            if ($file->get_filename() === '.') {
                continue;
            }

            if ($file->is_valid_image() === false) {
                continue;
            }

            return moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
        }

        return '';
    }

    /**
     * Get link
     *
     * @return string
     */
    public function link(): string {
        return new moodle_url(url: $this->record->link ?? '');
    }

    /**
     * get_items
     *
     * @param array $conditions
     *
     * @return array
     */
    public static function get_items(array $conditions): array {
        global $DB;

        $items = [];

        $rs = $DB->get_recordset(table: self::$table, conditions: $conditions, sort: 'sortorder ASC');
        foreach ($rs as $item) {
            // Mapping.
            $items[$item->id] = (new self())->set_record($item);
        }
        $rs->close();

        return $items;
    }

}
