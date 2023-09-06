USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusPositions]    Script Date: 25/08/2023 11:00:33 a. m. ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[CampusPositions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[idGrupo] [int] NOT NULL,
	[nombre] [varchar](10) NOT NULL,
	[asignado] [bit] NOT NULL,
	[usuarioAsignado] [int] NULL,
	[fechaAsignado] [datetime] NULL,
 CONSTRAINT [PK_CampusPositions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[CampusPositions]  WITH CHECK ADD  CONSTRAINT [FK_CampusPositions_CampusGrupoTrabajo] FOREIGN KEY([idGrupo])
REFERENCES [dbo].[CampusGrupoTrabajo] ([id])
GO

ALTER TABLE [dbo].[CampusPositions] CHECK CONSTRAINT [FK_CampusPositions_CampusGrupoTrabajo]
GO


