-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del contacto>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_get_contactos] (@TIPO_QUERY VARCHAR(20), @idBusqueda INT, @strBusqueda VARCHAR(250))
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
-- TIPO_QUERY = ALL, ID, IDCOPPEL, NOMBRE, EMAIL
-- CampusContactos
-- ================================================================================================================================================
    -- SELECIONANDO TODOS
    IF (@TIPO_QUERY LIKE 'ALL')
        BEGIN
            SELECT * FROM CampusContactos;
        END

    -- SELECIONANDO POR ID CONTACTO
    ELSE IF (@TIPO_QUERY LIKE 'ID')
        BEGIN
            IF (@idBusqueda > 0)
                BEGIN
                    SELECT * FROM CampusContactos WHERE id = @idBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR ID COPPEL DEL CONTACTO
    ELSE IF (@TIPO_QUERY LIKE 'IDCOPPEL')
        BEGIN
            IF (@idBusqueda > 0)
                BEGIN
                    SELECT * FROM CampusContactos WHERE idCoppel = @idBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR NOMBRE
    ELSE IF (@TIPO_QUERY LIKE 'NOMBRE')
        BEGIN
            IF (@strBusqueda NOT LIKE '')
                BEGIN
                    SELECT * FROM CampusContactos C WHERE C.name LIKE @strBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR EMAIL
    ELSE IF (@TIPO_QUERY LIKE 'EMAIL')
        BEGIN
            IF (@strBusqueda NOT LIKE '')
                BEGIN
                    SELECT * FROM CampusContactos C WHERE C.email LIKE @strBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    ELSE
        BEGIN
            SELECT NULL AS CONSULTA;
        END
END