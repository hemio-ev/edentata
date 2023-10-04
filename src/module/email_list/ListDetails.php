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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of ListDetails
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ListDetails extends Window
{

    public function content($list)
    {
        $window = $this->newFormWindow(
            'select_subscribers'
            , _('Email List')
            , $list
        );

        $this->addActions($window->addHeaderbarMenu());

        $window->addButtonRight(
            new gui\LinkButton(
            $this->module->request->derive('subscribers_create', true)
            , _('Add Subscriber')
            )
            , true);

        $listData = $this->db->listSelect($list)->fetch();
        if (!$listData)
            throw new exception\Error(_('Email list does not exist.'));

        $window->getForm()->addChild(
            new gui\Output(_('List Owner'), $listData['admin']));

        $this->subscribers($window, $list);

        return $window;
    }

    protected function addActions($menu)
    {
        $menu->addEntry(
            $this->module->request->derive('list_update', true)
            , _('Change list owner')
        );

        $menu->addEntry(
            $this->module->request->derive('list_delete', true)
            , _('Delete list')
        );
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
