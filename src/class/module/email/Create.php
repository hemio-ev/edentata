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

/**
 * Description of Create
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Create extends \hemio\edentata\Window {

    public function content() {
        $window = new gui\Window(_('Create Email Address'));

        $selecting = new gui\Selecting(_('Where should new emails go'));

        $reqAccount = $this->module->request->derive('create_account');
        $strAccount = _('Create a new postbox for incoming mails');
        $linkAccount = $selecting->addLink($reqAccount, $strAccount);

        $reqAlias = $this->module->request->derive('create_alias');
        $strAlias = _('Deliver mails to an existing postbox');
        $linkAlias = $selecting->addLink($reqAlias, $strAlias);

        $reqRedirect = $this->module->request->derive('create_redirect');
        $strRedirect = _('Redirect mails to an external mail account');
        $selecting->addLink($reqRedirect, $strRedirect);

        if ($this->db()->getMailAccounts()->fetch()) {
            $linkAlias->setSuggested();
        } else {
            $linkAccount->setSuggested();
            $linkAlias->setDisabled();
        }
        
        $window->addChild($selecting);

        return $window;
    }


}
