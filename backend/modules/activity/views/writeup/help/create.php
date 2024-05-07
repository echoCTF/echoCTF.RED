Create a writeup for a platform target.

Writeups allow players to share their methodology when solving a target.

The form fields include:
* `Target ID`: The target this writeup belongs to
* `Player ID`: The username that the writeup will display as author
* `Formatter`: The formatter that will be used by the frontend to display this writeup (supported formatters `Markdown`, `Text`).
* `Language`: The language that this writeup is written (defaults to `English`)
* `Content`: The raw content of the writeup
* `Approved`: Whether a writeup is approved or not
* `Status`: Status of writeup
  * `OK`: Writeup is published and its ok to be displayed
  * `Pending`: The writeup is pending for review by a moderator
  * `Rejected`: The writeup is rejected by a moderator
  * `Needs Fixes`: The writeup needs fixes as suggested by a moderator. (This usually means there is also a comment left for the player to read)
* `Comment`: A comment that will be left for the author to read