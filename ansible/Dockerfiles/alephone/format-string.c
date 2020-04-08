/*
 * Author: http://www.cis.syr.edu/~wedu/Teaching/cis643/LectureNotes_New/Format_String.pdf
 */
int main(int argc, char *argv[])
{
  char user_input[100];
  scanf("%99s", user_input); /* getting a string from user */
  printf(user_input); /* Vulnerable place */
  return 0;
}
