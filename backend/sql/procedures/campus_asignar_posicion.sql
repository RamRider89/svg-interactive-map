-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Guarda la informacion de la posicion>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_asignar_posicion] (@idPosition INT, @idEmpleado INT)
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
	DECLARE @TRUE BIT = 1;
	DECLARE @FALSE BIT = 0;

    DECLARE @datePassRegistro AS DATETIME = GETDATE(); -- fecha de registro
	DECLARE @caducado AS BIT = 0;

    IF (@idPosition > 0 AND @idEmpleado > 0)
        BEGIN

            UPDATE CampusPositions SET asignado = @FALSE WHERE idEmpleado = @idEmpleado;    -- reset all positions
            UPDATE CampusPositions SET asignado = @TRUE, idEmpleado = @idEmpleado, fechaAsignado = @datePassRegistro WHERE id = @idPosition;

            SELECT @TRUE AS ASIGNADO;
        END
    ELSE
        BEGIN
            SELECT @FALSE AS ASIGNADO;
        END
END