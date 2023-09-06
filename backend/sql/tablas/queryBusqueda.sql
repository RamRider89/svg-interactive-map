/*
ULTIMOS REGISTROS DE MARZO

NUEVOS
90314963
90314702
EM.Numemp AS NumeroEmpleado, CONCAT(EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno) AS NombreEmpleado, EM.CURP, 
CARLOS DAVID        DUARTE         GILL           /
Carlos David Duarte Gill
*/

SELECT 
EM.Numemp AS NumeroEmpleado, EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno, EM.CURP, 
Em.Telefono, EM.Centro, CE.NombreCentro, EM.FechaNacimiento,
EM.NumeroPuesto, PUES.Nombre AS NombrePuesto,
EM.Empresa, EMP.Nombre AS NombreEmpresa,
CE.NumeroGerente, GER.Nombre AS NombreGerente, GER.ApellidoPaterno AS ApellidoPaternoGerente, GER.ApellidoMaterno AS ApellidoMaternoGerente,
CE.GerenteZona, CE.GerenteDivision, CE.GerenteRegional
FROM sapCatalogoEmpleados EM

INNER JOIN sapCatalogoCentros CE
ON EM.Centro = CE.NumeroCentro

INNER JOIN sapEmpresas EMP
ON EM.empresa = EMP.Clave

INNER JOIN sapcatalogopuestos PUES
ON EM.NumeroPuesto = PUES.Numero

INNER JOIN sapCatalogoEmpleados GER
ON GER.Numemp = CE.NumeroGerente

WHERE EM.Numemp = 90279459

