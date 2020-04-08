# Contribution Guidelines
Contributions to echoCTF.RED are greatly appreciated. If you'd like to help make the project better, read on.


1. Follow good git practices: use pull requests, prefer feature branches, always write clear commit messages.
2. Ensure any specific version requirements are well documented
3. echoCTF.RED code should always use the BSD 3-clause license.

## Coding guidelines
1. MySQL triggers must be named after the table they are affecting prefixed by their operation
  * `tbi_`: Trigger before insert
  * `tbu_`: Trigger before update
  * `tbd_`: Trigger before delete
  * `tai_`: Trigger after insert
  * `tau_`: Trigger after update
  * `tad_`: Trigger after delete

2. Add yii console commands on the backend application, unless the command needs to operate explicitly on the frontend (such as the `frontend/yii generator/sitemap` command)

And lastly, thank you for contributing!
