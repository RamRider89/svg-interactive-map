-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del empleado>
-- ================================================================================================================================================
USE Personal
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
CREATE PROCEDURE [dbo].[campus_get_empleado] @id_Usuario INT	
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
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

    WHERE EM.Numemp = @id_Usuario;
END