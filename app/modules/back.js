function loadAsignarLugar(employee) {


}




// UTILS
function toTitleCase(texto) {
    const str = String(texto).trim().toLowerCase();
    return str.replace(/(?:^|\s)\w/g, function (match) {
        return match.toUpperCase();
    });
}



function getBirthday(fecha) {
    const nacimiento = new Date(fecha.trim());
    const day = nacimiento.getDate();
    const month = nacimiento.getMonth();
    return String(day + ' de ' + MESES[month]);
}