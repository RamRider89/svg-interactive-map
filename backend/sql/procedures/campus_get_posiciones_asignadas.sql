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
	IF @asignada > 1
		BEGIN
			SELECT 
			P.id, P.nombre, P.idEmpleado, P.fechaAsignado,
			U.idEmpleado, U.nombre, U.apellidoPaterno, U.apellidoMaterno, U.puesto, U.centro, U.lider, U.gerente, U.empresa, U.tipoTrabajo
			FROM CampusPositions P
			INNER JOIN CampusUsersPositions U
			ON P.idEmpleado = U.id
		END
	ELSE
		BEGIN
		    SELECT 
			P.id, P.nombre, P.idEmpleado, P.fechaAsignado,
			U.idEmpleado, U.nombre, U.apellidoPaterno, U.apellidoMaterno, U.puesto, U.centro, U.lider, U.gerente, U.empresa, U.tipoTrabajo
			FROM CampusPositions P
			INNER JOIN CampusUsersPositions U
			ON P.idEmpleado = U.id
			WHERE P.asignado = @asignada
		END

END