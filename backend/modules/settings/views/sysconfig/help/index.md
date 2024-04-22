Define key/value configuration pairs. These configuration keys can be used by the platform
or external applications to determine their operational parameters (eg VPN server checks if
event is active here).

These key/value pairs are populated on the Memcached server prefixed with `sysconfig:key`

Fields:
* `ID`: The configuration key name
* `Val`: The value it holds

**NOTE**: Keep in mind that these are free form keys and no validation is performed on
them for compatibility with other applications. The frontend application converts **ANY**
key with value of `0` or `1` to `false` and `true` respectively.

Visit our online documentation for <a href="https://echoctfred.readthedocs.io/Sysconfig-Keys/" target="_blank">Sysconfig keys</a>
for a full list of the supported keys and their values.