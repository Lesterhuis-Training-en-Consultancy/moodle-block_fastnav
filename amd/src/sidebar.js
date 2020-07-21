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
 * Javascript fast navigation interactive sidebar
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   moodle-block_fastnav
 * @copyright 17/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
define(['jquery', 'core/ajax', 'core/log', 'core/notification', 'core/templates', 'core/config'],
    function($, Ajax, Log, Notification, Templates, config) {
        'use strict';

        var sidebar = {

            options: {
                open: false,
                instanceid : 0
            },

            /**
             * Set options base on listed options
             * @param {object} options
             */
            setOptions: function(options) {
                "use strict";
                var key, vartype;
                for (key in this.options) {
                    if (this.options.hasOwnProperty(key) && options.hasOwnProperty(key)) {
                        // Casting to prevent errors.
                        vartype = typeof this.options[key];
                        if (vartype === "boolean") {
                            this.options[key] = Boolean(options[key]);
                        } else if (vartype === 'number') {
                            this.options[key] = Number(options[key]);
                        } else if (vartype === 'string') {
                            this.options[key] = String(options[key]);
                        } else {
                            this.options[key] = options[key];
                        }
                    }
                }
            },

            /**
             * start
             */
            start: function() {
                Log.debug('block_fastnav/sidebar: start()');
                Log.debug(this.options);

                // Load template.
                this.loadNavigation()
            },

            /**
             * Add sidebar
             */
            loadNavigation: function() {
                var promises = Ajax.call([{
                    methodname: 'block_fastnav_get_items',
                    args: {
                        instanceid: this.options.instanceid,
                    }
                }]);
``
                promises[0].done(function(response) {
                    Templates.render('mod_threesixo/question_list', response)
                        .done(function(html) {
                            $('body').prepend(html)
                            this.loadEvents();
                        }) .fail(Notification.exception);

                }).fail(Notification.exception);
            },

            /**
             * Check for some DOM events.
             */
            loadEvents : function() {

            }
        }

        return {

            /**
             *
             * @param {object} params
             */
            init: function(params) {
                sidebar.setOptions(params);
                sidebar.start();
            }
        }
    });