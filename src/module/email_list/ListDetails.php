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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\form;

/**
 * Description of ListDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ListDetails extends Window
{

    public function content($list)
    {
        $window = $this->newFormWindow('select_subscribers', _('Email List'),
                                                               $list);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->module->request->derive('subscribers_create', true)
            , _('Add Subscriber')
            )
            , true);

        $window->addChild($this->details());
        $this->subscribers($window, $list);

        return $window;
    }

    protected function details()
    {
        $selecting = new gui\Selecting(_('Email List'));

        $selecting->addLink(
            $this->module->request->derive('list_update', true)
            , _('Change list owner')
        );

        $selecting->addLink(
            $this->module->request->derive('list_delete', true)
            , _('Delete list')
        );

        return $selecting;
    }

    protected function subscribers(gui\WindowModuleWithForm $window, $list)
    {
        $subscribers = $this->db->subscriberSelect($list)->fetchAll();

        $fieldset  = new gui\Fieldset(_('Subscribers'));
        $selectbox = new gui\Selectbox();

        $window->getForm()->addInheritableAppendage(
            'selected_subscribers', $selectbox
        );

        $window->getForm()->addChild($fieldset)->addChild($selectbox);

        foreach ($subscribers as $subscriber) {
            $selectbox->addItem(
                $subscriber['address']
                , $subscriber['address']
                , $subscriber['backend_status']
            );
        }
        $options = new form\Container();

        $move      = new form\FieldSubmit('move', _('Move …'));
        $move->getControlElement()
            ->setAttribute(
                'formaction',
                $this->request->derive('subscribers_move', $list)->getUrl()
        );
        $options[] = $move;

        $unsubscribe = new form\FieldSubmit('unsubscribe', _('Unsubscribe'));
        $unsubscribe->getControlElement()
            ->setAttribute(
                'formaction',
                $this->request->derive('subscribers_unsubscribe', $list)->getUrl()
        );
        $options[]   = $unsubscribe;

        $selectbox->setOptions($options);
    }
}
