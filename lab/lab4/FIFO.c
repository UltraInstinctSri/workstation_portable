#include <stdio.h>

int main() {
    int f, n, a[50] = {0}, p[20];
    int i, j, k = 0, hit = 0, fault = 0;

    scanf("%d%d", &f, &n);

    for (i = 0; i < n; i++)
        scanf("%d", &p[i]);

    for (i = 0; i < n; i++) {
        int x = 0;

        for (j = 0; j < f; j++)
            if (a[j] == p[i])
                x = 1;

        if (x)
            hit++;
        else {
            a[k] = p[i];
            k = (k + 1) % f;
            fault++;
        }

        printf("%d: ", p[i]);

        for (j = 0; j < f; j++)
            printf("%d ", a[j]);

        puts("");
    }

    printf("Fault=%d Hit=%d", fault, hit);

    return 0;
}