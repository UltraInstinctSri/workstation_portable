#include <stdio.h>
#include <unistd.h>
#include <sys/types.h>

int main()
{
  pid_t pid;
  pid = fork();
  if(pid < 0){
    printf("Process has errors");
  
  }
  else if(pid  == 0 ){
    printf("Child process is created with PID: %d\n", getpid());
       printf("Parent of child created with PPID = %d\n", getppid());
  }
  else{
    printf("Parent process is created with PID: %d\n", getpid());   
  }
    return 0;
}