
// MUTATION OBSERVER
const target = document.getElementsByClassName("ms-List-surface")[0];
const mutationDom = new MutationObserver(function(list, _o) {
    for (let i = 0; i < list.length; i++) {

    (list[i].type === "childList") ? addMoreContacts() : 0;

  }
});

mutationDom.observe(target, { childList: true });

// obteniendo la lista
let nombres, correos, totalItems = 0;
let listaContactos = {
    nombres: [],
    correos: []
};

listaContactos.correos[0] = 0;
listaContactos.correos[1] = 1;

function addMoreContacts(params) {
    console.warn("Pantalla actualizada");

    totalItems = listaContactos.correos.length;
    nombres = document.querySelectorAll('.css-245');
    correos = document.querySelectorAll('.css-231');
    
    nombres.forEach((item) => {
        (!listaContactos.nombres.includes(item.textContent)) ? listaContactos.nombres.push(item.textContent) : 0;
    });
    //listaContactos.correos[totalItems] = 0;
    //listaContactos.correos[totalItems + 1] = 1;

    correos.forEach((item) => { 
        if (item.textContent != 'User') { 
            (!listaContactos.correos.includes(item.textContent)) ? listaContactos.correos.push(item.textContent) : 0;
        }
    });

    console.warn("-> " + listaContactos.nombres.length + " Contactos registrados");
    
}


addMoreContacts();