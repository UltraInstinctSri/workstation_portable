largest_so_far = None
smallest_so_far = None
for num in [1,4,6,89,10]:
    if smallest_so_far is None:
        smallest_so_far =num
    if largest_so_far is None:
        largest_so_far = num
    if num > largest_so_far:
        largest_so_far = num
    if num <smallest_so_far:
        smallest_so_far = num
print(largest_so_far , smallest_so_far)