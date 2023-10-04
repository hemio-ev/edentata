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

use hemio\edentata;
use hemio\edentata\exception;
use hemio\edentata\exception\UnknownOperation;

/**
 * Email Module
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Module extends edentata\Module
{
    /**
     *
     * @var Db
     */
    public $db;

    public static function getName()
    {
        return _('Email');
    }

    public static function getDir()
    {
        return __DIR__;
    }

    protected function constructHook()
    {
        $this->db = new Db($this->pdo);
        $this->pdo->addExceptionMapper(
            new DbExceptionMapping($this->request->derive()));
    }

    public function getContent()
    {
        switch ($this->request->action) {
            case '':
                $content = (new Overview($this))->content();
                break;


            case 'address_create':
                $content = (new AddressCreate($this))->content();
                break;

            case 'alias_create':
                try {
                    $content = (new AliasCreate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'alias_delete':
                try {
                    $content = (new AliasDelete($this))->content($this->request->subject,
                                                                 $this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('mailbox_details',
                                                                        true));
                }
                break;

            case 'mailbox_create':
                try {
                    $content = (new MailboxCreate($this))->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'mailbox_delete':
                try {
                    $content = (new MailboxDelete($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'mailbox_details':
                $content = (new MailboxDetails($this))->content($this->request->subject);
                break;

            case 'mailbox_password':
                $content = (new MailboxPassword($this))->content($this->request->subject);
                break;

            case 'redirection_create':
                try {
                    $content = (new RedirectionCreate($this))->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'redirection_delete':
                try {
                    $content = (new RedirectionDelete($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            default:
                throw UnknownOperation::unknownAction($this->request->action);
        }

        return $content;
    }
}
