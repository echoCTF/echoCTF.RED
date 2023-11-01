## logstalgia
```sh
nc echoctf.net 50000|logstalgia -x --hide-response-code -g "UDP,URI=udp?$,20" -g "TCP,URI=tcp?$,60" -g "ICMP,URI=icmp?$,20" -
```
## Gource
```sh
nc echoctf.net 60000|gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1  -f -
```
## Visualising syslog

mysqltail was slightly modified. Line 206 of mt.c:

```c
        while((dummy = strchr(dummy, ',')) != NULL)
```

was changed to


```c
        while((dummy = strchr(dummy, '\t')) != NULL)
```

Not a good hack; Simply replacing comma separated output with pipe-separated would be better, but the change above was shamefully quicker and worked... Now start mysqltail on echofish through stdbuf to eliminate pipe i/o buffering:

```sh
stdbuf -i0 -o0 -e0 ./mysqltail -h echo.ctf -u echo_ctf_mods -p modsuser -d ets_ctf -t vtcpdump -k id -c gource -i 1 -n 1 | gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1 -f -
```

```sh
stdbuf -i0 -o0 -e0 /home/gadamo/work/mysqltail-0.1/mysqltail \
   -h db.echothrust.net -u gadamo -p PASSWORD_HERE
   -d ETS_echofish_prod -t archive -k id \
   -c "CONCAT(unix_timestamp(created_at),'|',inet_ntoa(host),'|M|',inet_ntoa(host),'/',program,'/messages')" \
   -n 10 -i 1 | \
   gource --log-format custom --auto-skip-seconds 3 --seconds-per-day 1 --file-idle-time 1 --hide files \
   --bloom-intensity 0.25 --bloom-multiplier 0.25 --user-friction 0.25 --highlight-all-users --realtime -
```

```sh
stdbuf -i0 -o0 -e0 ./mysqltail \
   -d ETS_echofish_prod -t archive -k id \
   -c "CONCAT(unix_timestamp(created_at),'|',program,'@',inet_ntoa(host),'|A|','log/',inet_ntoa(host),'/',program,'/messages')" \
   -n 10 -i 1 | \
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

exit

# to somehow limit the glow effect (also remove 'bloom' from --hide) use:
#   --bloom-intensity 0.25 \
#   --bloom-multiplier 0.25 \

# to keep node elements' text from fading out, use:
#   --highlight-dirs \

# Gource options to try:
-e, --elasticity FLOAT
    Elasticity of nodes.
-b, --background-colour FFFFFF
    Background colour in hex.
--background-image IMAGE
    Set a background image.
    Font colour in hex.
--logo IMAGE
    Logo to display in the foreground.
--logo-offset XxY
    Offset position of the logo.
--user-image-dir DIRECTORY
    Directory containing .jpg or .png images of users (eg 'Full Name.png') to use as avatars.
--default-user-image IMAGE
    Path of .jpg to use as the default user image.
--colour-images
    Colourize user images.
```

### More on unbuffering stdin/stdout

Language-specific tips to disable i/o buffering:

* python: run with -u flag `python -u script.py`
* awk: use `fflush()` function
* grep: `grep --line-buffered`

When the tricks above don't apply, use one of the following helper commands, to disable i/o buffering on your running program:

* socat (poses as "netcat++", small installation footprint): `socat EXEC:myprogram,pty,ctty,echo=0 STDIO`
* stdbuf (comes with coreutils package, small running footprint but comes with a whole bunch of gnu): stdbuf -i0 -o0 -e0 myprogram
* unbuffer (expect script that comes with expect-dev package, which might or might not be available): `unbuffer myprogram` (Untested)

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


```sh
stdbuf -i0 -o0 -e0 ./mysqltail -h echo.ctf -u echo_ctf_mods -p modsuser -d ets_ctf -t vtcpdump -k id -c gource -i 1 -n 1 | gource --log-format custom --highlight-all-users --realtime --multi-sampling --auto-skip-seconds 3 --seconds-per-day 1 -f -
stdbuf -i0 -o0 -e0 mysqltail -h echo.ctf -u echo_ctf_mods -p modsuser -d ets_ctf -t logstalgia -k id -c msg -i 1 -n 1 | logstalgia --sync -f
```
