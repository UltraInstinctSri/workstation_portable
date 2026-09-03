#include <stdio.h>
#include <unistd.h>
#include <stdlib.h>
#include <sys/types.h>

int main() {
    pid_t pid = fork();

    if (pid < 0) {
        printf("Fork failed\n");
        return 1;
    }

    else if (pid == 0) {
        printf("Child process is terminating.\n");
        printf("Child PID = %d\n", getpid());
        exit(0);
    }

    else {
        printf("Parent PID = %d\n", getpid());
        printf("Child PID = %d\n", pid);

        sleep(10);

        printf("Parent is terminating now.\n");
    }

    return 0;
}