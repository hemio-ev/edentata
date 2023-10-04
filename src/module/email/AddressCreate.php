<?php
/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
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

/**
 * Description of Create
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class AddressCreate extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Create Email Address'));

        $selecting = new gui\Selecting(_('Delivery for new Address'));

        $reqMailbox  = $this->module->request->derive('mailbox_create');
        $strMailbox  = _('Create new mailbox for incoming emails');
        $linkMailbox = $selecting->addLink($reqMailbox, $strMailbox);

        $reqAlias  = $this->module->request->derive('alias_create');
        $strAlias  = _('Deliver emails to existing mailbox (alias)');
        $linkAlias = $selecting->addLink($reqAlias, $strAlias);

        $reqRedirect = $this->module->request->derive('redirection_create');
        $strRedirect = _('Deliver emails to external mailbox (redirection)');
        $selecting->addLink($reqRedirect, $strRedirect);

        if ($this->db->mailboxSelect()->fetch()) {
            $linkAlias->setSuggested();
            $linkAlias->getButton()->setAttribute('autofocus', true);
        } else {
            $linkMailbox->setSuggested();
            $linkMailbox->getButton()->setAttribute('autofocus', true);
            $linkAlias->setDisabled();
        }

        $window->addChild($selecting);

        return $window;
    }
}
