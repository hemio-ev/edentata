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
 * Description of SiteCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SiteCreate extends Window
{

    public function content($user, $serviceName)
    {
        if (!$user || !$serviceName)
            return $this->chooseServerAccess();
        else
            return $this->siteCreate($user, $serviceName);
    }

    protected function chooseServerAccess()
    {
        $window = $this->newWindow(_('New Site'));

        $users = $this->db->userSelect()->fetchAll();

        if (empty($users)) {
            $window->addChild(
                new gui\Hint(
                _('You need to create a server access first.')
                )
            );
        } elseif (count($users) === 1) {
            $data = array_pop($users);
            \hemio\edentata\Utils::htmlRedirect(
                $this->request->derive(
                    true
                    , $data['user']
                    , $data['service_name']
                )
            );
        } else {
            $listbox = new gui\Listbox();
            $window->addChild($listbox);
        }

        return $window;
    }

    protected function siteCreate($user, $serviceName)
    {
        $window = $this->newFormWindow(
            'site_create'
            , _('New Site')
            , $user.' @ '.$serviceName
            , _('Create')
        );

        $domain = new \hemio\form\FieldSelect('domain', _('Domain'));

        $switch = new gui\FieldSwitch('https-enabled', _('Enable HTTPS'));
        $switch->getControlElement()->addCssClass('display_control');

        $identifier = new \hemio\form\FieldText('identifier',
                                                _('Certificate Identifier'));
        $identifier->setDefaultValue((new \DateTime)->format('Y'));

        $window->getForm()->addChild($domain);
        $window->getForm()->addChild($switch);
        $window->getForm()->addChild($identifier);

        $domains = $this->db->availableDomains(['web'])->fetchAll();
        foreach ($domains as $data) {
            $domain->addOption($data['domain']);
        }


        $this->handleSubmit($window->getForm(), $user, $serviceName, $switch);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , $user
    , $serviceName
    , gui\FieldSwitch $switch)
    {
        if ($form->correctSubmitted()) {
            $siteParams = [
                'p_user' => $user,
                'p_service_name' => $serviceName
                ] + $form->getVal(['domain']);

            $httpsParams      = $form->getVal(['domain', 'identifier']);
            $siteUpdateParams = $form->getVal(['domain', 'identifier']);

            $this->db->beginTransaction();

            $this->db->siteCreate($siteParams);

            if ($switch->getValueUser()) {
                $this->db->httpsCreate($httpsParams);
                $this->db->siteUpdate($siteUpdateParams);
            }

            $this->db->commit();
        }
    }
}