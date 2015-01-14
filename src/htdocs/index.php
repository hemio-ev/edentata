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

namespace hemio\edentata;

use hemio\html;

chdir(__DIR__ . '/../../');

require_once 'vendor/autoload.php';

# external data
$request = new Request($_GET);
$modulesNavi = ['email2', 'email', '.'];

$activeModuleName = $request->module;

try {
# db connect
    $pdo = new sql\Connection('pgsql:dbname=test1', 'postgres');
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

# db auth
    $usrData = [
        'p_name' => 'user1',
        'p_password' => 'pw'
    ];
    $qryAuth = new sql\QuerySelectFunction($pdo, 'user.login', $usrData);
    $qryAuth->execute();

# doc
    $doc = new html\Document(new html\String('Title'));
    $doc->getHtml()->getHead()->addCssFile('style.css');

    $body = $doc->getHtml()->getBody();
    $body['messages'] = new html\Div();

# navi
    $nav = (new ContentNav($modulesNavi))->getNav();
    while ($nav->unhandledEvents()) {
        try {
            $nav->handleEvent();
        } catch (exception\Printable $event) {
            $body['messages'][] = new gui\Message($event);
        }
    }
    $body->addChild($nav->getContent());

# module
    $loadedModule = new LoadModule($activeModuleName, $pdo);

#generate content 
    $content = $loadedModule->getContent($request);
    $body->addChild($content);
} catch (\PDOException $e) {
    $body->addChild(new gui\Message(new exception\Error('DB operation failed' . $e->getMessage())));
} catch (exception\Printable $e) {
    $body->addChild(new gui\Message($e));
}




echo $doc->__toString();
