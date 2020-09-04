<?php

$id = setTableData('tb_prodoc_document_number_pattern', ['direction' => 'incoming'], [
    'option_id' => '-1',
    'initial_number' => '1',
    'editable' => '0',
    'editable_with_select' => '1',
    'repeat_appeal' => '0',
    'is_deleted' => NULL,
    'pattern_prefix' => 'DOS-',
    'pattern' => '$seria$-$il$',
    'created_by' => NULL,
    'created_at' => NULL,
    'TenantId' => $activeTenant,
    'direction' => 'incoming',
    'initial_number_for_next_years' => '1',
    'set_number_after_approval' => '0',
]);

setTableData('tb_prodoc_document_number_pattern_document_type', [ 'document_number_patter_id' => $id ], [
    'document_type_id' => '-1',
    'document_number_patter_id' => $id
]);

setTableData('tb_prodoc_document_number_pattern_option_value', [ 'document_number_patter_id' => $id ], [
    'option_value_id' => '-1',
    'document_number_patter_id' => $id
]);

$id = setTableData('tb_prodoc_document_number_pattern', ['direction' => 'outgoing'], [
    'option_id' => '-1',
    'initial_number' => '1',
    'editable' => NULL,
    'editable_with_select' => NULL,
    'repeat_appeal' => NULL,
    'is_deleted' => '0',
    'pattern_prefix' => 'XOS-',
    'pattern' => '$seria$-$il$',
    'created_by' => $userId,
    'created_at' => NULL,
    'TenantId' => $activeTenant,
    'direction' => 'outgoing',
    'initial_number_for_next_years' => '1',
    'set_number_after_approval' => '0',
]);

setTableData('tb_prodoc_document_number_pattern_document_type', [ 'document_number_patter_id' => $id ], [
    'document_type_id' => '-1',
    'document_number_patter_id' => $id
]);

setTableData('tb_prodoc_document_number_pattern_option_value', [ 'document_number_patter_id' => $id ], [
    'option_value_id' => '-1',
    'document_number_patter_id' => $id
]);

$id = setTableData('tb_prodoc_document_number_pattern', ['direction' => 'internal'], [
    'option_id' => '-1',
    'initial_number' => '1',
    'editable' => '0',
    'editable_with_select' => '0',
    'repeat_appeal' => '0',
    'is_deleted' => '0',
    'pattern_prefix' => 'DS-',
    'pattern' => '$seria$-$il$',
    'created_by' => $userId,
    'created_at' => NULL,
    'TenantId' => $activeTenant,
    'direction' => 'internal',
    'initial_number_for_next_years' => '1',
    'set_number_after_approval' => '0',
]);

setTableData('tb_prodoc_document_number_pattern_document_type', [ 'document_number_patter_id' => $id ], [
    'document_type_id' => '-1',
    'document_number_patter_id' => $id
]);

setTableData('tb_prodoc_document_number_pattern_option_value', [ 'document_number_patter_id' => $id ], [
    'option_value_id' => '-1',
    'document_number_patter_id' => $id
]);
