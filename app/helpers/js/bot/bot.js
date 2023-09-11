const puppeteer = require("puppeteer", { headless: true });
const fs = require("fs");

const pages = ["https://www.google.com", "https://www.facebook.com", "https://www.youtube.com"];

(async () => {
  // Inicializa el navegador.
  const browser = await puppeteer.launch();

  // Itera sobre la lista de páginas.
  for (const page of pages) {
    // Crea una nueva página.
    const page = await browser.newPage();

    // Carga la página.
    await page.goto(page);

    // Obtén el título de la página.
    const title = await page.title();

    // Imprime el título.
    console.log(title);
  }

  // Cierra el navegador.
  await browser.close();
})();
