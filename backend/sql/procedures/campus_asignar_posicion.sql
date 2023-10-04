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
ALTER PROCEDURE [dbo].[campus_asignar_posicion] (@TIPO_MOV INT, @idPosition INT, @idEmpleado INT)
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
-- TIPO_MOV => 1:ALTA || 0:BAJA
-- ================================================================================================================================================	
    DECLARE @TRUE BIT = 1;
	DECLARE @FALSE BIT = 0;
    DECLARE @datePassRegistro AS DATETIME = GETDATE(); -- fecha de registro
    SET @TIPO_MOV = CAST(@TIPO_MOV AS BIT); -- true || false
    SET @idEmpleado = CAST(@idEmpleado * @TIPO_MOV AS INT);
    
    IF (@idPosition > 0)
        BEGIN
            UPDATE CampusPositions SET asignado = @FALSE WHERE idEmpleado = @idEmpleado;    -- reset all positions
            UPDATE CampusPositions SET asignado = @TIPO_MOV, idEmpleado = @idEmpleado, fechaAsignado = @datePassRegistro WHERE id = @idPosition;
            SELECT @TRUE AS ASIGNADO;
        END
    ELSE
        BEGIN
            SELECT @FALSE AS ASIGNADO;
        END
END