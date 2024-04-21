Manage news items and (optionally) post on
<a href="/index.php?r=settings/sysconfig/view&id=discord_news_webhook" target="_blank" title="Discord Webhook Sysconf">discord webhook</a>.

These news items are displayed by default on the <a href="//<?=Yii::$app->sys->offense_domain?>/dashboard" title="Frontend Dashboard page" target="_blank">frontend dashboard page</a>.

The listing displays the following fields:
* `ID`: The unique ID of the entry
* `Title`: The title of the news item
* `Category`: A category for the entry. It supports HTML so you can use images such as `<img src="/images/news/category/target-migration.svg" width="25px" alt="">`
* `Created At`/`Updated At`: A date showing when the new item was created and when was the last time it got updated.