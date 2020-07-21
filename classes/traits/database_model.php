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
namespace block_fastnav\traits;

use dml_exception;
use ReflectionClass;
use ReflectionException;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 *
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
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
     * @param $record
     */
    public function set_record(stdClass $record) : void {
        $this->record = $record;
    }

    /**
     * Get value from the record.
     *
     * @param string $name
     *
     * @return null|mixed
     */
    public function get(string $name = '') {

        if (property_exists($this->record, $name)) {
            return $this->record->$name;
        } else {
            debugging('Missing  ' . self::get_class() . '->record->' . $name);
        }

        return null;
    }

    /**
     * Should always be available.
     *
     * @return int
     */
    public function get_id() : int {
        return $this->record->id ?? 0;
    }

    /**
     * Set record data
     *
     * @param stdClass $formdata
     *
     * @return self
     */
    public function set(stdClass $formdata) : self {

        $formdata = (object)$formdata;
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
     * Create a new record
     *
     * @return int
     * @throws dml_exception
     */
    public function create() : int {
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
    public function exists() : bool {
        return !empty($this->record->id);
    }

    /**
     * Update record to the DB
     *
     * @return int
     *
     * @throws \dml_exception
     */
    public function update() : int {
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
     */
    public static function get_class() : string {

        try {
            $reflect = new ReflectionClass(static::class);
            $class = $reflect->getShortName();
        } catch (ReflectionException $exception) {
            die(' Fatal Error get_class() should always exists!');
        }

        return $class;
    }

    /**
     * Mark item as deleted
     *
     * @param bool $hard_delete
     *
     * @return bool
     * @throws \dml_exception
     */
    public function delete(bool $hard_delete = false) : bool {
        global $DB;

        if ($hard_delete) {
            $DB->delete_records(self::$table, ['id' => $this->get_id()]);

            return true;
        }
    }
}