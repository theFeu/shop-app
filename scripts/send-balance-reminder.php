#!/usr/bin/env php
<?php

/*
 * Shop
 * Copyright (C) 2015 Gunnar Beutner
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */

require_once(__DIR__ . '/../config.php');
require_once('helpers/user.php');
require_once('helpers/order.php');

$users = get_users();

foreach ($users as $user_id => $user) {
    $ext_info = get_user_ext_info($user['email']);

    $balance = $ext_info['balance'];

    if (bccomp($balance, '-15') >= 0)
        continue;

    $first_name = explode(' ', $user['name'], 2)[0];

    $debt_formatted = format_number(bcmul($balance, '-1'), false);

    $message = <<<MESSAGE
Hallo ${first_name}! Du hast noch ${debt_formatted} EUR Schulden. Bitte überweise das Geld an:

Kontoinhaber: ${ext_info['tgt_owner']}
IBAN: ${ext_info['tgt_iban']}
BIC: ${ext_info['tgt_bic']}
Kreditinstitut: ${ext_info['tgt_org']}
Verwendungszweck: ${ext_info['tgt_reference']}
MESSAGE;

    send_jabber_message($user['email'], mb_convert_encoding($message, 'iso-8859-1'));
}