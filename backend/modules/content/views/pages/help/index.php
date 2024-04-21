Manage static pages to be displayed on the frontend.

The listing displays the following fields:
* `ID`: The unique id of the page
* `Title`: The title of the page
* `Slug`: A unique URL slug used at <a href="/index.php?r=content/pages/index" target="_blank">URL Routes</a>
* `Created at`/`Updated at`: The creation date and the date it was last updated

**NOTE**: In order for the pages to be visible into the frontend you either have to create a custom page and `findOne()` or create a URL rule for the slug to point to `static-page/view` such as
* **source** => **`<slug:myurl>`**
* **destination** => **`static-page/view`**