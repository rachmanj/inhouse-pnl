**Purpose**: Record technical decisions and rationale for future reference
**Last Updated**: [Auto-updated by AI]

# Technical Decision Records

## Decision Template

Decision: [Title] - [YYYY-MM-DD]

**Context**: [What situation led to this decision?]

**Options Considered**:

1. **Option A**: [Description]
   - ✅ Pros: [Benefits]
   - ❌ Cons: [Drawbacks]
2. **Option B**: [Description]
   - ✅ Pros: [Benefits]
   - ❌ Cons: [Drawbacks]

**Decision**: [What we chose]

**Rationale**: [Why we chose this option]

**Implementation**: [How this affects the codebase]

**Review Date**: [When to revisit this decision]

---

## Recent Decisions

Decision: No sarang-erp integration — ArkaLedger is standalone - 2026-10-09

**Context**: The original concept assumed `sarang-erp-laravel` runs on the same VPS and shared MySQL server as ArkaLedger, enabling read-only consumption of `TaxReport` / `TaxTransaction` data. In production, sarang-erp runs in a separate Docker/MySQL environment on another host, not on the shared MySQL server the cross-DB design targets.

**Options Considered**:

1. **Read-only MySQL to sarang_erp on the shared server**
   - ✅ Pros: Matches the original §12 integration sketch; no duplicate tax schema work.
   - ❌ Cons: Database is not co-located; would require network replication or a stale copy — out of scope and operationally fragile.

2. **REST/API consumption from remote sarang-erp**
   - ✅ Pros: Works across hosts.
   - ❌ Cons: New integration surface, auth, and coupling to another product's API; ArkaLedger tax module already has native tables and SAP as financial source of truth.

3. **No sarang-erp integration — native tax in ArkaLedger**
   - ✅ Pros: Standalone deploy; tax data from manual entry, historical import, and SAP; keeps only proven co-located cross-DB reads (`arkfleet`, `daily_production`).
   - ❌ Cons: Tax features must be fully owned in ArkaLedger (already planned for 21-sheet deliverable).

**Decision**: Option 3 — no `sarang_erp` database connection, no read-only view, and no REST consumption from sarang-erp. Tax filings and payments live in `inhouse_pnl`; sources are `manual` and `sap`. Cross-DB read-only access to `arkfleet` and `daily_production` is unchanged.

**Rationale**: ArkaLedger must run as a standalone app on the shared MySQL server without depending on a sister system that is not co-located. Financial and tax reconciliation should anchor on SAP plus ArkaLedger's own tax records, not a third application's database.

**Implementation**: Removed `config/database.php` `sarang_erp` connection, `SarangErpRepository`, env vars, `tax_filings.sarang_erp_ref_id` and `source = sarang_erp`; updated docs and `.cursorrules` to reflect two sister-app integrations only.

**Review Date**: 2027-10-09 (revisit only if sarang-erp is migrated to the shared MySQL server or a formal API contract is approved)

---

Decision: HO and JKT share one sheet each — 21-sheet workbook - 2026-10-10

**Context**: The concept/plan called the deliverable a "21-sheet workbook", but the sheet map in concept §10.2 did not add up to 21: its numbering ran to 22, and the implementation produced 23 unique sheets (with `Rincian 026C` defined twice by mistake on top of that). The only way to reach 21 was to decide how the two admin sites are represented.

**Options Considered**:

1. **HO and JKT combined into one sheet each**
   - ✅ Pros: Matches plan §2.2's arithmetic (4 fixed + 16 per-site + 1 consolidated = 21); matches the concept row that names "Rincian / P&L HO & JKT" as a single entry.
   - ❌ Cons: Those two sheets hold two stacked data blocks instead of one, so they differ in shape from the other 18 per-site sheets.

2. **HO and JKT as separate sheets**
   - ✅ Pros: Every per-site sheet has an identical shape.
   - ❌ Cons: Totals 23 sheets; the "21-sheet" naming used throughout the concept, plan, and Phase 2 exit criteria would have to be rewritten.

**Decision**: Option 1 — `Rincian HO & JKT` and `P&L HO & JKT`, each containing the HO block, a blank spacer row, a `JKT` label row, and the JKT block under one shared year-column header. Workbook totals 21 sheets in a fixed order: 4 fixed sheets, then all 8 Rincian sheets, then all 8 P&L sheets, then `SUMMARY P&L`.

**Rationale**: 21 is the number the whole concept is built on and the Phase 2 acceptance criterion is checked against, so the sheet map has to reconcile to it rather than the other way round.

**Implementation**: `WorkbookGeneratorService::SITE_ORDER` + `buildHoJkt()` on `RincianSheetBuilder`/`PnlSheetBuilder`; duplicate `Rincian 026C` removed; `SPT & PAYMENT` repositioned to sheet 3 after the streaming merge. concept §10.2 and plan §2.2 rewritten as the single canonical sheet list.

**Review Date**: when the generated workbook is compared sheet-by-sheet against the legacy manual workbook — if the original deliverable actually keeps HO and JKT on separate sheets, this decision flips to 23 and the docs follow.
