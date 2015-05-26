#!/usr/bin/env php
<?php
require_once 'Common.php';

require_once 'lib/PopUpArchive/Client.php';

plan(1);

ok( $client = new PopUpArchive_Client(), "new Client" );
