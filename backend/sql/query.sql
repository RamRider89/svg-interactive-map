SELECT TOP 10 * FROM sapcatalogocentros WHERE NumeroCentro = 230178

select * from cat_secciones where numeroseccion = 103

select * from SAPCATALOGOAREAS where numero = 3

select * from [sapdivisiones] where numero = 5

SELECT Nombre FROM sapEmpresas where Clave = 1



SELECT 
EM.Numemp, EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno, EM.Curp, EM.Centro, EM.NumeroPuesto, EM.empresa,
CE.NombreCentro, CE.NumeroCiudad, CE.NumeroGerente, CE.GerenteZona, CE.GerenteDivision, CE.GerenteRegional
FROM sapCatalogoEmpleados EM
INNER JOIN sapCatalogoCentros CE
ON EM.Centro = CE.NumeroCentro
WHERE Numemp = 90279459