USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusUsersPositions]    Script Date: 25/08/2023 12:41:42 p. m. ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[CampusUsersPositions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[idEmpleado] [int] NOT NULL,
	[nombre] [varchar](250) NOT NULL,
	[apellidoPaterno] [varchar](250) NOT NULL,
	[apellidoMaterno] [varbinary](250) NULL,
	[puesto] [int] NOT NULL,
	[centro] [int] NOT NULL,
	[correo] [varchar](250) NOT NULL,
	[telefono] [int] NOT NULL,
	[lider] [int] NOT NULL,
	[gerente] [int] NOT NULL,
	[empresa] [int] NOT NULL,
	[tipoTrabajo] [int] NOT NULL,
	[cumpleanos] [date] NULL,
	[fotoUrl] [varchar](250) NULL,
 CONSTRAINT [PK_CampusUsersPositions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[CampusUsersPositions]  WITH CHECK ADD  CONSTRAINT [FK_CampusUsersPositions_CampusTipoTrabajoUsers] FOREIGN KEY([tipoTrabajo])
REFERENCES [dbo].[CampusTipoTrabajoUsers] ([id])
GO

ALTER TABLE [dbo].[CampusUsersPositions] CHECK CONSTRAINT [FK_CampusUsersPositions_CampusTipoTrabajoUsers]
GO


