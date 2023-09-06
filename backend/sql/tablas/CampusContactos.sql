USE [ConstruNet_des]
GO

/****** Object:  Table [dbo].[CampusContactos]    Script Date: 25/08/2023 11:39:52 a. m. ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[CampusContactos](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[idCoppel] [int] NULL,
	[name] [varchar](400) NULL,
	[email] [varchar](400) NULL,
 CONSTRAINT [PK_CampusContactos] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


