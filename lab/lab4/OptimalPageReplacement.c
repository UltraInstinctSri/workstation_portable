#include <stdio.h>

int main() {
    int f, n, a[20], p[50];
    int i, j, k, pos, far;
    int hit = 0, fault = 0;

    scanf("%d%d", &f, &n);

    for (i = 0; i < n; i++)
        scanf("%d", &p[i]);

    for (i = 0; i < n; i++) {
        int x = 0;

        for (j = 0; j < f; j++)
            if (a[j] == p[i])
                x = 1;

        if (x) {
            hit++;
        } else {
            fault++;

            if (i < f) {
                a[i] = p[i];
            } else {
                far = -1;

                for (j = 0; j < f; j++) {
                    for (k = i + 1; k < n && p[k] != a[j]; k++);

                    if (k > far)
                        far = k, pos = j;
                }

                a[pos] = p[i];
            }
        }

        printf("%d: ", p[i]);

        for (j = 0; j < f; j++)
            printf("%d ", a[j]);

        puts("");
    }

    printf("Fault=%d Hit=%d", fault, hit);

    return 0;
}