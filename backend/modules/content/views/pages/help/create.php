Create a new static page

The form includes the following fields:
* `Title`: The title of the page
* `Slug`: A unique URL slug used at <a href="/index.php?r=content/pages/index" target="_blank">URL Routes</a>
* `Metatags`: Metatags to be included into the page headings
* `Body`: The body of the page

**NOTE**: In order for the pages to be visible into the frontend you either have to create a custom page and `findOne()` or create a URL rule for the slug to point to `static-page/view` such as

* **source** => **`<slug:myurl>`**
* **destination** => **`static-page/view`**
