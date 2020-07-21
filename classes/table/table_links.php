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
 * @package   moodle-block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_fastnav\table;
defined('MOODLE_INTERNAL') || die();

/**
 * Class table_links
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 */
class table_links extends \table_sql {

    /**
     * @param string $uniqueid
     * @param int    $blockinstanceid
     */
    public function __construct(string $uniqueid , int $blockinstanceid) {

        parent::__construct($uniqueid);

        $this->sql = new \stdClass();
        $this->sql->fields = 'i.*';
        $this->sql->from = '{block_fastnav} i';
        $this->sql->where = 'blockinstanceid = :blockinstanceid';
        $this->sql->params = [
            'blockinstanceid' => $blockinstanceid
        ];

        $this->countsql = 'SELECT COUNT(*) FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where;
        $this->countparams = $this->sql->params;
    }

    public function col_action(\stdClass $row) {
        return '';
    }

}