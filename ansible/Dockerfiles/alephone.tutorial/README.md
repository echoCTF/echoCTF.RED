# Target: alephone
A target named in honor of the author of the famous Phrack49 article _"Smashing The Stack For Fun And Profit"_ by Aleph One.

This is a target created to serve as a tutorial in target creation, however it
can also serve as a real target since the scenarios included are not far from
truth.

## Teaching objectives
Illustrate detecting and exploiting some common vulnerability types such as
* Buffer Overflows
* Stack overflows
* Format string attacks


## Scenario goals
Write or adapt code to exploit each of the vulnerabilities
1. `bof.c`: Buffer overflow
2. `stack_overflow.c`: Stack overflow
3. `format_string.c`: Format string

## Administration
XXFIXMEXX: Any special administrative notes for building and running the target. Mandatory
and optional variables and runtime arguments.

## Refs
* [Smashing The Stack For Fun And Profit: Phrack Volume Seven, Issue Forty-Nine, File 14 of 16](Phrack49-14.txt)
* [Buffer Overflow Attack Explained with a C Program Example](https://www.thegeekstuff.com/2013/06/buffer-overflow/)
* [Lecture Notes (Syracuse University): Format String Vulnerability](http://www.cis.syr.edu/~wedu/Teaching/cis643/LectureNotes_New/Format_String.pdf)

## Exploitation
1. `nmap 172.17.0.2`
