-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del grupo de trabajo>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_get_grupo_trabajo] (@TIPO_QUERY VARCHAR(20), @idBusqueda INT, @nombreEdificio VARCHAR(10), @lugarAsignado BIT)
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
-- TIPO_QUERY = ALL, ID, TIPO, EDIFICIO {EA || EB}, ASIGNADO {0 || 1}, GERENTE {claveCoppel}    
-- ================================================================================================================================================
    -- SELECIONANDO POR ID DE GRUPO DE TRABAJO
    IF (@TIPO_QUERY LIKE 'ALL')
        BEGIN
            SELECT
            G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
            FROM CampusGrupoTrabajo G
            INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
        END

    -- SELECIONANDO POR ID DE GRUPO DE TRABAJO
    ELSE IF (@TIPO_QUERY LIKE 'ID')
        BEGIN
            IF (@idBusqueda > 0)
                BEGIN
                    SELECT
                    G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
                    FROM CampusGrupoTrabajo G
                    INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
                    WHERE G.id = @idBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR TIPO DE GRUPO DE TRABAJO
    ELSE IF (@TIPO_QUERY LIKE 'TIPO')
        BEGIN
            IF (@idBusqueda > 0)
                BEGIN
                    SELECT
                    G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
                    FROM CampusGrupoTrabajo G
                    INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
                    WHERE G.tipo = @idBusqueda;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR EDIFICIO
    ELSE IF (@TIPO_QUERY LIKE 'EDIFICIO')
        BEGIN
            IF (@nombreEdificio LIKE 'EA' OR @nombreEdificio LIKE 'EB')
                BEGIN
                    SELECT
                    G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
                    FROM CampusGrupoTrabajo G
                    INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
                    WHERE G.edificio LIKE @nombreEdificio;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR GRUPOS ASIGNADOS
    ELSE IF (@TIPO_QUERY LIKE 'ASIGNADO')
        BEGIN
            IF (@lugarAsignado = 0 OR @lugarAsignado = 1)
                BEGIN
                    SELECT
                    G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
                    FROM CampusGrupoTrabajo G
                    INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
                    WHERE G.asignado = @lugarAsignado;
                END
            ELSE
                BEGIN
                    SELECT NULL AS CONSULTA;
                END
        END

    -- SELECIONANDO POR GRUPOS ASIGNADOS A GERENTE
    ELSE IF (@TIPO_QUERY LIKE 'GERENTE')
        BEGIN
            IF (@idBusqueda > 0)
                BEGIN
                    SELECT
                    G.id, G.tipo, T.name AS nombreTipo, G.edificio, G.asignado, G.gerenteAsignado, G.fechaAsignado
                    FROM CampusGrupoTrabajo G
                    INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
                    WHERE G.gerenteAsignado = @idBusqueda;
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