<?php

pdof()->query("
        CREATE TABLE tb_arayish_teqdim_edilen_qurum (
        id int IDENTITY(1,1) PRIMARY KEY,
        name varchar(200) NOT NULL,
        is_deleted int DEFAULT 0
    )
");