USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusLogPositions]    Script Date: 04/09/2023 03:12:38 p. m. ******/
-- tipoMovimiento A: B: C

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[CampusLogPositions](
	[id] [int] NOT NULL,
	[idEmpleado] [int] NOT NULL,
	[emailEmpleado] [varchar](250) NULL,
	[idCentro] [int] NOT NULL,
	[idPosition] [int] NOT NULL,
	[codigo] [int] NOT NULL,
	[tipoMovimiento] [nchar](10) NOT NULL,
	[fechaRegistro] [datetime] NULL,
	[caducado] [bit] NOT NULL,
 CONSTRAINT [PK_CampusLogPositions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[CampusLogPositions]  WITH CHECK ADD  CONSTRAINT [FK_CampusLogPositions_CampusPositions] FOREIGN KEY([idPosition])
REFERENCES [dbo].[CampusPositions] ([id])
GO

ALTER TABLE [dbo].[CampusLogPositions] CHECK CONSTRAINT [FK_CampusLogPositions_CampusPositions]
GO



