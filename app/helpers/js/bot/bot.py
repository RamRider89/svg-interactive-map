import requests

def get_title(url):
  """Obtiene el título de una página web."""
  response = requests.get(url)
  return response.headers["title"]

def main():
  # Define la lista de páginas.
  pages = ["https://www.google.com", "https://www.facebook.com", "https://www.youtube.com"]

  # Itera sobre la lista de páginas.
  for page in pages:
    print(get_title(page))

if __name__ == "__main__":
  main()