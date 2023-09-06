-- ================================================================================================================================================
-- CAMPUS MAPA DIGITAL
-- ================================================================================================================================================
-- ================================================================================================================================================
-- Author:		<Carlos David Duarte>
-- Email:		david.duarte@coppel.com
-- Create date: <01/02/2023>
-- Description:	<Obtiene la informacion del empleado>
-- ================================================================================================================================================
USE Personal
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

BEGIN

-- select * from campuscontactos where name like 'Aaron Alberto Berrelleza Paredes';
-- Aaron Alberto Berrelleza Paredes

DECLARE @COUNT_USERS_DEFAULT INT = 0;
DECLARE @idContacto INT = 0;
DECLARE @id_Usuario INT = 0;
DECLARE @Nombre VARCHAR(500);
DECLARE @ApellidoPaterno VARCHAR(500);
DECLARE @ApellidoMaterno VARCHAR(500);
DECLARE @NombreCompleto VARCHAR(500);

DECLARE @nameContacto VARCHAR(500);

	SELECT @COUNT_USERS_DEFAULT = COUNT(DISTINCT id) FROM CampusContactos;
	PRINT ('TOTAL USERS -> ' + CONVERT(varchar(10), @COUNT_USERS_DEFAULT));

	-- declaramos un cursor para recorrer la tabla temporal
	DECLARE CURSOR_USERS_DEFAULT CURSOR FAST_FORWARD READ_ONLY FOR SELECT C.id, C.name FROM CampusContactos C;

	-- cursor
	OPEN CURSOR_USERS_DEFAULT
	FETCH NEXT FROM CURSOR_USERS_DEFAULT INTO @idContacto, @nameContacto; 

	WHILE @@FETCH_STATUS = 0
	BEGIN
        SELECT TOP 1 @id_Usuario = E.Numemp from sapCatalogoEmpleados E 
        WHERE (RTRIM(LTRIM(Nombre)) + ' ' + RTRIM(LTRIM(ApellidoPaterno)) + ' ' + RTRIM(LTRIM(ApellidoMaterno)) + ' ') 
        LIKE @nameContacto;

        IF (@id_Usuario > 0)
            BEGIN
                --SET @NombreCompleto = (RTRIM(LTRIM(@Nombre)) + ' ' + RTRIM(LTRIM(@ApellidoPaterno)) + ' ' + RTRIM(LTRIM(@ApellidoMaterno)) + ' ');
                PRINT ('Usuario econtrado -> ' + CONVERT(varchar(10), @id_Usuario) + ': ' + @nameContacto);
                --UPDATE CampusContactos SET idCoppel = @id_Usuario WHERE id = @idContacto;
            END
        ELSE
            BEGIN
                PRINT ('Usuario NO econtrado -> ' + @nameContacto);
                UPDATE CampusContactos SET idCoppel = NULL WHERE id = @idContacto;
            END

        SET @id_Usuario = 0;

		FETCH NEXT FROM CURSOR_USERS_DEFAULT INTO @idContacto, @nameContacto; 
	END

	CLOSE CURSOR_USERS_DEFAULT
	DEALLOCATE CURSOR_USERS_DEFAULT

END
