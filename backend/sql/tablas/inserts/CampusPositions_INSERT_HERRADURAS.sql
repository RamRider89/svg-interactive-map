USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusContactos]    Script Date: 25/08/2023 11:39:52 a. m. ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

-- creamos una tabla temporal para guardar a las herraduras solamente
DECLARE @tempHerraduras TABLE (
[id] [INT] NULL, 
[nombre] [varchar](20) NULL,
[edificio] [varchar](20) NULL,
[TipoGrupo] [varchar](20) NULL);

-- Guardando a las herraduras solamente
INSERT INTO @tempHerraduras
SELECT G.id, G.nombre,G.edificio, T.name AS TipoGrupo FROM CampusGrupoTrabajo G
INNER JOIN CampusTiposGrupoTrabajo T
ON G.tipo = T.id
WHERE tipo = 1;

-- select * from @tempHerraduras

-- Revisamos la tabla temporal @tempHerraduras que no este vacia
IF (EXISTS (SELECT 1 FROM @tempHerraduras))
	BEGIN
        DECLARE @COUNT_HERRADURAS INT = 0;
		DECLARE @PASS_HASH_DEFAULT VARCHAR(128);
		DECLARE @idHerradura INT = 0;
		DECLARE @nameHerradura VARCHAR(20);

        SELECT @COUNT_HERRADURAS = COUNT(DISTINCT id) FROM @tempHerraduras;
		PRINT ('HERRADURAS -> ' + CONVERT(varchar(10), @COUNT_HERRADURAS));

		-- declaramos un cursor para recorrer la tabla temporal
		DECLARE CURSOR_HERRADURAS CURSOR FAST_FORWARD READ_ONLY FOR SELECT DISTINCT id, nombre FROM @tempHerraduras;

		DECLARE @Counter INT;
		DECLARE @nombrePosicion VARCHAR(20);

		-- cursor
		OPEN CURSOR_HERRADURAS
		FETCH NEXT FROM CURSOR_HERRADURAS INTO @idHerradura, @nameHerradura; 

		WHILE @@FETCH_STATUS = 0
		BEGIN
			PRINT ('Herradura registrada -> ' + @nameHerradura);
			SET @Counter = 1;
			WHILE (@Counter <= 13)
			BEGIN
				SET @nombrePosicion = @nameHerradura + '_' + CONVERT(VARCHAR,@Counter);
				PRINT 'Posicion: ' + @nombrePosicion; 

				INSERT INTO [dbo].[CampusPositions] VALUES (@idHerradura, @nombrePosicion, 0, NULL, NULL);
				SET @Counter = @Counter + 1;
			END

			FETCH NEXT FROM CURSOR_HERRADURAS INTO @idHerradura, @nameHerradura; 
		END

		CLOSE CURSOR_HERRADURAS
		DEALLOCATE CURSOR_HERRADURAS

	END
ELSE
	BEGIN
		PRINT 'HERRADURAS -> VACIO'
	END

