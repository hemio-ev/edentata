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

namespace hemio\edentata\module\dns;

use hemio\edentata\exception\UnknownOperation;
use hemio\edentata\exception;
use hemio\edentata;

/**
 * Description of ModuleDns
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Module extends \hemio\edentata\Module
{

    protected function constructHook()
    {
        $this->db = new Db($this->pdo);
    }

    public function getContent()
    {
        switch ($this->request->action) {
            case '':
                $content = (new Overview($this))->content();
                break;

            case 'adminc':
                try {
                    $content = (new AdminC($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect(
                        $this->request->derive('registered_details', true));
                }
                break;

            case 'custom_create':
                try {
                    $content = (new CustomCreate($this))->content($this->request->subject,
                                                                  $this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('registered_details',
                                                                        true));
                }
                break;

            case 'custom_delete':
                try {
                    $content = (new CustomDelete($this))
                        ->content($this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('registered_details',
                                                                        true));
                }
                break;

            case 'custom_details':
                try {
                    $content = (new CustomDetails($this))
                        ->content($this->request->subject, $this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('registered_details',
                                                                        true));
                }
                break;

            case 'handle_create':
                try {
                    $content = (new HandleCreate($this))
                        ->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'handle_delete':
                try {
                    $content = (new HandleDelete($this))
                        ->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'handle_details':
                try {
                    $content = (new HandleDetails($this))
                        ->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'registered_create':
                try {
                    $content = (new RegisteredCreate($this))->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'registered_details':
                $content = (new RegisteredDetails($this))->content($this->request->subject);
                break;

            case 'service_details':
                try {
                    $content = (new ServiceDetails($this))->content($this->request->subject,
                                                                    $this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('registered_details',
                                                                        true));
                }
                break;

            case 'service_create':
                try {
                    $content = (new ServiceCreate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($e->backTo);
                }
                break;

            default:
                throw UnknownOperation::unknownAction($this->request->action);
        }

        return $content;
    }

    public static function getDir()
    {
        return __DIR__;
    }

    public static function getName()
    {
        return _('Domain');
    }
}
