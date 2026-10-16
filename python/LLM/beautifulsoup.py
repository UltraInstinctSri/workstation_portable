from bs4 import BeautifulSoup

html = "<p>Hello Python!</p>"
soup = BeautifulSoup(html, "html.parser")

print(soup.p.text)