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
        printf("Before parent terminates:\n");
        printf("Child PID = %d, Parent PID = %d\n", getpid(), getppid());

        sleep(5);

        printf("After parent terminates:\n");
        printf("Child PID = %d, Parent PID = %d\n", getpid(), getppid());
    }

    else {
        printf("Parent PID = %d\n", getpid());
        exit(0);
    }

    return 0;
}