-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion de la posicion>
-- ================================================================================================================================================
USE ConstruNet_des
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- ================================================================================================================================================
ALTER PROCEDURE [dbo].[campus_get_posicion] (@idPosicion INT)
AS
BEGIN
SET NOCOUNT ON;
-- ================================================================================================================================================
    IF (@idPosicion > 0)
        BEGIN
            SELECT TOP 1
            C.id, C.nombre AS nombrePosicion, C.asignado, C.idEmpleado, C.fechaAsignado,
            C.idGrupo, G.nombre AS nombreGrupo, T.name AS nombreTipo, G.edificio, G.asignado AS grupoAsignado, 
            G.gerenteAsignado, G.fechaAsignado AS gerenteFechaAsignado
            FROM CampusPositions C
            INNER JOIN CampusGrupoTrabajo G ON C.idGrupo = G.id
            INNER JOIN CampusTiposGrupoTrabajo T ON G.tipo = T.id
            WHERE C.id = @idPosicion;
        END
    ELSE
        BEGIN
            SELECT NULL AS id;
        END
END