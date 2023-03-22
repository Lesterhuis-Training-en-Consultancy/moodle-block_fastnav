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
 * Table link overview
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\table;

use block_fastnav\item;
use coding_exception;
use html_writer;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;
use Traversable;

/**
 * Class table_links
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class table_links extends table_sql {

    /**
     * table_links constructor.
     *
     * @param string $uniqueid
     * @param int $blockinstanceid
     */
    public function __construct(string $uniqueid, int $blockinstanceid) {

        parent::__construct($uniqueid);

        $this->sql = new stdClass();
        $this->sql->fields = 'i.*';
        $this->sql->from = '{block_fastnav} i';
        $this->sql->where = 'blockinstanceid = :blockinstanceid';
        $this->sql->params = [
            'blockinstanceid' => $blockinstanceid,
        ];

        $this->countsql = 'SELECT COUNT(*) FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where;
        $this->countparams = $this->sql->params;
    }

    /**
     * Always ordered by sortorder
     *
     * @return string fragment that can be used in an ORDER BY clause.
     */
    public function get_sql_sort(): string {
        return 'sortorder ASC';
    }

    /**
     * Take the data returned from the db_query and go through all the rows
     * processing each col using either col_{columnname} method or other_cols
     * method or if other_cols returns NULL then put the data straight into the
     * table.
     *
     * After calling this function, don't forget to call close_recordset.
     */
    public function build_table(): void {

        if ($this->rawdata instanceof Traversable && !$this->rawdata->valid()) {
            return;
        }
        if (!$this->rawdata) {
            return;
        }

        foreach ($this->rawdata as $row) {
            $row = (new item)->set_record($row);

            $formattedrow = $this->format_row($row);
            $this->add_data_keyed($formattedrow,
                $this->get_row_class($row));
        }
    }

    /**
     * col_icon
     *
     * @param item $item
     *
     * @return string
     * @throws coding_exception
     */
    public function col_icon(item $item): string {
        return html_writer::img($item->icon(), '');
    }

    /**
     * col_link
     *
     * @param item $item
     *
     * @return string
     * @throws moodle_exception
     */
    public function col_link(item $item): string {
        return $item->link();
    }

    /**
     * col_name
     *
     * @param item $item
     *
     * @return string
     */
    public function col_name(item $item): string {
        return $item->name();
    }

    /**
     * col_id
     *
     * @param item $item
     *
     * @return string
     */
    public function col_id(item $item): string {
        return $item->id();
    }

    /**
     * col_action
     *
     * @param item $item
     *
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_action(item $item): string {
        global $PAGE;
        if ($this->is_downloading()) {
            return '';
        }

        $params = array_merge($PAGE->url->params(), [
            'id' => $item->id(),
        ]);

        $list = [
            html_writer::link(new moodle_url($PAGE->url->get_path(), ['action' => 'down'] + $params),
                '<i class="fa fa-arrow-down"></i>', ['class' => 'btn btn-sm btn-primary']),

            html_writer::link(new moodle_url($PAGE->url->get_path(), ['action' => 'up'] + $params),
                '<i class="fa fa-arrow-up"></i>', ['class' => 'btn btn-sm btn-primary']),

            html_writer::link(new moodle_url($PAGE->url->get_path(), ['action' => 'edit'] + $params),
                get_string('btn:edit', 'block_fastnav'), ['class' => 'btn btn-sm btn-primary']),

            html_writer::link(new moodle_url($PAGE->url->get_path(), ['action' => 'delete'] + $params),
                '<i class="fa fa-trash"></i>', ['class' => 'btn btn-danger btn-sm delete']),
        ];

        return implode(' ', $list);
    }

}
