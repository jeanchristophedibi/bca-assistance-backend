<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissionsData = [
            ['name' => \App\Enums\PermissionEnum::CREATE_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::RESET_USER, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_EXPIRED_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ACCEPT_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::REJECT_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CANCEL_ASSIGNMENT_REQUEST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::OPEN_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::REALIZE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_REALIZED_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_QUOTE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_QUOTE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_QUOTE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_QUOTE_WITH_CONDITION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_QUOTE_BY_REPAIRER_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_QUOTE_BY_REPAIRER_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_QUOTE_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_QUOTE_WITH_CONDITIONS_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_QUOTE_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_WORKSHEET_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_WORK_SHEET_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_WORK_SHEET_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::EDIT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_EDITED_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_BY_REPAIRER_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_BY_REPAIRER_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_BY_EXPERT_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CLOSE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::MARK_AS_PAID_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::MARK_AS_UNPAID_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CANCEL_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::GENERATE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ASSIGNMENT_STATISTICS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_EDITION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_EDITION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_FIRST_CORRECTION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_FIRST_CORRECTION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VALIDATE_SECOND_CORRECTION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UNVALIDATE_SECOND_CORRECTION_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CANCEL_QUOTE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::RESTORE_DELETED_ASSIGNMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CANCEL_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::GENERATE_INVOICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::INVOICE_STATISTICS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAYMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAYMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAYMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAYMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CANCEL_PAYMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::PAYMENT_STATISTICS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_SHOCK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_SHOCK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_SHOCK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_SHOCK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_SHOCK_WORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_SHOCK_WORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_SHOCK_WORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_SHOCK_WORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_SHOCK_POINT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_WORKFORCE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_WORKFORCE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_WORKFORCE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_WORKFORCE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_WORKFORCE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_ASSIGNMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_EXPERTISE_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_GENERAL_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_CLAIM_NATURE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_TECHNICAL_CONCLUSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_DOCUMENT_TRANSMITTED, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_ASSIGNMENT_DOCUMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_STATUS, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ROLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ROLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ROLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ROLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PERMISSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PERMISSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PERMISSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PERMISSION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_ENTITY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_ENTITY_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_VEHICLE_MODEL, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_VEHICLE_STATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_BRAND, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_COLOR, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_NUMBER_PAINT_ELEMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PAINT_PRODUCT_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_OTHER_COST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_OTHER_COST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_OTHER_COST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_OTHER_COST, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_OTHER_COST_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PAINT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PAINTING_PRICE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_HOURLY_RATE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_WORK_FEE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_RECEIPT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_RECEIPT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_RECEIPT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_RECEIPT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_RECEIPT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_SUPPLY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_DEPRECIATION_TABLE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_VEHICLE_AGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_VEHICLE_ENERGY, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_VEHICLE_GENRE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_BODYWORK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASCERTAINMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASCERTAINMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASCERTAINMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASCERTAINMENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_ASCERTAINMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_ASSIGNMENT_MESSAGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_ASSIGNMENT_MESSAGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_MESSAGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_ASSIGNMENT_MESSAGE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_CHECK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_CHECK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_CHECK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_CHECK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_BANK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PAYMENT_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PAYMENT_METHOD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_CLIENT, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PHOTO, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PHOTO, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PHOTO, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PHOTO, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_PHOTO_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_QR_CODE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_USER_ACTION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_USER_ACTION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_USER_ACTION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_USER_ACTION, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_USER_ACTION_TYPE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_REMARK, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_GENERAL_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::CREATE_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::VIEW_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::UPDATE_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DELETE_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::ENABLE_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DISABLE_STATUS_DEADLINE, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::DASHBOARD, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::MANAGE_APP, 'guard_name' => 'sanctum'],
            ['name' => \App\Enums\PermissionEnum::COMMENT_ASSIGNMENT, 'guard_name' => 'sanctum'],
        ];

        // Upsert permissions
        Permission::upsert(
            $permissionsData,
            ['name', 'guard_name'], // clé unique
            [] // pas de champs à mettre à jour
        );

        // Récupérer les permissions pour les assigner aux rôles
        $permissionsMap = Permission::where('guard_name', 'sanctum')
            ->get()
            ->keyBy('name');

        // Créer des variables pour chaque permission
        $createUser = $permissionsMap[\App\Enums\PermissionEnum::CREATE_USER->value] ?? null;
        $viewUser = $permissionsMap[\App\Enums\PermissionEnum::VIEW_USER->value] ?? null;
        $updateUser = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_USER->value] ?? null;
        $deleteUser = $permissionsMap[\App\Enums\PermissionEnum::DELETE_USER->value] ?? null;
        $enableUser = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_USER->value] ?? null;
        $disableUser = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_USER->value] ?? null;
        $resetUser = $permissionsMap[\App\Enums\PermissionEnum::RESET_USER->value] ?? null;
        $createAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASSIGNMENT_REQUEST->value] ?? null;
        $viewAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASSIGNMENT_REQUEST->value] ?? null;
        $viewExpiredAssignment = $permissionsMap[\App\Enums\PermissionEnum::VIEW_EXPIRED_ASSIGNMENT->value] ?? null;
        $updateAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_REQUEST->value] ?? null;
        $deleteAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASSIGNMENT_REQUEST->value] ?? null;
        $acceptAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::ACCEPT_ASSIGNMENT_REQUEST->value] ?? null;
        $rejectAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::REJECT_ASSIGNMENT_REQUEST->value] ?? null;
        $cancelAssignmentRequest = $permissionsMap[\App\Enums\PermissionEnum::CANCEL_ASSIGNMENT_REQUEST->value] ?? null;
        $createAssignment = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASSIGNMENT->value] ?? null;
        $openAssignment = $permissionsMap[\App\Enums\PermissionEnum::OPEN_ASSIGNMENT->value] ?? null;
        $viewAssignment = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASSIGNMENT->value] ?? null;
        $updateAssignment = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASSIGNMENT->value] ?? null;
        $realizeAssignment = $permissionsMap[\App\Enums\PermissionEnum::REALIZE_ASSIGNMENT->value] ?? null;
        $updateRealizedAssignment = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_REALIZED_ASSIGNMENT->value] ?? null;
        $createQuoteAssignment = $permissionsMap[\App\Enums\PermissionEnum::CREATE_QUOTE_ASSIGNMENT->value] ?? null;
        $validateQuoteAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_QUOTE_ASSIGNMENT->value] ?? null;
        $unvalidateQuoteAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_QUOTE_ASSIGNMENT->value] ?? null;
        $validateQuoteWithConditionAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_QUOTE_WITH_CONDITION_ASSIGNMENT->value] ?? null;
        $validateQuoteByRepairerAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_QUOTE_BY_REPAIRER_ASSIGNMENT->value] ?? null;
        $unvalidateQuoteByRepairerAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_QUOTE_BY_REPAIRER_ASSIGNMENT->value] ?? null;
        $validateQuoteByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_QUOTE_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $validateQuoteWithConditionsByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_QUOTE_WITH_CONDITIONS_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $unvalidateQuoteByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_QUOTE_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $createWorksheetAssignment = $permissionsMap[\App\Enums\PermissionEnum::CREATE_WORKSHEET_ASSIGNMENT->value] ?? null;
        $validateWorkSheetByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_WORK_SHEET_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $unvalidateWorkSheetByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_WORK_SHEET_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $editAssignment = $permissionsMap[\App\Enums\PermissionEnum::EDIT_ASSIGNMENT->value] ?? null;
        $updateEditedAssignment = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_EDITED_ASSIGNMENT->value] ?? null;
        $validateAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_ASSIGNMENT->value] ?? null;
        $unvalidateAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_ASSIGNMENT->value] ?? null;
        $validateByRepairerAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_BY_REPAIRER_ASSIGNMENT->value] ?? null;
        $unvalidateByRepairerAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_BY_REPAIRER_ASSIGNMENT->value] ?? null;
        $validateByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $unvalidateByExpertAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_BY_EXPERT_ASSIGNMENT->value] ?? null;
        $closeAssignment = $permissionsMap[\App\Enums\PermissionEnum::CLOSE_ASSIGNMENT->value] ?? null;
        $cancelAssignment = $permissionsMap[\App\Enums\PermissionEnum::CANCEL_ASSIGNMENT->value] ?? null;
        $generateAssignment = $permissionsMap[\App\Enums\PermissionEnum::GENERATE_ASSIGNMENT->value] ?? null;
        $assignmentStatistics = $permissionsMap[\App\Enums\PermissionEnum::ASSIGNMENT_STATISTICS->value] ?? null;
        $cancelQuoteAssignment = $permissionsMap[\App\Enums\PermissionEnum::CANCEL_QUOTE_ASSIGNMENT->value] ?? null;
        $deleteAssignment = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASSIGNMENT->value] ?? null;
        $restoreDeletedAssignment = $permissionsMap[\App\Enums\PermissionEnum::RESTORE_DELETED_ASSIGNMENT->value] ?? null;
        $validateEditionAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_EDITION_ASSIGNMENT->value] ?? null;
        $unvalidateEditionAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_EDITION_ASSIGNMENT->value] ?? null;
        $validateFirstCorrectionAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_FIRST_CORRECTION_ASSIGNMENT->value] ?? null;
        $unvalidateFirstCorrectionAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_FIRST_CORRECTION_ASSIGNMENT->value] ?? null;
        $validateSecondCorrectionAssignment = $permissionsMap[\App\Enums\PermissionEnum::VALIDATE_SECOND_CORRECTION_ASSIGNMENT->value] ?? null;
        $unvalidateSecondCorrectionAssignment = $permissionsMap[\App\Enums\PermissionEnum::UNVALIDATE_SECOND_CORRECTION_ASSIGNMENT->value] ?? null;
        $markAsPaidAssignment = $permissionsMap[\App\Enums\PermissionEnum::MARK_AS_PAID_ASSIGNMENT->value] ?? null;
        $markAsUnpaidAssignment = $permissionsMap[\App\Enums\PermissionEnum::MARK_AS_UNPAID_ASSIGNMENT->value] ?? null;
        $createInvoice = $permissionsMap[\App\Enums\PermissionEnum::CREATE_INVOICE->value] ?? null;
        $viewInvoice = $permissionsMap[\App\Enums\PermissionEnum::VIEW_INVOICE->value] ?? null;
        $updateInvoice = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_INVOICE->value] ?? null;
        $deleteInvoice = $permissionsMap[\App\Enums\PermissionEnum::DELETE_INVOICE->value] ?? null;
        $cancelInvoice = $permissionsMap[\App\Enums\PermissionEnum::CANCEL_INVOICE->value] ?? null;
        $generateInvoice = $permissionsMap[\App\Enums\PermissionEnum::GENERATE_INVOICE->value] ?? null;
        $invoiceStatistics = $permissionsMap[\App\Enums\PermissionEnum::INVOICE_STATISTICS->value] ?? null;
        $createPayment = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAYMENT->value] ?? null;
        $viewPayment = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAYMENT->value] ?? null;
        $updatePayment = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAYMENT->value] ?? null;
        $deletePayment = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAYMENT->value] ?? null;
        $cancelPayment = $permissionsMap[\App\Enums\PermissionEnum::CANCEL_PAYMENT->value] ?? null;
        $paymentStatistics = $permissionsMap[\App\Enums\PermissionEnum::PAYMENT_STATISTICS->value] ?? null;
        $createShock = $permissionsMap[\App\Enums\PermissionEnum::CREATE_SHOCK->value] ?? null;
        $viewShock = $permissionsMap[\App\Enums\PermissionEnum::VIEW_SHOCK->value] ?? null;
        $updateShock = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_SHOCK->value] ?? null;
        $deleteShock = $permissionsMap[\App\Enums\PermissionEnum::DELETE_SHOCK->value] ?? null;
        $createShockWork = $permissionsMap[\App\Enums\PermissionEnum::CREATE_SHOCK_WORK->value] ?? null;
        $viewShockWork = $permissionsMap[\App\Enums\PermissionEnum::VIEW_SHOCK_WORK->value] ?? null;
        $updateShockWork = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_SHOCK_WORK->value] ?? null;
        $deleteShockWork = $permissionsMap[\App\Enums\PermissionEnum::DELETE_SHOCK_WORK->value] ?? null;
        $createShockPoint = $permissionsMap[\App\Enums\PermissionEnum::CREATE_SHOCK_POINT->value] ?? null;
        $viewShockPoint = $permissionsMap[\App\Enums\PermissionEnum::VIEW_SHOCK_POINT->value] ?? null;
        $updateShockPoint = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_SHOCK_POINT->value] ?? null;
        $deleteShockPoint = $permissionsMap[\App\Enums\PermissionEnum::DELETE_SHOCK_POINT->value] ?? null;
        $enableShockPoint = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_SHOCK_POINT->value] ?? null;
        $disableShockPoint = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_SHOCK_POINT->value] ?? null;
        $createWorkforce = $permissionsMap[\App\Enums\PermissionEnum::CREATE_WORKFORCE->value] ?? null;
        $viewWorkforce = $permissionsMap[\App\Enums\PermissionEnum::VIEW_WORKFORCE->value] ?? null;
        $updateWorkforce = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_WORKFORCE->value] ?? null;
        $deleteWorkforce = $permissionsMap[\App\Enums\PermissionEnum::DELETE_WORKFORCE->value] ?? null;
        $createWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_WORKFORCE_TYPE->value] ?? null;
        $viewWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_WORKFORCE_TYPE->value] ?? null;
        $updateWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_WORKFORCE_TYPE->value] ?? null;
        $deleteWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_WORKFORCE_TYPE->value] ?? null;
        $enableWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_WORKFORCE_TYPE->value] ?? null;
        $disableWorkforceType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_WORKFORCE_TYPE->value] ?? null;
        $createAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASSIGNMENT_TYPE->value] ?? null;
        $viewAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASSIGNMENT_TYPE->value] ?? null;
        $updateAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_TYPE->value] ?? null;
        $deleteAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASSIGNMENT_TYPE->value] ?? null;
        $enableAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_ASSIGNMENT_TYPE->value] ?? null;
        $disableAssignmentType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_ASSIGNMENT_TYPE->value] ?? null;
        $createExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_EXPERTISE_TYPE->value] ?? null;
        $viewExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_EXPERTISE_TYPE->value] ?? null;
        $updateExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_EXPERTISE_TYPE->value] ?? null;
        $deleteExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_EXPERTISE_TYPE->value] ?? null;
        $enableExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_EXPERTISE_TYPE->value] ?? null;
        $disableExpertiseType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_EXPERTISE_TYPE->value] ?? null;
        $createGeneralState = $permissionsMap[\App\Enums\PermissionEnum::CREATE_GENERAL_STATE->value] ?? null;
        $viewGeneralState = $permissionsMap[\App\Enums\PermissionEnum::VIEW_GENERAL_STATE->value] ?? null;
        $updateGeneralState = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_GENERAL_STATE->value] ?? null;
        $deleteGeneralState = $permissionsMap[\App\Enums\PermissionEnum::DELETE_GENERAL_STATE->value] ?? null;
        $enableGeneralState = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_GENERAL_STATE->value] ?? null;
        $disableGeneralState = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_GENERAL_STATE->value] ?? null;
        $createClaimNature = $permissionsMap[\App\Enums\PermissionEnum::CREATE_CLAIM_NATURE->value] ?? null;
        $viewClaimNature = $permissionsMap[\App\Enums\PermissionEnum::VIEW_CLAIM_NATURE->value] ?? null;
        $updateClaimNature = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_CLAIM_NATURE->value] ?? null;
        $deleteClaimNature = $permissionsMap[\App\Enums\PermissionEnum::DELETE_CLAIM_NATURE->value] ?? null;
        $enableClaimNature = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_CLAIM_NATURE->value] ?? null;
        $disableClaimNature = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_CLAIM_NATURE->value] ?? null;
        $createTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::CREATE_TECHNICAL_CONCLUSION->value] ?? null;
        $viewTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::VIEW_TECHNICAL_CONCLUSION->value] ?? null;
        $updateTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_TECHNICAL_CONCLUSION->value] ?? null;
        $deleteTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::DELETE_TECHNICAL_CONCLUSION->value] ?? null;
        $enableTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_TECHNICAL_CONCLUSION->value] ?? null;
        $disableTechnicalConclusion = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_TECHNICAL_CONCLUSION->value] ?? null;
        $createDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::CREATE_DOCUMENT_TRANSMITTED->value] ?? null;
        $viewDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::VIEW_DOCUMENT_TRANSMITTED->value] ?? null;
        $updateDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_DOCUMENT_TRANSMITTED->value] ?? null;
        $deleteDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::DELETE_DOCUMENT_TRANSMITTED->value] ?? null;
        $enableDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_DOCUMENT_TRANSMITTED->value] ?? null;
        $disableDocumentTransmitted = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_DOCUMENT_TRANSMITTED->value] ?? null;
        $createAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASSIGNMENT_DOCUMENT->value] ?? null;
        $viewAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASSIGNMENT_DOCUMENT->value] ?? null;
        $updateAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_DOCUMENT->value] ?? null;
        $deleteAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASSIGNMENT_DOCUMENT->value] ?? null;
        $enableAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_ASSIGNMENT_DOCUMENT->value] ?? null;
        $disableAssignmentDocument = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_ASSIGNMENT_DOCUMENT->value] ?? null;
        $createStatus = $permissionsMap[\App\Enums\PermissionEnum::CREATE_STATUS->value] ?? null;
        $viewStatus = $permissionsMap[\App\Enums\PermissionEnum::VIEW_STATUS->value] ?? null;
        $updateStatus = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_STATUS->value] ?? null;
        $deleteStatus = $permissionsMap[\App\Enums\PermissionEnum::DELETE_STATUS->value] ?? null;
        $enableStatus = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_STATUS->value] ?? null;
        $disableStatus = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_STATUS->value] ?? null;
        $createRole = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ROLE->value] ?? null;
        $viewRole = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ROLE->value] ?? null;
        $updateRole = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ROLE->value] ?? null;
        $deleteRole = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ROLE->value] ?? null;
        $createPermission = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PERMISSION->value] ?? null;
        $viewPermission = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PERMISSION->value] ?? null;
        $updatePermission = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PERMISSION->value] ?? null;
        $deletePermission = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PERMISSION->value] ?? null;
        $createEntity = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ENTITY->value] ?? null;
        $viewEntity = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ENTITY->value] ?? null;
        $updateEntity = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ENTITY->value] ?? null;
        $deleteEntity = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ENTITY->value] ?? null;
        $enableEntity = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_ENTITY->value] ?? null;
        $disableEntity = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_ENTITY->value] ?? null;
        $createEntityType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ENTITY_TYPE->value] ?? null;
        $viewEntityType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ENTITY_TYPE->value] ?? null;
        $updateEntityType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ENTITY_TYPE->value] ?? null;
        $deleteEntityType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ENTITY_TYPE->value] ?? null;
        $enableEntityType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_ENTITY_TYPE->value] ?? null;
        $disableEntityType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_ENTITY_TYPE->value] ?? null;
        $createVehicle = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE->value] ?? null;
        $viewVehicle = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE->value] ?? null;
        $updateVehicle = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE->value] ?? null;
        $deleteVehicle = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE->value] ?? null;
        $createVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE_MODEL->value] ?? null;
        $viewVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE_MODEL->value] ?? null;
        $updateVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE_MODEL->value] ?? null;
        $deleteVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE_MODEL->value] ?? null;
        $enableVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_VEHICLE_MODEL->value] ?? null;
        $disableVehicleModel = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_VEHICLE_MODEL->value] ?? null;
        $createVehicleState = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE_STATE->value] ?? null;
        $viewVehicleState = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE_STATE->value] ?? null;
        $updateVehicleState = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE_STATE->value] ?? null;
        $deleteVehicleState = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE_STATE->value] ?? null;
        $enableVehicleState = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_VEHICLE_STATE->value] ?? null;
        $disableVehicleState = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_VEHICLE_STATE->value] ?? null;
        $createBrand = $permissionsMap[\App\Enums\PermissionEnum::CREATE_BRAND->value] ?? null;
        $viewBrand = $permissionsMap[\App\Enums\PermissionEnum::VIEW_BRAND->value] ?? null;
        $updateBrand = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_BRAND->value] ?? null;
        $deleteBrand = $permissionsMap[\App\Enums\PermissionEnum::DELETE_BRAND->value] ?? null;
        $enableBrand = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_BRAND->value] ?? null;
        $disableBrand = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_BRAND->value] ?? null;
        $createColor = $permissionsMap[\App\Enums\PermissionEnum::CREATE_COLOR->value] ?? null;
        $viewColor = $permissionsMap[\App\Enums\PermissionEnum::VIEW_COLOR->value] ?? null;
        $updateColor = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_COLOR->value] ?? null;
        $deleteColor = $permissionsMap[\App\Enums\PermissionEnum::DELETE_COLOR->value] ?? null;
        $enableColor = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_COLOR->value] ?? null;
        $disableColor = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_COLOR->value] ?? null;
        $createNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::CREATE_NUMBER_PAINT_ELEMENT->value] ?? null;
        $viewNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::VIEW_NUMBER_PAINT_ELEMENT->value] ?? null;
        $updateNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_NUMBER_PAINT_ELEMENT->value] ?? null;
        $deleteNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::DELETE_NUMBER_PAINT_ELEMENT->value] ?? null;
        $enableNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_NUMBER_PAINT_ELEMENT->value] ?? null;
        $disableNumberPaintElement = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_NUMBER_PAINT_ELEMENT->value] ?? null;
        $createPaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAINT_PRODUCT_PRICE->value] ?? null;
        $viewPaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAINT_PRODUCT_PRICE->value] ?? null;
        $updatePaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAINT_PRODUCT_PRICE->value] ?? null;
        $deletePaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAINT_PRODUCT_PRICE->value] ?? null;
        $enablePaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PAINT_PRODUCT_PRICE->value] ?? null;
        $disablePaintProductPrice = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PAINT_PRODUCT_PRICE->value] ?? null;
        $createOtherCost = $permissionsMap[\App\Enums\PermissionEnum::CREATE_OTHER_COST->value] ?? null;
        $viewOtherCost = $permissionsMap[\App\Enums\PermissionEnum::VIEW_OTHER_COST->value] ?? null;
        $updateOtherCost = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_OTHER_COST->value] ?? null;
        $deleteOtherCost = $permissionsMap[\App\Enums\PermissionEnum::DELETE_OTHER_COST->value] ?? null;
        $createOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_OTHER_COST_TYPE->value] ?? null;
        $viewOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_OTHER_COST_TYPE->value] ?? null;
        $updateOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_OTHER_COST_TYPE->value] ?? null;
        $deleteOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_OTHER_COST_TYPE->value] ?? null;
        $enableOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_OTHER_COST_TYPE->value] ?? null;
        $disableOtherCostType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_OTHER_COST_TYPE->value] ?? null;
        $createPaintType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAINT_TYPE->value] ?? null;
        $viewPaintType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAINT_TYPE->value] ?? null;
        $updatePaintType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAINT_TYPE->value] ?? null;
        $deletePaintType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAINT_TYPE->value] ?? null;
        $enablePaintType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PAINT_TYPE->value] ?? null;
        $disablePaintType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PAINT_TYPE->value] ?? null;
        $createPaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAINTING_PRICE->value] ?? null;
        $viewPaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAINTING_PRICE->value] ?? null;
        $updatePaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAINTING_PRICE->value] ?? null;
        $deletePaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAINTING_PRICE->value] ?? null;
        $enablePaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PAINTING_PRICE->value] ?? null;
        $disablePaintingPrice = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PAINTING_PRICE->value] ?? null;
        $createHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::CREATE_HOURLY_RATE->value] ?? null;
        $viewHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::VIEW_HOURLY_RATE->value] ?? null;
        $updateHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_HOURLY_RATE->value] ?? null;
        $deleteHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::DELETE_HOURLY_RATE->value] ?? null;
        $enableHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_HOURLY_RATE->value] ?? null;
        $disableHourlyRate = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_HOURLY_RATE->value] ?? null;
        $createWorkFee = $permissionsMap[\App\Enums\PermissionEnum::CREATE_WORK_FEE->value] ?? null;
        $viewWorkFee = $permissionsMap[\App\Enums\PermissionEnum::VIEW_WORK_FEE->value] ?? null;
        $updateWorkFee = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_WORK_FEE->value] ?? null;
        $deleteWorkFee = $permissionsMap[\App\Enums\PermissionEnum::DELETE_WORK_FEE->value] ?? null;
        $enableWorkFee = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_WORK_FEE->value] ?? null;
        $disableWorkFee = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_WORK_FEE->value] ?? null;
        $createReceipt = $permissionsMap[\App\Enums\PermissionEnum::CREATE_RECEIPT->value] ?? null;
        $viewReceipt = $permissionsMap[\App\Enums\PermissionEnum::VIEW_RECEIPT->value] ?? null;
        $updateReceipt = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_RECEIPT->value] ?? null;
        $deleteReceipt = $permissionsMap[\App\Enums\PermissionEnum::DELETE_RECEIPT->value] ?? null;
        $createReceiptType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_RECEIPT_TYPE->value] ?? null;
        $viewReceiptType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_RECEIPT_TYPE->value] ?? null;
        $updateReceiptType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_RECEIPT_TYPE->value] ?? null;
        $deleteReceiptType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_RECEIPT_TYPE->value] ?? null;
        $enableReceiptType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_RECEIPT_TYPE->value] ?? null;
        $disableReceiptType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_RECEIPT_TYPE->value] ?? null;
        $createSupply = $permissionsMap[\App\Enums\PermissionEnum::CREATE_SUPPLY->value] ?? null;
        $viewSupply = $permissionsMap[\App\Enums\PermissionEnum::VIEW_SUPPLY->value] ?? null;
        $updateSupply = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_SUPPLY->value] ?? null;
        $deleteSupply = $permissionsMap[\App\Enums\PermissionEnum::DELETE_SUPPLY->value] ?? null;
        $enableSupply = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_SUPPLY->value] ?? null;
        $disableSupply = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_SUPPLY->value] ?? null;
        $costSupply = $permissionsMap[\App\Enums\PermissionEnum::COST_SUPPLY->value] ?? null;
        $createDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::CREATE_DEPRECIATION_TABLE->value] ?? null;
        $viewDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::VIEW_DEPRECIATION_TABLE->value] ?? null;
        $updateDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_DEPRECIATION_TABLE->value] ?? null;
        $deleteDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::DELETE_DEPRECIATION_TABLE->value] ?? null;
        $enableDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_DEPRECIATION_TABLE->value] ?? null;
        $disableDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_DEPRECIATION_TABLE->value] ?? null;
        $calculateDepreciationTable = $permissionsMap[\App\Enums\PermissionEnum::CALCULATE_DEPRECIATION_TABLE->value] ?? null;
        $createVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE_AGE->value] ?? null;
        $viewVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE_AGE->value] ?? null;
        $updateVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE_AGE->value] ?? null;
        $deleteVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE_AGE->value] ?? null;
        $enableVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_VEHICLE_AGE->value] ?? null;
        $disableVehicleAge = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_VEHICLE_AGE->value] ?? null;
        $createVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE_ENERGY->value] ?? null;
        $viewVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE_ENERGY->value] ?? null;
        $updateVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE_ENERGY->value] ?? null;
        $deleteVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE_ENERGY->value] ?? null;
        $enableVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_VEHICLE_ENERGY->value] ?? null;
        $disableVehicleEnergy = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_VEHICLE_ENERGY->value] ?? null;
        $createVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::CREATE_VEHICLE_GENRE->value] ?? null;
        $viewVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::VIEW_VEHICLE_GENRE->value] ?? null;
        $updateVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_VEHICLE_GENRE->value] ?? null;
        $deleteVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::DELETE_VEHICLE_GENRE->value] ?? null;
        $enableVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_VEHICLE_GENRE->value] ?? null;
        $disableVehicleGenre = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_VEHICLE_GENRE->value] ?? null;
        $createBodywork = $permissionsMap[\App\Enums\PermissionEnum::CREATE_BODYWORK->value] ?? null;
        $viewBodywork = $permissionsMap[\App\Enums\PermissionEnum::VIEW_BODYWORK->value] ?? null;
        $updateBodywork = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_BODYWORK->value] ?? null;
        $deleteBodywork = $permissionsMap[\App\Enums\PermissionEnum::DELETE_BODYWORK->value] ?? null;
        $enableBodywork = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_BODYWORK->value] ?? null;
        $disableBodywork = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_BODYWORK->value] ?? null;
        $createAscertainment = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASCERTAINMENT->value] ?? null;
        $viewAscertainment = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASCERTAINMENT->value] ?? null;
        $updateAscertainment = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASCERTAINMENT->value] ?? null;
        $deleteAscertainment = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASCERTAINMENT->value] ?? null;
        $createAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASCERTAINMENT_TYPE->value] ?? null;
        $viewAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASCERTAINMENT_TYPE->value] ?? null;
        $updateAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASCERTAINMENT_TYPE->value] ?? null;
        $deleteAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASCERTAINMENT_TYPE->value] ?? null;
        $enableAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_ASCERTAINMENT_TYPE->value] ?? null;
        $disableAscertainmentType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_ASCERTAINMENT_TYPE->value] ?? null;
        $createAssignmentMessage = $permissionsMap[\App\Enums\PermissionEnum::CREATE_ASSIGNMENT_MESSAGE->value] ?? null;
        $viewAssignmentMessage = $permissionsMap[\App\Enums\PermissionEnum::VIEW_ASSIGNMENT_MESSAGE->value] ?? null;
        $updateAssignmentMessage = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_ASSIGNMENT_MESSAGE->value] ?? null;
        $deleteAssignmentMessage = $permissionsMap[\App\Enums\PermissionEnum::DELETE_ASSIGNMENT_MESSAGE->value] ?? null;
        $createCheck = $permissionsMap[\App\Enums\PermissionEnum::CREATE_CHECK->value] ?? null;
        $viewCheck = $permissionsMap[\App\Enums\PermissionEnum::VIEW_CHECK->value] ?? null;
        $updateCheck = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_CHECK->value] ?? null;
        $deleteCheck = $permissionsMap[\App\Enums\PermissionEnum::DELETE_CHECK->value] ?? null;
        $createBank = $permissionsMap[\App\Enums\PermissionEnum::CREATE_BANK->value] ?? null;
        $viewBank = $permissionsMap[\App\Enums\PermissionEnum::VIEW_BANK->value] ?? null;
        $updateBank = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_BANK->value] ?? null;
        $deleteBank = $permissionsMap[\App\Enums\PermissionEnum::DELETE_BANK->value] ?? null;
        $enableBank = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_BANK->value] ?? null;
        $disableBank = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_BANK->value] ?? null;
        $createPaymentType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAYMENT_TYPE->value] ?? null;
        $viewPaymentType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAYMENT_TYPE->value] ?? null;
        $updatePaymentType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAYMENT_TYPE->value] ?? null;
        $deletePaymentType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAYMENT_TYPE->value] ?? null;
        $enablePaymentType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PAYMENT_TYPE->value] ?? null;
        $disablePaymentType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PAYMENT_TYPE->value] ?? null;
        $createPaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PAYMENT_METHOD->value] ?? null;
        $viewPaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PAYMENT_METHOD->value] ?? null;
        $updatePaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PAYMENT_METHOD->value] ?? null;
        $deletePaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PAYMENT_METHOD->value] ?? null;
        $enablePaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PAYMENT_METHOD->value] ?? null;
        $disablePaymentMethod = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PAYMENT_METHOD->value] ?? null;
        $createClient = $permissionsMap[\App\Enums\PermissionEnum::CREATE_CLIENT->value] ?? null;
        $viewClient = $permissionsMap[\App\Enums\PermissionEnum::VIEW_CLIENT->value] ?? null;
        $updateClient = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_CLIENT->value] ?? null;
        $deleteClient = $permissionsMap[\App\Enums\PermissionEnum::DELETE_CLIENT->value] ?? null;
        $enableClient = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_CLIENT->value] ?? null;
        $disableClient = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_CLIENT->value] ?? null;
        $createPhoto = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PHOTO->value] ?? null;
        $viewPhoto = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PHOTO->value] ?? null;
        $updatePhoto = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PHOTO->value] ?? null;
        $deletePhoto = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PHOTO->value] ?? null;
        $createPhotoType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_PHOTO_TYPE->value] ?? null;
        $viewPhotoType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_PHOTO_TYPE->value] ?? null;
        $updatePhotoType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_PHOTO_TYPE->value] ?? null;
        $deletePhotoType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_PHOTO_TYPE->value] ?? null;
        $enablePhotoType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_PHOTO_TYPE->value] ?? null;
        $disablePhotoType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_PHOTO_TYPE->value] ?? null;
        $createQrCode = $permissionsMap[\App\Enums\PermissionEnum::CREATE_QR_CODE->value] ?? null;
        $viewQrCode = $permissionsMap[\App\Enums\PermissionEnum::VIEW_QR_CODE->value] ?? null;
        $updateQrCode = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_QR_CODE->value] ?? null;
        $deleteQrCode = $permissionsMap[\App\Enums\PermissionEnum::DELETE_QR_CODE->value] ?? null;
        $enableQrCode = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_QR_CODE->value] ?? null;
        $disableQrCode = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_QR_CODE->value] ?? null;
        $createUserAction = $permissionsMap[\App\Enums\PermissionEnum::CREATE_USER_ACTION->value] ?? null;
        $viewUserAction = $permissionsMap[\App\Enums\PermissionEnum::VIEW_USER_ACTION->value] ?? null;
        $updateUserAction = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_USER_ACTION->value] ?? null;
        $deleteUserAction = $permissionsMap[\App\Enums\PermissionEnum::DELETE_USER_ACTION->value] ?? null;
        $createUserActionType = $permissionsMap[\App\Enums\PermissionEnum::CREATE_USER_ACTION_TYPE->value] ?? null;
        $viewUserActionType = $permissionsMap[\App\Enums\PermissionEnum::VIEW_USER_ACTION_TYPE->value] ?? null;
        $updateUserActionType = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_USER_ACTION_TYPE->value] ?? null;
        $deleteUserActionType = $permissionsMap[\App\Enums\PermissionEnum::DELETE_USER_ACTION_TYPE->value] ?? null;
        $enableUserActionType = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_USER_ACTION_TYPE->value] ?? null;
        $disableUserActionType = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_USER_ACTION_TYPE->value] ?? null;
        $createRemark = $permissionsMap[\App\Enums\PermissionEnum::CREATE_REMARK->value] ?? null;
        $viewRemark = $permissionsMap[\App\Enums\PermissionEnum::VIEW_REMARK->value] ?? null;
        $updateRemark = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_REMARK->value] ?? null;
        $deleteRemark = $permissionsMap[\App\Enums\PermissionEnum::DELETE_REMARK->value] ?? null;
        $enableRemark = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_REMARK->value] ?? null;
        $disableRemark = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_REMARK->value] ?? null;
        $createGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::CREATE_GENERAL_STATUS_DEADLINE->value] ?? null;
        $viewGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::VIEW_GENERAL_STATUS_DEADLINE->value] ?? null;
        $updateGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_GENERAL_STATUS_DEADLINE->value] ?? null;
        $deleteGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::DELETE_GENERAL_STATUS_DEADLINE->value] ?? null;
        $enableGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_GENERAL_STATUS_DEADLINE->value] ?? null;
        $disableGeneralStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_GENERAL_STATUS_DEADLINE->value] ?? null;
        $createStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::CREATE_STATUS_DEADLINE->value] ?? null;
        $viewStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::VIEW_STATUS_DEADLINE->value] ?? null;
        $updateStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::UPDATE_STATUS_DEADLINE->value] ?? null;
        $deleteStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::DELETE_STATUS_DEADLINE->value] ?? null;
        $enableStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::ENABLE_STATUS_DEADLINE->value] ?? null;
        $disableStatusDeadline = $permissionsMap[\App\Enums\PermissionEnum::DISABLE_STATUS_DEADLINE->value] ?? null;
        $dashboard = $permissionsMap[\App\Enums\PermissionEnum::DASHBOARD->value] ?? null;
        $manageApp = $permissionsMap[\App\Enums\PermissionEnum::MANAGE_APP->value] ?? null;
        $commentAssignment = $permissionsMap[\App\Enums\PermissionEnum::COMMENT_ASSIGNMENT->value] ?? null;

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::SYSTEM_ADMIN->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Administrateur système',
                'description' => "Chargé de l'administration et de la configuration de la plateforme.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,
            $generateAssignment,
            $assignmentStatistics,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $viewExpiredAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,
            $markAsPaidAssignment,
            $markAsUnpaidAssignment,
            $deleteAssignment,
            $restoreDeletedAssignment,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $viewShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $viewWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $viewReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $viewOtherCost,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $createGeneralStatusDeadline,
            $viewGeneralStatusDeadline,
            $updateGeneralStatusDeadline,
            $deleteGeneralStatusDeadline,
            $enableGeneralStatusDeadline,
            $disableGeneralStatusDeadline,

            $createStatusDeadline,
            $viewStatusDeadline,
            $updateStatusDeadline,
            $deleteStatusDeadline,
            $enableStatusDeadline,
            $disableStatusDeadline,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::ADMIN->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Administrateur',
                'description' => "Chargé de la gestion de la plateforme.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $validateAssignment,
            $unvalidateAssignment,
            $validateByRepairerAssignment,
            $unvalidateByRepairerAssignment,
            $validateByExpertAssignment,
            $unvalidateByExpertAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,

            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $viewOtherCostType,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createStatusDeadline,
            $viewStatusDeadline,
            $updateStatusDeadline,
            $deleteStatusDeadline,
            $enableStatusDeadline,
            $disableStatusDeadline,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::CEO->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Directeur général',
                'description' => "Chargé de la direction générale de l'entreprise.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $validateAssignment,
            $unvalidateAssignment,
            $validateByRepairerAssignment,
            $unvalidateByRepairerAssignment,
            $validateByExpertAssignment,
            $unvalidateByExpertAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,
            $markAsPaidAssignment,

            $createInvoice,
            $viewInvoice,
            $updateInvoice,
            $deleteInvoice,
            $cancelInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,
            $cancelPayment,
            $paymentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $viewBank,
            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $viewPaymentType,

            $viewPaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $viewPhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::EXPERT_MANAGER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Responsable expert',
                'description' => "Responsable expert.",
            ]
        );
        $role->givePermissionTo([
            $viewUser,

            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $validateAssignment,
            $unvalidateAssignment,
            $validateByRepairerAssignment,
            $unvalidateByRepairerAssignment,
            $validateByExpertAssignment,
            $unvalidateByExpertAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,

            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $viewBank,
            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $viewPaymentType,

            $viewPaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $viewPhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::EXPERT->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Expert',
                'description' => "Expert.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::OPENER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Receptionniste',
                'description' => "Receptionniste.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::BUSINESS_DEVELOPER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Commercial',
                'description' => "Commercial",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::EDITOR_MANAGER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Responsable rédaction',
                'description' => "Responsable rédaction.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::EDITOR->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Responsable rédaction',
                'description' => "Responsable rédaction.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::VALIDATOR->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Validateur',
                'description' => "Validateur.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $viewPayment,
            $paymentStatistics,

            $viewShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $createWorkforceType,
            $viewWorkforceType,
            $updateWorkforceType,
            $deleteWorkforceType,
            $enableWorkforceType,
            $disableWorkforceType,

            $createAssignmentType,
            $openAssignment,
            $viewAssignmentType,
            $updateAssignmentType,
            $deleteAssignmentType,
            $enableAssignmentType,
            $disableAssignmentType,

            $createExpertiseType,
            $viewExpertiseType,
            $updateExpertiseType,
            $deleteExpertiseType,
            $enableExpertiseType,
            $disableExpertiseType,

            $createGeneralState,
            $viewGeneralState,
            $updateGeneralState,
            $deleteGeneralState,
            $enableGeneralState,
            $disableGeneralState,

            $createClaimNature,
            $viewClaimNature,
            $updateClaimNature,
            $deleteClaimNature,
            $enableClaimNature,
            $disableClaimNature,

            $createTechnicalConclusion,
            $viewTechnicalConclusion,
            $updateTechnicalConclusion,
            $deleteTechnicalConclusion,
            $enableTechnicalConclusion,
            $disableTechnicalConclusion,

            $createDocumentTransmitted,
            $viewDocumentTransmitted,
            $updateDocumentTransmitted,
            $deleteDocumentTransmitted,
            $enableDocumentTransmitted,
            $disableDocumentTransmitted,

            $createAssignmentDocument,
            $viewAssignmentDocument,
            $updateAssignmentDocument,
            $deleteAssignmentDocument,
            $enableAssignmentDocument,
            $disableAssignmentDocument,

            $createStatus,
            $viewStatus,
            $updateStatus,
            $deleteStatus,
            $enableStatus,
            $disableStatus,

            $createRole,
            $viewRole,
            $updateRole,
            $deleteRole,

            $createPermission,
            $viewPermission,
            $updatePermission,
            $deletePermission,

            $createEntity,
            $viewEntity,
            $updateEntity,
            $deleteEntity,
            $enableEntity,
            $disableEntity,

            $createEntityType,
            $viewEntityType,
            $updateEntityType,
            $deleteEntityType,
            $enableEntityType,
            $disableEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $createBank,
            $viewBank,
            $updateBank,
            $deleteBank,
            $enableBank,
            $disableBank,
            
            $createReceiptType,
            $viewReceiptType,
            $updateReceiptType,
            $deleteReceiptType,
            $enableReceiptType,
            $disableReceiptType,

            $createSupply,
            $viewSupply,
            $updateSupply,
            $deleteSupply,
            $enableSupply,
            $disableSupply,

            $createPaintType,
            $viewPaintType,
            $updatePaintType,
            $deletePaintType,
            $enablePaintType,
            $disablePaintType,

            $createPaintProductPrice,
            $viewPaintProductPrice,
            $updatePaintProductPrice,
            $deletePaintProductPrice,
            $enablePaintProductPrice,
            $disablePaintProductPrice,

            $createPaintingPrice,
            $viewPaintingPrice,
            $updatePaintingPrice,
            $deletePaintingPrice,
            $enablePaintingPrice,
            $disablePaintingPrice,

            $createHourlyRate,
            $viewHourlyRate,
            $updateHourlyRate,
            $deleteHourlyRate,
            $enableHourlyRate,
            $disableHourlyRate,

            $createWorkFee,
            $viewWorkFee,
            $updateWorkFee,
            $deleteWorkFee,
            $enableWorkFee,
            $disableWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $createDepreciationTable,
            $viewDepreciationTable,
            $updateDepreciationTable,
            $deleteDepreciationTable,
            $enableDepreciationTable,
            $disableDepreciationTable,

            $createAscertainmentType,
            $viewAscertainmentType,
            $updateAscertainmentType,
            $deleteAscertainmentType,
            $enableAscertainmentType,
            $disableAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $createNumberPaintElement,
            $viewNumberPaintElement,
            $updateNumberPaintElement,
            $deleteNumberPaintElement,
            $enableNumberPaintElement,
            $disableNumberPaintElement,

            $createOtherCostType,
            $viewOtherCostType,
            $updateOtherCostType,
            $deleteOtherCostType,
            $enableOtherCostType,
            $disableOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPaymentMethod,
            $viewPaymentMethod,
            $updatePaymentMethod,
            $deletePaymentMethod,
            $enablePaymentMethod,
            $disablePaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $createPhotoType,
            $viewPhotoType,
            $updatePhotoType,
            $deletePhotoType,
            $enablePhotoType,
            $disablePhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $createUserActionType,
            $viewUserActionType,
            $updateUserActionType,
            $deleteUserActionType,
            $enableUserActionType,
            $disableUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::ACCOUNTANT_MANAGER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Chef Comptable',
                'description' => "Chef Comptable.",
            ]
        );
        $role->givePermissionTo([
            $viewUser,
            
            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $createInvoice,
            $viewInvoice,
            $updateInvoice,
            $deleteInvoice,
            $cancelInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,
            $cancelPayment,
            $paymentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $viewBank,
            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $viewPaymentType,

            $viewPaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $viewPhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::ACCOUNTANT->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Comptable',
                'description' => "Comptable.",
            ]
        );
        $role->givePermissionTo([
            $viewUser,
            
            $viewAssignmentRequest,
            $acceptAssignmentRequest,
            $rejectAssignmentRequest,

            $createAssignment,
            $openAssignment,
            $viewAssignment,
            $updateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $unvalidateQuoteAssignment,
            $validateQuoteWithConditionAssignment,
            $createWorksheetAssignment,
            $validateWorkSheetByExpertAssignment,
            $unvalidateWorkSheetByExpertAssignment,
            $validateEditionAssignment,
            $unvalidateEditionAssignment,
            $validateFirstCorrectionAssignment,
            $unvalidateFirstCorrectionAssignment,
            $validateSecondCorrectionAssignment,
            $unvalidateSecondCorrectionAssignment,
            $realizeAssignment,
            $updateRealizedAssignment,
            $editAssignment,
            $updateEditedAssignment,
            $cancelAssignment,
            $generateAssignment,
            $assignmentStatistics,
            $commentAssignment,
            
            $createInvoice,
            $viewInvoice,
            $updateInvoice,
            $deleteInvoice,
            $cancelInvoice,
            $generateInvoice,
            $invoiceStatistics,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,
            $cancelPayment,
            $paymentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createVehicleGenre,
            $viewVehicleGenre,
            $updateVehicleGenre,
            $deleteVehicleGenre,
            $enableVehicleGenre,
            $disableVehicleGenre,

            $createVehicleEnergy,
            $viewVehicleEnergy,
            $updateVehicleEnergy,
            $deleteVehicleEnergy,
            $enableVehicleEnergy,
            $disableVehicleEnergy,

            $createVehicleAge,
            $viewVehicleAge,
            $updateVehicleAge,
            $deleteVehicleAge,
            $enableVehicleAge,
            $disableVehicleAge,

            $createVehicleModel,
            $viewVehicleModel,
            $updateVehicleModel,
            $deleteVehicleModel,
            $enableVehicleModel,
            $disableVehicleModel,

            $createVehicleState,
            $viewVehicleState,
            $updateVehicleState,
            $deleteVehicleState,
            $enableVehicleState,
            $disableVehicleState,

            $createBrand,
            $viewBrand,
            $updateBrand,
            $deleteBrand,
            $enableBrand,
            $disableBrand,

            $createColor,
            $viewColor,
            $updateColor,
            $deleteColor,
            $enableColor,
            $disableColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,
            $deleteBodywork,
            $enableBodywork,
            $disableBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createCheck,
            $viewCheck,
            $updateCheck,
            $deleteCheck,

            $viewBank,
            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $createReceipt,
            $viewReceipt,
            $updateReceipt,
            $deleteReceipt,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createOtherCost,
            $viewOtherCost,
            $updateOtherCost,
            $deleteOtherCost,

            $viewPaymentType,

            $viewPaymentMethod,

            $createClient,
            $viewClient,
            $updateClient,
            $deleteClient,
            $enableClient,
            $disableClient,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $viewPhotoType,

            $createQrCode,
            $viewQrCode,
            $updateQrCode,
            $deleteQrCode,
            $enableQrCode,
            $disableQrCode,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::INSURER_ADMIN->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Administrateur assureur',
                'description' => "Chargé de l'administration d'un assureur.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,
            
            $createAssignment,
            $viewAssignment,
            $updateAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            
            $viewVehicleGenre,

            $viewVehicleEnergy,

            $viewVehicleAge,

            $viewVehicleModel,

            $viewVehicleState,

            $createColor,
            $viewColor,
            $updateColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createClient,
            $viewClient,
            $updateClient,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::INSURER_STANDARD_USER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Utilisateur assureur',
                'description' => "Utilisateur assureur.",
            ]
        );
        $role->givePermissionTo([
            $viewUser,
            
            $createAssignment,
            $viewAssignment,
            $updateAssignment,
            $generateAssignment,
            $assignmentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            
            $viewVehicleGenre,

            $viewVehicleEnergy,

            $viewVehicleAge,

            $viewVehicleModel,

            $viewVehicleState,

            $createColor,
            $viewColor,
            $updateColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createClient,
            $viewClient,
            $updateClient,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::REPAIRER_ADMIN->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Administrateur réparateur',
                'description' => "Chargé de l'administration d'un réparateur.",
            ]
        );
        $role->givePermissionTo([
            $createUser,
            $viewUser,
            $updateUser,
            $deleteUser,
            $enableUser,
            $disableUser,
            $resetUser,

            $viewUser,
            
            $viewAssignment,
            $updateAssignment,
            $generateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $cancelQuoteAssignment,
            $assignmentStatistics,

            $viewShock,
            $createShock,
            $updateShock,
            $deleteShock,

            $createShockWork,
            $viewShockWork,
            $updateShockWork,
            $deleteShockWork,

            $createShockPoint,
            $viewShockPoint,
            $updateShockPoint,
            $deleteShockPoint,
            $enableShockPoint,
            $disableShockPoint,

            $createWorkforce,
            $viewWorkforce,
            $updateWorkforce,
            $deleteWorkforce,

            $viewGeneralState,

            $viewClaimNature,

            $viewTechnicalConclusion,

            $viewDocumentTransmitted,

            $viewAssignmentDocument,

            $viewStatus,

            $viewRole,

            $viewPermission,


            $viewEntity,

            $viewEntityType,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            
            $viewVehicleGenre,

            $viewVehicleEnergy,

            $viewVehicleAge,

            $viewVehicleModel,

            $viewVehicleState,

            $createColor,
            $viewColor,
            $updateColor,

            $createBodywork,
            $viewBodywork,
            $updateBodywork,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            
            $viewReceiptType,

            $viewSupply,

            $viewPaintType,

            $viewPaintProductPrice,

            $viewPaintingPrice,

            $viewHourlyRate,

            $viewWorkFee,

            $viewDepreciationTable,

            $viewAscertainmentType,

            $createRemark,
            $viewRemark,
            $updateRemark,
            $deleteRemark,
            $enableRemark,
            $disableRemark,

            $viewNumberPaintElement,

            $viewOtherCostType,

            $createClient,
            $viewClient,
            $updateClient,

            $createUserAction,
            $viewUserAction,
            $updateUserAction,
            $deleteUserAction,

            $viewUserActionType,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::REPAIRER_STANDARD_USER->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Utilisateur standard réparateur',
                'description' => "Utilisateur standard réparateur.",
            ]
        );
        $role->givePermissionTo([
            $viewAssignment,
            $updateAssignment,
            $generateAssignment,
            $createQuoteAssignment,
            $validateQuoteAssignment,
            $cancelQuoteAssignment,
            $assignmentStatistics,

            $dashboard,
        ]);

        $role = Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::CLIENT->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Client',
                'description' => "Client.",
            ]
        );
        $role->givePermissionTo([
            $viewUser,

            $viewAssignmentRequest,
            $cancelAssignmentRequest,

            $viewAssignment,

            $viewShock,

            $createVehicle,
            $viewVehicle,
            $updateVehicle,
            $deleteVehicle,

            $createAssignmentMessage,
            $viewAssignmentMessage,
            $updateAssignmentMessage,
            $deleteAssignmentMessage,

            $createPayment,
            $viewPayment,
            $updatePayment,
            $deletePayment,

            $createPaymentType,
            $viewPaymentType,
            $updatePaymentType,
            $deletePaymentType,
            $enablePaymentType,
            $disablePaymentType,

            $createPhoto,
            $viewPhoto,
            $updatePhoto,
            $deletePhoto,

            $dashboard,
        ]);

        Role::updateOrCreate(
            ['name' => \App\Enums\RoleEnum::UNASSIGNED->value, 'guard_name' => 'sanctum'],
            [
                'label' => 'Non assigné',
                'description' => 'Aucune habilitation',
            ]
        );
    }
}
