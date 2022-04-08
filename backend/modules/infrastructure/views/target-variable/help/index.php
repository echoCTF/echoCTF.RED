Target variables represent key/value pairs that will be used as environment variables inside the target (currently supports only docker).

These can be flags or configuration values used by the applications running on the target.

The fields include
* **Target**: The target that this volume belongs to
* **Key**: The exact variable name to be used
* **Val**: The exact value to be assigned to the variable

For more information you can refer to [Docker: Set environment variables](https://docs.docker.com/engine/reference/commandline/run/#set-environment-variables--e---env---env-file).
