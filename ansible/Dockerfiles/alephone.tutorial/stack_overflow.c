/* stack_overflow.c */
// EXAMPLE_REPLACE_PLACEHOLDER
void main(int argc, char *argv[]) {
  char buffer[512];
  char ETSCTF_FLAG[]="EXAMPLE_FLAG_PLACEHOLDER";

  if (argc > 1)
    strcpy(buffer,argv[1]);
}
