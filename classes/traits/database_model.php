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
 * Trait containing database model functions
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\traits;
defined('MOODLE_INTERNAL') || die;

use coding_exception;
use dml_exception;
use Exception;
use ReflectionClass;
use ReflectionException;
use stdClass;

/**
 * Database model trait
 *  - easy way to work with database records
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
trait database_model {

    /**
     * The DB record of the item
     *
     * @var stdClass|bool
     */
    protected $record = false;

    /**
     * Set record
     *
     * @param stdClass $record
     *
     * @return $this
     */
    public function set_record(stdClass $record): self {
        $this->record = $record;

        return $this;
    }

    /**
     * Get value from the record.
     *
     * @param string $name
     *
     * @return null|mixed
     * @throws Exception
     */
    public function get(string $name = '') {

        if (property_exists($this->record, $name)) {
            return $this->record->$name;
        }

        debugging('Missing  ' . self::get_class() . '->record->' . $name);

        return null;
    }

    /**
     * Get record id
     *
     * @return int
     */
    public function get_id(): int {
        return $this->record->id ?? 0;
    }

    /**
     * Set record data
     *
     * @param stdClass $formdata
     *
     * @return self
     */
    public function set(stdClass $formdata): self {

        $formdata = (object) $formdata;
        if (empty($this->record)) {
            $this->record = new stdClass();
        }

        foreach ($formdata as $k => $v) {

            if (isset($v['text'])) {
                $v = $v['text'];
            }

            $this->record->$k = $v;
        }

        return $this;
    }

    /**
     * Create a record
     *
     * @return int
     * @throws dml_exception
     */
    public function create(): int {
        global $DB;
        $obj = new stdClass();
        unset($this->record->id, $this->record->submitbutton);

        foreach ($this->record as $k => $v) {
            $obj->$k = $v;
        }

        $obj->created_at = time();
        $this->record->id = $DB->insert_record(self::$table, $obj);

        return $this->record->id;
    }

    /**
     * Check if record is empty
     *
     * @return bool
     */
    public function exists(): bool {
        return !empty($this->record->id);
    }

    /**
     * Update record to the DB
     *
     * @return int
     *
     * @throws \dml_exception
     */
    public function update(): int {
        global $DB;
        $obj = new stdClass();
        $obj->id = $this->record->id;

        foreach ($this->record as $k => $v) {
            $obj->$k = $v;
        }
        $DB->update_record(self::$table, $obj);

        return $obj->id;
    }

    /**
     * Get the class of the item
     *
     * @return string
     * @throws Exception
     */
    public static function get_class(): string {

        try {
            $reflect = new ReflectionClass(static::class);
            $class = $reflect->getShortName();
        } catch (ReflectionException $exception) {
            throw new Exception(' Fatal Error get_class() should always exists!');
        }

        return $class;
    }

    /**
     * Delete a record
     *
     * @return bool
     * @throws \dml_exception
     */
    public function delete(): bool {
        global $DB;

        $DB->delete_records(self::$table, ['id' => $this->get_id()]);

        return true;
    }

    /**
     * Moves a record
     *
     * @param int $direction    move direction: +1 or -1
     * @param array $conditions used for getting matching records
     *
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     */
    public function change_sortorder(int $direction, array $conditions = []): bool {
        global $DB;

        if ($direction !== -1 && $direction !== 1) {
            throw new coding_exception('Second argument in change_sortorder() can be only 1 or -1');
        }

        $records = $DB->get_records(self::$table, $conditions, 'sortorder ASC');

        $keys = array_keys($records);
        $idx = array_search($this->get_id(), $keys, false);

        if ($idx === false || $idx + $direction < 0 || $idx + $direction >= count($records)) {
            return false;
        }
        $otherid = $keys[$idx + $direction];

        $DB->update_record(self::$table, (object) [
            'id' => $this->get_id(),
            'sortorder' => $idx + $direction,
        ]);

        $DB->update_record(self::$table, (object) [
            'id' => $otherid,
            'sortorder' => $idx,
        ]);

        return true;
    }

}
