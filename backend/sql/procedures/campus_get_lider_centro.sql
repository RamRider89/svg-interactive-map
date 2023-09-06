-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del lider del centro>
-- ================================================================================================================================================
USE Personal
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_get_lider_centro] (@TIPO_QUERY VARCHAR(10), @id_Usuario INT, @id_Centro INT, @nombre_Empleado VARCHAR(250))
AS
BEGIN
SET NOCOUNT ON;
	-- ================================================================================================================================================
	-- TIPO_QUERY = CENTRO, ID, NOMBRE
	-- ================================================================================================================================================
	-- SELECIONANDO POR ID DE CENTRO DE TRABAJO
    IF (@TIPO_QUERY LIKE 'CENTRO')
        BEGIN
            	SELECT
				EM.Numemp AS NumeroEmpleado, EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno, EM.Centro,
				EM.NumeroPuesto, PUES.Nombre AS NombrePuesto
				FROM sapCatalogoEmpleados EM
				INNER JOIN sapcatalogopuestos PUES
				ON EM.NumeroPuesto = PUES.Numero
				WHERE EM.Centro = @id_Centro
				AND PUES.Nombre LIKE 'LIDER%';
        END

    -- SELECIONANDO POR ID DE EMPLEADO
    ELSE IF (@TIPO_QUERY LIKE 'ID')
        BEGIN
			SELECT TOP 1
			EM.Numemp AS NumeroEmpleado, EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno, EM.Centro,
			EM.NumeroPuesto, PUES.Nombre AS NombrePuesto
			FROM sapCatalogoEmpleados EM
			INNER JOIN sapcatalogopuestos PUES
			ON EM.NumeroPuesto = PUES.Numero
			WHERE EM.Numemp = @id_Usuario
			AND EM.Centro = @id_Centro
			AND PUES.Nombre LIKE 'LIDER%';
        END

	-- SELECIONANDO POR ID DE GRUPO DE TRABAJO
    ELSE IF (@TIPO_QUERY LIKE 'NOMBRE')
        BEGIN
			SELECT
			EM.Numemp AS NumeroEmpleado, EM.Nombre, EM.ApellidoPaterno, EM.ApellidoMaterno, EM.Centro,
			EM.NumeroPuesto, PUES.Nombre AS NombrePuesto
			FROM sapCatalogoEmpleados EM
			INNER JOIN sapcatalogopuestos PUES
			ON EM.NumeroPuesto = PUES.Numero
			WHERE EM.Centro = @id_Centro
			AND EM.Nombre LIKE '%' + @nombre_Empleado + '%'
			AND PUES.Nombre LIKE 'LIDER%';
		END
	ELSE
        BEGIN
            SELECT NULL AS CONSULTA;
        END
END