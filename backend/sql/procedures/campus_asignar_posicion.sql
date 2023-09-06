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
CREATE PROCEDURE [dbo].[campus_asignar_posicion] (@idEmpleado INT, @nombre VARCHAR(250), @apellidoPaterno VARCHAR(250), @apellidoMaterno VARCHAR(250), @puesto INT, @centro INT, @correo VARCHAR(250), @telefono INT, @lider INT, @gerente INT, @empresa INT, @tipoTrabajo INT, @cumpleanos DATE, @fotoUrl VARCHAR(250))
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
	DECLARE @TRUE BIT = 1;
	DECLARE @FALSE BIT = 0;

    DECLARE @datePassRegistro AS DATETIME = GETDATE(); -- fecha de registro
	DECLARE @caducado AS BIT = 0;

    IF (@idEmpleado > 0)
        BEGIN
            INSERT INTO CampusUsersPositions 
            (idEmpleado, nombre, apellidoPaterno, apellidoMaterno, puesto, centro, correo, telefono, lider, gerente, empresa, tipoTrabajo, cumpleanos, fotoUrl)
            VALUES (@idEmpleado, @nombre, @apellidoPaterno, @apellidoMaterno, @puesto, @centro, @correo, @telefono, @lider, @gerente, @empresa, @tipoTrabajo, @cumpleanos, @fotoUrl);

            SELECT @TRUE AS ASIGNADO;
        END
    ELSE
        BEGIN
            SELECT @FALSE AS ASIGNADO;
        END
END