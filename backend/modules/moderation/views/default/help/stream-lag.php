Lists stream entries with the time gap to the previous entry. Each row shows one entry of the activity feed (the same stream as Activity / Stream) and, in `Seconds Since Last`, how many seconds passed between it and the entry before it when the whole stream is ordered by time. The gap is computed with a `LAG()` window function over the entry timestamps, so it always refers to the previous entry of the whole filtered set, not of the current page.

### The displayed fields
* `ID`: The stream entry id
* `Formatted`: The stream message as the frontend feed renders it
* `Player ID`: The player the entry belongs to
* `Model`: The object the entry is about, eg finding, treasure or headshot
* `Model ID`: The id of that object
* `Points`: The points the entry carried
* `Seconds Since Last`: The gap in seconds to the previous entry, empty for the oldest entry

### Notes
* Entries come ordered by newest `ID` first. Sort by `Seconds Since Last` to bring the biggest gaps on top.
* A long gap means the stream went quiet for that long, a run of near zero values means several entries landed at once.
* The grid filters work like the activity stream grid, by player, model, model id, points, academic flag, timestamp and title/message.
* Applying filters changes what counts as the previous entry, so the gaps are always relative to the filtered set.
