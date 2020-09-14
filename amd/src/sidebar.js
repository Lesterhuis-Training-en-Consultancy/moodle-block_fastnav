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
 * @package   block_fastnav
 * @copyright 17/07/2020 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
define(['jquery', 'core/ajax', 'core/log', 'core/notification', 'core/templates', 'core/config'],
    function($, Ajax, Log, Notification, Templates) {
        'use strict';

        var sidebar = {

            options: {
                open: false,
                instanceid: 0
            },

            /**
             * Set options base on listed options
             * @param {object} options
             */
            setOptions: function(options) {
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
             * Start
             */
            start: function() {
                Log.debug('block_fastnav/sidebar: start()');
                Log.debug(this.options);

                // Load template.
                this.loadNavigation();
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
                promises[0].done(function(response) {

                    if (response.items.length === 0) {
                        Log.debug('block_fastnav/sidebar: no items.');
                        return;
                    }

                    Templates.render('block_fastnav/block_item_list', response)
                        .done(function(html) {
                            $('body').prepend('<nav class="block-fastnav-sidebar">' + html + '</nav>');
                            sidebar.loadEvents();
                        }).fail(Notification.exception);

                }).fail(Notification.exception);
            },

            /**
             * Open or close the fastnav sidebar
             */
            openOrClose: function() {

                var $sidebar = $('.block-fastnav-sidebar');
                if ($sidebar.width() !== 80) {
                    $sidebar.animate({
                        width: '80px',
                    }, 300, function() {
                        Log.debug('block_fastnav/sidebar: Animation complete.');
                        $sidebar.find('.fa-arrow-left').removeClass('fa-arrow-left').addClass('fa-arrow-right');
                    });
                    return;
                }

                $sidebar.animate({
                    width: 0,
                }, 300, function() {
                    Log.debug('block_fastnav/sidebar: Animation complete.');
                    $sidebar.find('.fa-arrow-right').addClass('fa-arrow-left').removeClass('fa-arrow-right');
                });
            },

            /**
             * Check for some DOM events.
             */
            loadEvents: function() {
                Log.debug('block_fastnav/sidebar: loadEvents()');

                if ($('.block-fastnav-lock.active').length) {
                    sidebar.openOrClose();
                }

                $('body').on('mouseover', '.block-fastnav-opener', function() {
                    sidebar.openOrClose();
                });

                $('body').on('click', '.block-fastnav-lock', function(event) {

                    // Check current status.
                    var $el = $(event.currentTarget);
                    var locked = $el.hasClass('active');
                    Log.debug('block_fastnav/sidebar: locked ', locked);

                    if (locked) {
                        $el.removeClass('active');
                        sidebar.updateOpenPreference('0');
                        sidebar.openOrClose();
                        return;
                    }

                    $el.addClass('active');
                    sidebar.updateOpenPreference('1');
                });

                // Reload bootstrap tooltip.
                try {
                    $('.block-fastnav-sidebar a').tooltip();
                } catch (e) {
                    Log.debug('block_fastnav/sidebar: bootstrap tooltip not available ');
                }
            },

            /**
             *
             * @param {string} status
             */
            updateOpenPreference: function(status) {
                M.util.set_user_preference('block_fastnav_open', status);
            }
        };

        return {

            /**
             *
             * @param {object} params
             */
            init: function(params) {
                sidebar.setOptions(params);
                sidebar.start();
            }
        };
    });