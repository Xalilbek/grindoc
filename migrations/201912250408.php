<?php

// created tb_daxil_olan_senedler_deleted by Emin

DB::query("

CREATE TABLE [dbo].[tb_daxil_olan_senedler_deleted] (
  [id] int  IDENTITY(1,1) NOT NULL,
  [senedin_nomresi] nvarchar(50) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [senedin_daxil_olma_tarixi] datetime  NULL,
  [mektubun_tipi] int  NULL,
  [mektubun_alt_tipi] int  NULL,
  [vereq_sayi] int  NULL,
  [gonderen_teshkilatin_nomresi] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [senedin_tarixi] datetime  NULL,
  [mektub_nezaretdedir] tinyint  NULL,
  [mektubun_qisa_mezmunu] nvarchar(1000) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [gonderen_teshkilat] int  NULL,
  [gonderen_shexs] int  NULL,
  [created_by] int  NULL,
  [is_deleted] tinyint  NULL,
  [TenantId] int  NULL,
  [status] tinyint  NULL,
  [rey_muellifi] int  NULL,
  [yoxlayan_shexs] int  NULL,
  [tip] tinyint  NULL,
  [netice] int  NULL,
  [outgoing_document_id] int  NULL,
  [icra_edilme_tarixi] datetime  NULL,
  [state] tinyint  NULL,
  [document_number_id] int  NULL,
  [document_number] nvarchar(500) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [created_at] datetime  NULL,
  [internal_document_type_id] int  NULL,
  [sened_tip] int  NULL,
  [gonderen_aidiyyati_tabeli_id] int  NULL,
  [emeliyyat_tip] char(1) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [qisa_mezmun_id] int  NULL,
  [derkenar_emeliyyat] tinyint  NULL,
  [derkenar_metn_id] int  NULL,
  [nezaret] tinyint  NULL,
  [daxil_olma_yolu_id] int  NULL,
  [state_before_canceled] tinyint  NULL,
  [son_icra_tarixi] datetime  NULL,
  [belong_to] int  NULL,
  [qeyd] nvarchar(1000) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [baglanma_tarixi] datetime  NULL,
  [tibb_muessisesi] int  NULL,
  [nazalogiya] int  NULL,
  [mektubun_tipi_third] int  NULL,
  [mektubun_mezmunu] int  NULL,
  [qoshma_sayi] int  NULL
)

ALTER TABLE [dbo].[tb_daxil_olan_senedler_deleted] SET (LOCK_ESCALATION = TABLE)



-- ----------------------------
-- Primary Key structure for table tb_daxil_olan_senedler_deleted
-- ----------------------------
ALTER TABLE [dbo].[tb_daxil_olan_senedler_deleted] ADD CONSTRAINT [PK__tb_daxil__3213E83FC61CE13D] PRIMARY KEY CLUSTERED ([id])
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON)  
ON [PRIMARY]

");


// created tb_derkenar_deleted by Emin

DB::query("


CREATE TABLE [dbo].[tb_derkenar_deleted] (
  [id] int  IDENTITY(1,1) NOT NULL,
  [mesul_shexs] int  NULL,
  [nezaretde_saxlanilsin] tinyint  NULL,
  [son_icra_tarixi] date  NULL,
  [derkenar_metn_id] int  NULL,
  [daxil_olan_sened_id] int  NULL,
  [status] tinyint  NULL,
  [elave_olunma_tarixi] datetime DEFAULT (getdate()) NULL,
  [parentTaskId] int  NULL,
  [specifiesResult] tinyint  NULL,
  [created_by] int  NULL,
  [TenantId] int  NULL,
  [daxili_nezaret] tinyint  NULL,
  [group] int  NULL,
  [is_deleted] int DEFAULT ((0)) NULL,
  [approving_status] int  NULL
)

ALTER TABLE [dbo].[tb_derkenar_deleted] SET (LOCK_ESCALATION = TABLE)


-- ----------------------------
-- Primary Key structure for table tb_derkenar_deleted
-- ----------------------------
ALTER TABLE [dbo].[tb_derkenar_deleted] ADD CONSTRAINT [PK__tb_derke__3213E83F8866854B] PRIMARY KEY CLUSTERED ([id])
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON)  
ON [PRIMARY]


");

// created tb_chixan_senedler_deleted by Emin

DB::query("


CREATE TABLE [dbo].[tb_chixan_senedler_deleted] (
  [id] int  IDENTITY(1,1) NOT NULL,
  [kim_gonderir] int  NULL,
  [gonderme_tarixi] datetime  NULL,
  [teyinat] int  NULL,
  [gonderen_teshkilat] int  NULL,
  [gonderen_shexs] int  NULL,
  [senedin_novu] int  NULL,
  [vereq_sayi] int  NULL,
  [mektubun_qisa_mezmunu] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [qeyd] nvarchar(700) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [forma] int  NULL,
  [created_by] int  NULL,
  [created_at] datetime  NULL,
  [is_deleted] tinyint  NULL,
  [TenantId] int  NULL,
  [status] tinyint  NULL,
  [muraciet_tip_id] int  NULL,
  [muraciet_id] int  NULL,
  [document_number_id] int  NULL,
  [is_sended] tinyint  NULL,
  [eleve_nomre] int  NULL,
  [arayis_user_id] int  NULL,
  [arayis_tarixi] datetime  NULL,
  [arayis_ise_qebul_tarixi] datetime  NULL,
  [etibarname_sexs] int  NULL,
  [etibarname_kartin_nomresi] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_avtomobilin_nomresi] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_yanacagin_miqdari] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_vesiqenin_kodu] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_vesiqenin_nomresi] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_vesiqeni_teqdim_eden_orqan] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_etibarliq_muddet] nvarchar(255) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [etibarname_icraci_direktor] int  NULL,
  [etibarname_bas_muhasib] int  NULL,
  [icra_muddeti_vereq_sayi] nvarchar(1000) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [icra_muddeti_muraciet_olunan_tarix] datetime  NULL,
  [icra_muddeti_qeyd] nvarchar(2000) COLLATE SQL_Latin1_General_CP1_CI_AS  NULL,
  [answer_is_not_required] tinyint  NULL,
  [last_operation_date] datetime  NULL,
  [qisa_mezmun_id] int  NULL,
  [muraciet_alt_tip_id] int  NULL
)

ALTER TABLE [dbo].[tb_chixan_senedler_deleted] SET (LOCK_ESCALATION = TABLE)



-- ----------------------------
-- Primary Key structure for table tb_chixan_senedler_deleted
-- ----------------------------
ALTER TABLE [dbo].[tb_chixan_senedler_deleted] ADD CONSTRAINT [PK__tb_chixa__3213E83F5E67EA60] PRIMARY KEY CLUSTERED ([id])
WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON)  
ON [PRIMARY]



");


// tb_notifications add column by Emin


DB::query("

ALTER TABLE tb_notifications
ADD derkenar_id int;

");