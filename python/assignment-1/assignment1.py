import re


file = open(r"C:\Users\Srijan\OneDrive\Desktop\workstation_portable\python\assignment-1\data.txt")

total = 0

for line in file:
    numbers = re.findall('[0-9]+', line)
    for num in numbers:
        total += int(num)

print(total)