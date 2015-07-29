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

/**
 * Description of AliasCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AliasCreate extends Window
{

    public function content($siteAddr)
    {
        $site     = Utils::getHost($siteAddr);
        $sitePort = Utils::getPort($siteAddr);

        $window = $this->newFormWindow(
            'alias_create'
            , _('New Alias')
            , $siteAddr
            , _('Create')
        );

        $domain = new \hemio\form\FieldSelect('domain', _('Domain'));

        $window->getForm()->addChild($domain);

        $domains = $this->db->getUsableDomains('web', 'alias');
        foreach ($domains as $data) {
            $domain->addOption($data['domain']);
        }

        $this->handleSubmit($window->getForm(), $site, $sitePort);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $site, $sitePort)
    {
        if ($form->correctSubmitted()) {
            $params = [
                'p_site' => $site,
                'p_site_port' => $sitePort
                ] + $form->getVal(['domain']);

            $this->db->aliasCreate($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
