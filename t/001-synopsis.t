#!/usr/bin/env php
<?php
require_once 'Common.php';
require_once 'lib/PopUpArchive/Client.php';

// assumes we have 20+ search results for 'test'
plan(57);

ok( $client = new PopUpArchive_Client(), "new Client" );

# basic fetches
ok( $colls = $client->get_collections(), "get_collections" );
ok( $coll_id = $colls[0]->id, "get colls[0]->id" );
ok( $coll = $client->get("/collections/$coll_id"), "get /collections/$coll_id" );
ok( $coll_i = $client->get_collection($coll_id), "get_collection $coll_id" );
is( $coll->title, $coll_i->title, "coll titles match" );

ok( $item_ids = $coll->item_ids, "get collection->item_ids" );
ok( $item = $client->get("/collections/$coll_id/items/$item_ids[0]"), "get item $item_ids[0]" );
ok( $item_i = $client->get_item($coll_id, $item_ids[0]), "get_item" );
is( $item->title, $item_i->title, "item titles match" );

# create assets
ok( $new_item = $client->create_item($coll_id, array(
            'title' => 'this is an item with remote audio files',
            'extra' => array( 'callback' => 'https://nosuchdomain.foo/callback/path' ),
        )
    ), "create_item" );

$remote_audio = 'https://speechmatics.com/api-samples/zero';
ok( $new_audio = $client->create_audio_file( $new_item->id, array('remote_file_url' => $remote_audio)),
    "create_audio_file" );

# re-fetch item from server
ok( $pua_item = $client->get_item( $coll_id, $new_item->id ), "get newly minted item" );
is( $pua_item->audio_files[0]->original, $remote_audio, "new audio file has original==remote_file_url" );

# search
ok( $resp = $client->search(array('query' => 'test')), "search query=test" );
foreach ( $resp->results as $item ) {
    diag( sprintf("[%s] %s", $item->id, $item->title) );
    ok( $item->title, "hit has title" );
    ok( $item->collection_id, "hit has collection_id" );
}

is( $resp->query, 'test', "search results has query" );
is( $resp->page, 1, "search results have page" );
