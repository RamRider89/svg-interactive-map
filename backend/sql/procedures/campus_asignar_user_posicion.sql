-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Guarda la informacion del usuario en la posicion asignada>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_asignar_user_posicion] (@idEmpleado INT, @nombre VARCHAR(250), @apellidoPaterno VARCHAR(250), @apellidoMaterno VARCHAR(250), @puesto INT, @centro INT, @correo VARCHAR(250), @telefono INT, @lider INT, @gerente INT, @empresa INT, @tipoTrabajo INT, @cumpleanos VARCHAR(250), @fotoUrl VARCHAR(250))
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
	DECLARE @TRUE BIT = 1;
	DECLARE @FALSE BIT = 0;
    DECLARE @USER_HERE INT = 0;

    DECLARE @datePassRegistro AS DATETIME = GETDATE(); -- fecha de registro
    DECLARE @dateCumpleanos AS DATETIME; -- fecha de cumpleaños
	DECLARE @caducado AS BIT = 0;

    IF (@idEmpleado > 0)
        BEGIN
            SET @dateCumpleanos = @cumpleanos;
            SELECT TOP 1 @USER_HERE = id FROM CampusUsersPositions WHERE idEmpleado = @idEmpleado;

            IF (@USER_HERE > 0)
                BEGIN
                    -- EL USUARIO YA TIENE UN REGISTRO ANTIGUO
                    UPDATE CampusUsersPositions SET 
                    nombre = @nombre,
                    apellidoPaterno = @apellidoPaterno,
                    apellidoMaterno = @apellidoMaterno,
                    puesto = @puesto,
                    centro = @centro,
                    correo = @correo,
                    telefono = @telefono,
                    lider = @lider,
                    gerente = @gerente,
                    empresa = @empresa,
                    tipoTrabajo = @tipoTrabajo,
                    cumpleanos = @dateCumpleanos,
                    fotoUrl = @fotoUrl 
                    WHERE id = @USER_HERE AND idEmpleado = @idEmpleado;
                    
                END
            ELSE
                BEGIN
                    -- NUEVO USUARIO
                    INSERT INTO CampusUsersPositions 
                    (idEmpleado, nombre, apellidoPaterno, apellidoMaterno, puesto, centro, correo, telefono, lider, gerente, empresa, tipoTrabajo, cumpleanos, fotoUrl)
                    VALUES (@idEmpleado, @nombre, @apellidoPaterno, @apellidoMaterno, @puesto, @centro, @correo, @telefono, @lider, @gerente, @empresa, @tipoTrabajo, @dateCumpleanos, @fotoUrl);
                END
            
            SELECT @TRUE AS ASIGNADO;
        END
    ELSE
        BEGIN
            SELECT @FALSE AS ASIGNADO;
        END
END