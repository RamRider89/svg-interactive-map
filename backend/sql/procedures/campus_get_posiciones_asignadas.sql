-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del empleado>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_get_posiciones] (@asignada INT)
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
	IF @asignada <= 1
	-- solo las posiciones asignadas o libres
		BEGIN
		    SELECT * FROM CampusPositions P
			WHERE P.asignado = @asignada
		END
	ELSE IF @asignada = 2
	-- solo las posiciones asignadas en relacion a los usuarios
		BEGIN
			SELECT 
			P.id, P.nombre, P.fechaAsignado,
			U.idEmpleado, U.nombre AS nombreEmpleado, U.apellidoPaterno, U.apellidoMaterno, U.puesto, U.centro, U.lider, U.gerente, U.empresa, U.tipoTrabajo
			FROM CampusPositions P
			INNER JOIN CampusUsersPositions U
			ON P.idEmpleado = U.idEmpleado
			WHERE P.asignado = 1
		END
	ELSE IF @asignada = 3
	-- todas las posiciones
		BEGIN
			SELECT * FROM CampusPositions
		END
	ELSE
		BEGIN
			SELECT 0 AS CONSULTA
		END

END