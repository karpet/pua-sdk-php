Pop Up Archive PHP Client
=========================================

PHP 5.3+ client SDK for https://www.popuparchive.com/

See docs at https://developer.popuparchive.com/

OAuth credentials are available from https://www.popuparchive.com/oauth/applications

Example:

```php
require_once 'path/to/PopUpArchive/Client.php';

# create a client
$client = new PopUpArchive_Client(
  'key'     => 'oauth_id',
  'secret'  => 'oauth_secret',
  'host'    => 'https://www.popuparchive.com/',
  'debug'   => false,
)

# fetch all Collections
$collections = $client->get_collections();

# fetch a Collection with id 1234
$coll = $client->get('/collections/1234');
# or more idiomatically
$coll = $client->get_collection(1234);

# fetch an Item
$item = $client->get('/collections/1234/items/5678');
# or idiomatically
$item = $client->get_item(1234, 5678);

# search
$res = $client->search(array('query' => 'test'));
foreach($res->results as $items) {
  printf("[%s] %s (%s)\n", $item->id, $item->title, $item->collection_title);
}

```

## Development

This package uses composer. To install dependencies you'll need the composer tool
from https://getcomposer.org/. Then:

```bash
make install
```

To run the tests, create a **.env** file in the checkout
with the following environment variables set to meaningful values:

```
PUA_ID=somestring
PUA_SECRET=sekritstring
PUA_HOST=http://popuparchive.dev
```

Then run the tests:

```bash
make test
```

