# Literature matcher

A parser to parse literature lists

## Downloading Zotero items

Put your Zotero credentials (`ZOTERO_USER` and `ZOTERO_API_KEY`) in the file `.env`

Then run `downloader.php`

Docker example:

	docker run -it -u $(id -u):$(id -g) --rm -v $(pwd):/app -w /app --env-file .env php:8.1-alpine php downloader.php


