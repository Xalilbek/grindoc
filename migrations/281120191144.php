<?php

pdof() -> query( "
                    CREATE TABLE [tb_prodoc_sened_novu_rol] (
                        [id] INT NOT NULL IDENTITY (1, 1),
                        [tip] int ,
                        [rol] int ,
                    )
               ");

pdof() -> query( "
                    CREATE TABLE [tb_prodoc_mektubun_tipi_rol] (
                        [id] INT NOT NULL IDENTITY (1, 1),
                        [tip] int ,
                        [rol] int ,
                    )
               ");