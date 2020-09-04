<?php


DB::query("ALTER TABLE tb_chixan_senedler ADD tarix date DEFAULT getdate()");