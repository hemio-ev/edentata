<?php
/*
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace hemio\edentata\module\web;

use hemio\edentata\gui;
use hemio\form;
use hemio\html;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Websites'), null, false);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->request->derive('site_create')
            , _('New Site')
            )
            , true
        );

        $listbox = new gui\Listbox();
        $sites   = $this->db->siteSelect()->fetchAll();
        foreach ($sites as $site) {
            $container = new form\Container();

            $container->addChild(
                new html\String($site['domain'])
            );

            $ul = new html\Ul();
            $container->addChild($ul);

            if ($site['https'] !== null) {
                $s = $ul->addLine(new html\String(_('HTTPS enabled')));
                $s->addChild(Utils::certSummary($site['domain'], $site['port'],
                                                $site['https'], $this->db));
            }

            // display port if it is not the default (80/443)
            if (!Utils::defaultPort($site['port'], $site['https']))
                $ul->addLine(new html\String(
                    sprintf(_('Port: %s'), $site['port'])));

            $ul->addLine(new html\String(
                sprintf(_('User: %s'), $site['user'])));
            $ul->addLine(new html\String(
                sprintf(_('Host: %s'), $site['service_name'])));

            $listbox->addLinkEntry(
                $this->request->derive('site_details',
                                       $site['domain'].':'.$site['port'])
                , $container
                , $site['backend_status']
            );
        }

        $window->addChild($listbox);

        return $window;
    }
}
