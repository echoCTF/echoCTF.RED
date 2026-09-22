# Network  Visuals
The platform provides realtime network traffic visualization through `gource` and
`logstalgia`. The visuals are captured on the vpn server by the `gource-inetd-service.pl`
running through `inetd` on `127.0.0.1` port `50000` for `gource` and `60000` for `logstalgia`.

## PF config
You will need to configure the firewall to redirect connections from the external interface to the local running services.

Add something like the following at the top of `/etc/service.pf.conf`:

```
# For administrators
pass in quick inet proto tcp from <administrators> to port { 50000, 60000} rdr-to 127.0.0.1  label "administrators"

# For venue
pass in quick inet proto tcp from <venue> to port { 50000, 60000} rdr-to 127.0.0.1  label "administrators"

# For moderators
pass in quick inet proto tcp from <moderators> to port { 50000, 60000} rdr-to 127.0.0.1  label "administrators"

# For everyone
pass in quick inet proto tcp to port { 50000, 60000} rdr-to 127.0.0.1  label "administrators"
```

## logstalgia
```sh
stdbuf -i0 -o0 -e0 nc echoctf.net 60000|logstalgia -x --hide-response-code -g "UDP,URI=udp?$,20" -g "TCP,URI=tcp?$,60" -g "ICMP,URI=icmp?$,20" -
```

## Gource
```sh
stdbuf -i0 -o0 -e0 nc echoctf.net 50000|gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1  -f -
```

Some extra options for gource to make visuals a bit more spectacular
```sh
gource --log-format custom \
   --hide files,bloom,date,mouse,progress \
   --user-friction 0.25 \
   --user-scale 0.8 \
   --highlight-users \
   --title "log activity" \
   --font-size 32 \
   --font-colour 98bb56 \
   --crop horizontal \
   --multi-sampling \
   --realtime -
```

The following options can be applied/modified for modifying the effects

* to somehow limit the glow effect (also remove 'bloom' from --hide) use `--bloom-intensity 0.25` and `--bloom-multiplier 0.25`
* to keep node elements' text from fading out, use `--highlight-dirs`
* Elasticity of nodes `-e, --elasticity FLOAT`
* Background colour in hex. `-b, --background-colour FFFFFF`
* Set a background image. `--background-image IMAGE`
* Logo to display in the foreground. `--logo IMAGE`
* Offset position of the logo. `--logo-offset XxY`
* Directory containing .jpg or .png images of users (eg 'Full Name.png') to use as avatars. `--user-image-dir DIRECTORY`
* Path of .jpg to use as the default user image. `--default-user-image IMAGE`
* Colorize user images. `--colour-images`

## More on unbuffering stdin/stdout

Language-specific tips to disable i/o buffering:

* python: run with -u flag `python -u script.py`
* awk: use `fflush()` function
* grep: `grep --line-buffered`

When the tricks above don't apply, use one of the following helper commands, to disable i/o buffering on your running program:

* `socat` (poses as "netcat++", small installation footprint): `socat EXEC:myprogram,pty,ctty,echo=0 STDIO`
* `stdbuf` (comes with coreutils package, small running footprint but comes with a whole bunch of gnu): stdbuf -i0 -o0 -e0 myprogram
* `unbuffer` (expect script that comes with expect-dev package, which might or might not be available): `unbuffer myprogram` (Untested)

On linux stdbuf seems the best option:

```sh
$ time stdbuf -i0 -o0 -e0 ls > /dev/null
real	0m0.027s
user	0m0.003s
sys	0m0.000s

$ time unbuffer ls > /dev/null
real	0m0.103s
user	0m0.009s
sys	0m0.010s

$ time socat EXEC:ls,pty,ctty,echo=0 STDIO >/dev/null
real	0m0.506s
user	0m0.000s
sys	0m0.005s
```