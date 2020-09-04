<?php
pdof()->query("create table tb_chixan_senedler_imzalayan_shexsler
(
	id int identity
		constraint tb_chixan_senedler_imzalayan_shexsler_pk
			primary key nonclustered,
	user_id int not null,
	document_id int not null
)");