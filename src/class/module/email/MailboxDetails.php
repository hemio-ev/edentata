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
use hemio\html;
use hemio\html\String;

/**
 * Description of EditAccount
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class MailboxDetails extends \hemio\edentata\Window {

    public function content($address) {
        /*
          $xs = explode('@', $address, 2);
          $params = [
          'localpart' => $xs[0],
          'domain' => $xs[1]
          ];
         * 
         */

        $window = $this->newFormWindow(
                'edit_account'
                , _('Email Mailbox')
                , $address
        );

        /*
          $stmt = new sql\QuerySelectFunction($this->module->pdo, 'email.sel_mailbox');
          $stmt->options('WHERE localpart = :localpart AND domain = :domain');
          $res = $stmt->execute($params);

          $account = $res->fetch();
          $address = $account['localpart'] . '@' . $account['domain'];
         */

        $resAliases = $this->db()->aliasSelect(
                Utils::addrLocalpart($address)
                , Utils::addrDomain($address)
        );

        $aliases = $resAliases->fetchAll();

        $list = new gui\Listbox();

        foreach ($aliases as $alias) {
            $div = new html\Span();
            $div->addChild(new String($alias['localpart'] . '@' . $alias['domain']));
            $button = $div->addChild(new gui\LinkButton($this->module->request->derive(), _('Delete')));
            #$button['form']['button']->addCssClass('progress');
            $list->addLine($div);
            #print_r($alias);
        }

        if (count($aliases) > 0) {
            $window
                    ->addChild(new gui\Fieldset(_('Aliases')))
                    ->addChild($list);
        }
        $window->addChild($this->actions($address));

        return $window;
    }

    public function actions($address) {
        $selecting = new gui\Selecting(_('Possible Actions'));

        $selecting->addLink(
                $this->module->request->derive('mailbox_password', $address)
                , _('Change password')
        );

        $selecting->addLink(
                $this->module->request->derive('alias_create', $address)
                , _('Create alias for this mailbox')
        );

        $selecting->addLink(
                $this->module->request->derive('mailbox_delete', $address)
                , _('Delete entire mailbox')
        );

        return $selecting;
    }

}
