SilverStripe Simple News
==================

# Author
* Arno Poot
* http://arnopoot.nl

# Features
* DataObjects for News articles, no clutter in SiteTree.
* Controller $url_handlers to provide each article it's own page by title
* RSS Feed
* Pagination

# Requirements
* SilverStripe 3.0 or above

# Installation
SilverStripe Simple News can be easily installed on any already-developed website

 * Either extract the module into the `arnhoe-simplenews` folder, or install using composer

```bash
composer require "arnhoe/silverstripe-simplenews" "dev-master"
```

# Configuration
Use config.yml to configure the module.
* Keep your assets folder clean by enabling save_image_in_seperate_folder, which will save news image to seperate folder. "news/news-title"
* Use News Holder page to set page limit count.