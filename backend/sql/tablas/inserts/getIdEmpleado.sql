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
DECLARE @id_Usuario INT = 0;
DECLARE @Nombre VARCHAR;
DECLARE @ApellidoPaterno VARCHAR;
DECLARE @ApellidoMaterno VARCHAR;
DECLARE @NombreCompleto VARCHAR;

	SELECT @COUNT_USERS_DEFAULT = COUNT(DISTINCT Numemp) FROM sapCatalogoEmpleados;
	PRINT ('TOTAL USERS -> ' + CONVERT(varchar(10), @COUNT_USERS_DEFAULT));

	-- declaramos un cursor para recorrer la tabla temporal
	DECLARE CURSOR_USERS_DEFAULT CURSOR FAST_FORWARD READ_ONLY FOR SELECT Numemp, Nombre, ApellidoPaterno, ApellidoMaterno FROM sapCatalogoEmpleados;

	-- cursor
	OPEN CURSOR_USERS_DEFAULT
	FETCH NEXT FROM CURSOR_USERS_DEFAULT INTO @id_Usuario, @Nombre, @ApellidoPaterno, @ApellidoMaterno; 

	WHILE @@FETCH_STATUS = 0
	BEGIN
		SET @NombreCompleto = (RTRIM(LTRIM(@Nombre)) + ' ' + RTRIM(LTRIM(@ApellidoPaterno)) + ' ' + RTRIM(LTRIM(@ApellidoMaterno)) + ' ');
		PRINT ('Usuario econtrado -> ' + CONVERT(varchar(10), @id_Usuario) + ': ' + @NombreCompleto);

		FETCH NEXT FROM CURSOR_USERS_DEFAULT INTO @id_Usuario;
	END

	CLOSE CURSOR_USERS_DEFAULT
	DEALLOCATE CURSOR_USERS_DEFAULT

END
