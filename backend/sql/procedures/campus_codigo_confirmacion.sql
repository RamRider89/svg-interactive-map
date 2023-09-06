-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Guarda el codigo de confirmacion para asignar la posicion>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_codigo_confirmacion] (@TIPO_QUERY VARCHAR(20), @idEmpleado INT, @emailEmpleado VARCHAR(250), @idCentro INT, @idPosition INT, @codigo INT, @tipoMovimiento nvarchar(10))
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
-- TIPO_QUERY = REGISTRAR || CONFIRMAR
-- TIPOS DE MOVIMIENTOS = ALTA || BAJA || CAMBIAR
-- ================================================================================================================================================
    DECLARE @FALSE BIT = 0;
    DECLARE @TRUE BIT = 1;
    DECLARE @fechaRegistro DATETIME = GETDATE();
    DECLARE @caducado BIT = 0;
    DECLARE @posAsignada BIT = 0;
    DECLARE @userAsignado INT;
    DECLARE @confirmarCodigo INT;

    DECLARE @CONSULTA BIT = 0;
    DECLARE @REGISTRADO BIT = 0;
    DECLARE @CONFIRMADO BIT = 0;

    -- OBTENINEDO INFORMACION DE LA POSICION BY ID
    SELECT TOP 1 @posAsignada = C.asignado, @userAsignado = C.idEmpleado FROM CampusPositions C WHERE C.id = @idPosition;


    -- ALTA DE NUEVO REGISTRO
    IF (@tipoMovimiento LIKE 'A')
        BEGIN
            IF (@posAsignada = 0)
                BEGIN
                     -- REGISTRANDO NUEVO CODIGO DE ALTA
                    IF (@TIPO_QUERY LIKE 'REGISTRAR')
                        BEGIN
                            UPDATE CampusLogPositions SET caducado = 1 WHERE idEmpleado = @idEmpleado;
                            INSERT INTO CampusLogPositions VALUES (@idEmpleado, @emailEmpleado, @idCentro, @idPosition, @codigo, @tipoMovimiento, @fechaRegistro, @caducado);
                            SELECT @TRUE AS CONSULTA, @TRUE AS REGISTRADO;
                        END

                    -- CONFIRMANDO CODIGO
                    ELSE IF (@TIPO_QUERY LIKE 'CONFIRMAR')
                        BEGIN
                            SELECT TOP 1 @confirmarCodigo = L.codigo FROM CampusLogPositions L WHERE L.codigo = @codigo AND L.idEmpleado = @idEmpleado;

                            IF (@confirmarCodigo > 0)
                                BEGIN
                                    UPDATE CampusLogPositions SET caducado = 1 WHERE idEmpleado = @idEmpleado AND codigo = @codigo AND caducado = 0;
                                    SELECT @TRUE AS CONSULTA, @TRUE AS CONFIRMADO;
                                END
                            ELSE
                                BEGIN
                                    SELECT @TRUE AS CONSULTA, @FALSE AS CONFIRMADO;
                                END
                        END

                    -- TIPO DE CONSULTA ERRONEA
                    ELSE
                        BEGIN
                            SELECT @FALSE AS CONSULTA;
                        END

                END

            -- LAS POSICION YA ESTA ASIGNADA
            ELSE
                BEGIN
                    SELECT @TRUE AS CONSULTA, @FALSE AS REGISTRADO;
                END
        END

    -- BAJA DE REGISTRO || -- CAMBIO DE REGISTRO
    ELSE IF (@tipoMovimiento LIKE 'B' OR @tipoMovimiento LIKE 'C')
        BEGIN
            IF (@userAsignado = @idEmpleado)
                BEGIN
                     -- REGISTRANDO NUEVO CODIGO DE BAJA
                    IF (@TIPO_QUERY LIKE 'REGISTRAR')
                        BEGIN
                            UPDATE CampusLogPositions SET caducado = 1 WHERE idEmpleado = @idEmpleado;
                            INSERT INTO CampusLogPositions VALUES (@idEmpleado, @emailEmpleado, @idCentro, @idPosition, @codigo, @tipoMovimiento, @fechaRegistro, @caducado);
                            SELECT @TRUE AS CONSULTA, @TRUE AS REGISTRADO;
                        END

                    -- CONFIRMANDO CODIGO
                    ELSE IF (@TIPO_QUERY LIKE 'CONFIRMAR')
                        BEGIN
                            SELECT TOP 1 @confirmarCodigo = L.codigo FROM CampusLogPositions L WHERE L.codigo = @codigo AND L.idEmpleado = @idEmpleado;

                            IF (@confirmarCodigo > 0)
                                BEGIN
                                    UPDATE CampusLogPositions SET caducado = 1 WHERE idEmpleado = @idEmpleado AND codigo = @codigo AND caducado = 0;
                                    SELECT @TRUE AS CONSULTA, @TRUE AS CONFIRMADO;
                                END
                            ELSE
                                BEGIN
                                    SELECT @TRUE AS CONSULTA, @FALSE AS CONFIRMADO;
                                END
                        END

                    -- TIPO DE CONSULTA ERRONEA
                    ELSE
                        BEGIN
                            SELECT @FALSE AS CONSULTA;
                        END

                END

            -- LAS POSICION YA ESTA ASIGNADA
            ELSE
                BEGIN
                    SELECT @TRUE AS CONSULTA, @FALSE AS REGISTRADO;
                END
        END

    -- NA
    ELSE
        BEGIN
            SELECT @FALSE AS CONSULTA;
        END
END