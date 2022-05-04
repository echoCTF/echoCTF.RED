These are users that are able to access the administrative interfaces. These
users are not visible nor are able to access the frontend interfaces.

The system users require the details
* `Username`: A unique username used to login
* `Auth key` (optional): The system will generate one if needed
* `Password`: The password for the user
* `Password Reset token` (optional): A password reset token
* `Email`: The user email
* `Status`: One of Active, Inactive, Deleted
* `Created at`/`Updated at`: Timestamps of creation and last update for the account
* `Verification token` (optional): The token to be used to verify the account
* `Admin`: Flag for wether this user is super admin (access to update/delete operations)

**NOTE**: These users are also allowed to manipulate platform parameters through the REST API with the use of their AuthKey as authentication token.
