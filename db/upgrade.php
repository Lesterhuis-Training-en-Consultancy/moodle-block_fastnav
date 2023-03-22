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
 * Upgrade database
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_fastnav
 * @copyright 20/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

/**
 * Execute on plugin upgrade
 *
 * @param int $oldversion
 *
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_block_fastnav_upgrade($oldversion) {

    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2020072000) {

        // Define field sort to be added to block_fastnav.
        $table = new xmldb_table('block_fastnav');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '11', null, null, null, '0', 'blockinstanceid');

        // Conditionally launch add field sort.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fastnav savepoint reached.
        upgrade_block_savepoint(true, 2020072000, 'fastnav');
    }

    if ($oldversion < 2020072100) {

        // Define field contextid to be added to block_fastnav.
        $table = new xmldb_table('block_fastnav');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'blockinstanceid');

        // Conditionally launch add field contextid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fastnav savepoint reached.
        upgrade_block_savepoint(true, 2020072100, 'fastnav');
    }

    return true;
}
