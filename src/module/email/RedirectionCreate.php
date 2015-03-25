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

namespace hemio\edentata\module\email;

use hemio\edentata\gui;
use hemio\form;

/**
 * Description of RedirectCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class RedirectionCreate extends Window
{

    public function content()
    {
        $window = $this->newFormWindow(
            'create_redirect'
            , _('New Redirection')
            , null, _('Create')
        );

        $fieldsetFrom = new gui\Fieldset(_('Redirection from'));
        $from         = new gui\FieldEmailWithSelect();

        $fieldsetTo = new gui\Fieldset(_('Redirection to'));
        $to         = new form\FieldEmail('to', _('Email Address'));
        $to->setRequired();

        $window->getForm()
            ->addChild($fieldsetFrom)
            ->addChild($from);

        $window->getForm()
            ->addChild($fieldsetTo)
            ->addChild($to);

        $domains = $this->db->getPossibleDomains();
        while ($domain  = $domains->fetch()) {
            $from->getDomain()->addOption($domain['domain'], $domain['domain']);
        }

        $this->handleSubmit($window->getForm(), $from, $to);

        return $window;
    }

    public function handleSubmit(gui\FormPost $form,
                                 gui\FieldEmailWithSelect $from,
                                 form\FieldEmail $to)
    {
        if ($form->correctSubmitted()) {
            $args                  = $form->getVal(['localpart', 'domain']);
            $args['p_destination'] = $to->getValueUser();

            $this->db->redirectionCreate($args);

            throw new \hemio\edentata\exception\Successful();
        }
    }
}
