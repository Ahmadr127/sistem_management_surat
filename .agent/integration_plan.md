# Rencana Integrasi Permission & Organization System

## Overview
Mengintegrasikan sistem Permission, Organization Types, dan Organization Units dari project lain ke dalam sistem management surat yang sudah ada.

## Current State Analysis

### Sistem Yang Ada:
1. **User Management**: Menggunakan role berbasis integer (0-8)
   - 0: Staff/Unit
   - 1: Sekretaris
   - 2: Direktur
   - 3: Super Admin
   - 4: Manager
   - 5: Sekretaris ASP
   - 6: Admin
   - 7: Manager Keuangan
   - 8: General Manager

2. **Database Structure**:
   - `users` table dengan kolom `role` (integer)
   - `tbl_jabatan` untuk jabatan
   - `perusahaans` untuk perusahaan
   - Tidak ada tabel permissions, roles, organization_units, organization_types

3. **Authorization**: Menggunakan middleware `checkRole` dengan integer

### Sistem Yang Akan Diintegrasikan:
1. **Permission System**: Dynamic permission-based authorization
2. **Organization Types**: Hierarki tipe organisasi (Level 1-5)
3. **Organization Units**: Unit organisasi dengan parent-child relationship

## Integration Steps

### Phase 1: Database Migration
1. **Create Migrations**:
   - `create_permissions_table`
   - `create_roles_table`
   - `create_role_permission_table`
   - `create_organization_types_table`
   - `create_organization_units_table`
   - `add_role_id_to_users_table`
   - `add_organization_unit_id_to_users_table`

2. **Data Migration Strategy**:
   - Buat seeder untuk mengkonversi role integer ke role table
   - Mapping role lama ke permission baru
   - Preserve existing user data

### Phase 2: Models
1. **Create New Models**:
   - `Permission.php` ✓ (sudah ada)
   - `Role.php` (perlu dibuat)
   - `OrganizationType.php` ✓ (sudah ada)
   - `OrganizationUnit.php` ✓ (sudah ada)

2. **Update User Model**:
   - Add relationships: `role()`, `permissions()`, `organizationUnit()`
   - Add method: `hasPermission($permission)`
   - Maintain backward compatibility dengan role integer

### Phase 3: Controllers
1. **Create Controllers**:
   - `PermissionController.php`
   - `RoleController.php`
   - `OrganizationTypeController.php`
   - `OrganizationUnitController.php`

2. **Update Existing Controllers**:
   - Replace `checkRole` middleware dengan `can` middleware
   - Update authorization logic

### Phase 4: Views
1. **Copy Views**:
   - `resources/views/permissions/*` ✓ (sudah ada)
   - `resources/views/organization-types/*` ✓ (sudah ada)
   - `resources/views/organization-units/*` ✓ (sudah ada)
   - `resources/views/roles/*` (perlu dibuat)

2. **Update Layout**:
   - Merge sidebar dari `layouts copy/app.blade.php` ke `layouts/sidebar.blade.php`
   - Add menu items untuk Permissions, Roles, Organization management

### Phase 5: Routes
1. **Add Resource Routes**:
   - `permissions` resource
   - `roles` resource
   - `organization-types` resource
   - `organization-units` resource
   - Custom routes untuk member management

### Phase 6: Seeders
1. **Create Seeders**:
   - `RoleSeeder`: Convert existing integer roles
   - `PermissionSeeder`: Define all permissions
   - `RolePermissionSeeder`: Assign permissions to roles
   - `OrganizationTypeSeeder`: Basic organization types
   - `OrganizationUnitSeeder`: Sample organization units
   - `UserRoleUpdateSeeder`: Update existing users

### Phase 7: Middleware & Authorization
1. **Update Middleware**:
   - Keep `checkRole` for backward compatibility
   - Add `CheckPermission` middleware
   - Update `AuthServiceProvider` untuk gates

2. **Authorization Policies**:
   - Create policies untuk Permission, Role, OrganizationUnit

### Phase 8: Components
1. **Blade Components**:
   - `searchable-dropdown.blade.php` (sudah digunakan di views)
   - `table-filter.blade.php` (sudah digunakan di views)

### Phase 9: Testing & Validation
1. **Test Cases**:
   - Permission assignment
   - Role-based access
   - Organization hierarchy
   - User management dengan new system

### Phase 10: Documentation
1. **Update Documentation**:
   - Permission list
   - Role definitions
   - Organization structure
   - Migration guide untuk existing users

## Backward Compatibility Strategy

1. **Dual System Support**:
   - Keep integer `role` column untuk backward compatibility
   - Add `role_id` foreign key untuk new system
   - User model support both methods

2. **Gradual Migration**:
   - Phase 1: Add new tables, keep old system working
   - Phase 2: Populate new tables from old data
   - Phase 3: Update views to use new system
   - Phase 4: Update controllers gradually
   - Phase 5: Deprecate old system (optional)

## Permission Mapping

### Existing Roles → New Permissions

**Super Admin (3)**:
- manage_users
- manage_roles
- manage_permissions
- manage_organization_types
- manage_organization_units
- manage_perusahaan
- manage_jabatan
- view_dashboard
- manage_surat
- approve_surat

**Sekretaris (1)**:
- view_dashboard
- manage_surat_keluar
- manage_surat_masuk
- generate_nomor_surat
- manage_disposisi
- view_laporan

**Direktur (2)**:
- view_dashboard
- view_surat
- approve_disposisi
- view_laporan

**Manager (4)**:
- view_dashboard
- approve_surat_unit
- view_surat
- manage_unit_surat

**Staff/Unit (0)**:
- view_dashboard
- create_surat_unit
- view_own_surat

**Admin (6)**:
- view_dashboard
- manage_surat
- view_laporan

**Manager Keuangan (7)**:
- view_dashboard
- approve_surat_unit
- view_surat_keuangan

**General Manager (8)**:
- view_dashboard
- approve_surat_unit
- manage_team

**Sekretaris ASP (5)**:
- view_surat_masuk
- manage_surat_keluar
- view_arsip
- view_laporan

## Implementation Priority

1. **High Priority** (Core functionality):
   - Database migrations
   - Models
   - Seeders
   - User model update

2. **Medium Priority** (Management features):
   - Controllers
   - Routes
   - Views integration
   - Sidebar update

3. **Low Priority** (Enhancement):
   - Advanced features
   - Optimization
   - Complete migration from old system

## Risk Mitigation

1. **Data Loss Prevention**:
   - Backup database before migration
   - Test seeders on development first
   - Keep old columns until fully migrated

2. **Access Control Issues**:
   - Test all permission combinations
   - Ensure no user loses access
   - Provide fallback to old system if needed

3. **Performance**:
   - Index foreign keys
   - Optimize permission checks
   - Cache permission queries

## Success Criteria

1. ✓ All existing users can login
2. ✓ All existing features still work
3. ✓ New permission system functional
4. ✓ Organization hierarchy working
5. ✓ Sidebar shows correct menus based on permissions
6. ✓ No data loss
7. ✓ Performance acceptable
