# Literature matcher

A parser to parse a text-based literature list and export individual items
as BibTeX files.

It also checks if items were already imported in Zotero.

## Downloading Zotero items

Put your Zotero credentials (`ZOTERO_USER` and `ZOTERO_API_KEY`) in the file `.env`

Then run `downloader.php`

Docker example:

	docker run -it -u $(id -u):$(id -g) --rm -v $(pwd):/app -w /app --env-file .env php:8.1-alpine php downloader.php

This will download the items as JSON files. They can then be imported with
the `import_zotero_to_db.php` script (recreating the local database)


## Running the app

For local development, you must run both the PHP API and the vite dev
server (which proxies the API to PHP to avoid CORS errors).

Run PHP API with docker

	docker run -it -u $(id -u):$(id -g) --rm -v $(pwd):/app -w /app -p 8881:8881 php:8.1-alpine php -t public -S 0.0.0.0:8881

Run node app

	node run dev
