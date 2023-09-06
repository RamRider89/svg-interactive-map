USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusGrupoTrabajo]    Script Date: 25/08/2023 11:00:02 a. m. ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[CampusGrupoTrabajo](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[tipo] [int] NOT NULL,
	[nombre] [varchar](10) NOT NULL,
	[edificio] [varchar](10) NULL,
	[asignado] [bit] NOT NULL,
	[gerenteAsignado] [int] NULL,
	[fechaAsignado] [datetime] NULL
 CONSTRAINT [PK_CampusGrupoTrabajo] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[CampusGrupoTrabajo]  WITH CHECK ADD  CONSTRAINT [FK_CampusGrupoTrabajo_CampusTiposGrupoTrabajo] FOREIGN KEY([tipo])
REFERENCES [dbo].[CampusTiposGrupoTrabajo] ([id])
GO

ALTER TABLE [dbo].[CampusGrupoTrabajo] CHECK CONSTRAINT [FK_CampusGrupoTrabajo_CampusTiposGrupoTrabajo]
GO

ALTER TABLE [dbo].[CampusGrupoTrabajo]  WITH CHECK ADD  CONSTRAINT [FK_Gerente_CampusGrupoTrabajo_CampusUsersPositions] FOREIGN KEY([gerenteAsignado])
REFERENCES [dbo].[CampusUsersPositions] ([id])
GO

ALTER TABLE [dbo].[CampusGrupoTrabajo] CHECK CONSTRAINT [FK_Gerente_CampusGrupoTrabajo_CampusUsersPositions]
GO


