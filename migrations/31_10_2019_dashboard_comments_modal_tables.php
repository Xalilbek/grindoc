<?php

pdof() -> query( "
                    CREATE TABLE [tb_prodoc_comments] (
                        [id] INT NOT NULL IDENTITY (1, 1),
                        [created_by] int,
                        [document_id] int,
                        [text] nvarchar (4000),
                        [created_at] datetime2 ,
                        [is_deleted] tinyint DEFAULT (0),
                    )
               ");

pdof() -> query( "
                    CREATE TABLE [tb_prodoc_reaction_smile_names] (
                        [id] INT NOT NULL IDENTITY (1, 1),
                        [smile_name] varchar (255),
                        [is_deleted] tinyint ,
                    )
               ");

pdof() -> query( "
                    CREATE TABLE [tb_prodoc_reaction_smile] (
                        [id] INT NOT NULL IDENTITY (1, 1),
                        [created_by] int,
                        [smile_id] int,
                        [comment_id] int,
                        [is_deleted] tinyint ,
                    )
               ");
