# ArkaLedger Implementation Status

## Completed Phases

### Phase 0: Foundation & Scaffold
- Laravel 13 + Breeze React + Ant Design Pro (dark mode default)
- Spatie roles/permissions with 4 roles + seed users
- Core schema: project_sites, accounts, pnl_lines, coa_mappings, report_periods
- Admin CRUD for sites, accounts, CoA mappings, users, roles

### Phase 1: Data Ingestion & P&L Dashboard MVP
- Import pipeline (SapExcelParserService, staging, jobs)
- account_balances + pnl_snapshots aggregation
- Import Center, Site P&L, Consolidated P&L, Dashboard
- Journal, Petty Cash, Tax modules (schema + CRUD)

### Phase 2: Reports & Delivery
- report_packages, report_artifacts, approval_steps, delivery_logs
- Excel/PDF renderers, WorkbookGeneratorService (21-sheet map)
- Report Studio UI, approval workflow, period state machine

### Phase 3: Intelligence & Analytics
- variance_flags, reconciliation_checks, ratio_snapshots, anomaly_alerts
- Intelligence services + Insights feed

### Phase 4: Deep Integration & Automation
- sap_sync_runs, email_templates, sister-app repositories
- Scheduled SAP pull, Hermes inbound, n8n workflow JSONs
- Backfill commands scaffold

## Login
- admin@arkaledger.test / password (Super Admin)
- finance@arkaledger.test / password (Finance Manager)

## Gotchas
- npm requires `--legacy-peer-deps` always
- rc-field-form peer dep needed for Ant Design Pro
- MySQL index names must be shortened for long composite indexes
- SESSION_DRIVER=database (sessions table in users migration)
