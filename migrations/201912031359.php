<?php

pdof()->query("ALTER TABLE tb_prodoc_alt_privilegiyalar
ADD is_hidden int NOT NULL
DEFAULT 0");
