USE [ConstruNet_des]
GO

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

-- creamos una tabla temporal para guardar a las oficinas solamente
DECLARE @tempOficinas TABLE (
[id] [INT] NULL, 
[nombre] [varchar](20) NULL,
[edificio] [varchar](20) NULL,
[TipoGrupo] [varchar](20) NULL);

-- Guardando a las oficinas solamente
INSERT INTO @tempOficinas
SELECT G.id, G.nombre,G.edificio, T.name AS TipoGrupo FROM CampusGrupoTrabajo G
INNER JOIN CampusTiposGrupoTrabajo T
ON G.tipo = T.id
WHERE tipo = 2;

-- SELECT * FROM @tempOficinas

-- Revisamos la tabla temporal @tempOficinas que no este vacia
IF (EXISTS (SELECT 1 FROM @tempOficinas))
	BEGIN
        DECLARE @COUNT_OFICINAS INT = 0;
		DECLARE @idOficina INT = 0;
		DECLARE @nameOficina VARCHAR(20);

        SELECT @COUNT_OFICINAS = COUNT(DISTINCT id) FROM @tempOficinas;
		PRINT ('OFICINAS -> ' + CONVERT(varchar(10), @COUNT_OFICINAS));

		-- declaramos un cursor para recorrer la tabla temporal
		DECLARE CURSOR_OFICINAS CURSOR FAST_FORWARD READ_ONLY FOR SELECT DISTINCT id, nombre FROM @tempOficinas;

		DECLARE @Counter INT;
		SET @Counter = 1;
		DECLARE @nombrePosicion VARCHAR(20);

		-- cursor
		OPEN CURSOR_OFICINAS
		FETCH NEXT FROM CURSOR_OFICINAS INTO @idOficina, @nameOficina; 

		WHILE @@FETCH_STATUS = 0
		BEGIN
			PRINT ('Oficina registrada -> ' + @nameOficina);
			
			SET @nombrePosicion = @nameOficina + '_' + CONVERT(VARCHAR,@Counter);
			PRINT 'Posicion: ' + @nombrePosicion; 

			INSERT INTO [dbo].[CampusPositions] VALUES (@idOficina, @nombrePosicion, 0, NULL, NULL);

			FETCH NEXT FROM CURSOR_OFICINAS INTO @idOficina, @nameOficina; 
		END

		CLOSE CURSOR_OFICINAS
		DEALLOCATE CURSOR_OFICINAS

	END
ELSE
	BEGIN
		PRINT 'HERRADURAS -> VACIO'
	END

