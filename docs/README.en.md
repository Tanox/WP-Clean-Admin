# Documentation Index and Writing Conventions (English)

> **Applies to**: 1.9.18  |  **Last updated**: 2026-11-25  |  **Status**: Meta standard
> This is the English mirror of [`README.md`](README.md), the index and metadata standard for the `docs/` directory. Development standards (coding, naming, commits, branching, review) live **solely** in the root [`CONTRIBUTING.md`](../CONTRIBUTING.md); this document does not duplicate them.
> Every current document carries a table of contents, an applicable baseline, a status label and a version-control table at the end; historical snapshots are the only exception.
> The Simplified Chinese originals remain the **authoritative** source. When the two diverge, the Chinese version wins; English mirrors must be updated in the same change that touches the Chinese original.

---

## 1. Documentation Map

### 1.1 Repository Root (Authoritative Documents)

| File | Audience | Responsibility | Status |
|------|----------|----------------|--------|
| [`CONTRIBUTING.md`](../CONTRIBUTING.md) | Contributors / AI assistants | **Single source of truth for development standards**: coding style, naming conventions, directory layout, commit format, branching, code review, testing, versioning and release | Authoritative |
| [`README.md`](../README.md) | Users | Feature overview, installation, usage, FAQ | User documentation |
| [`README.en.md`](../README.en.md) | English users | English mirror of `README.md`; feature list and version must stay in sync | User documentation |
| [`DEVELOPMENT.md`](../DEVELOPMENT.md) | Developers | Environment setup, common commands, prototype preview (quick reference) | Quick reference |
| [`AGENTS.md`](../AGENTS.md) | AI assistants | Working guidelines, directory boundaries, OpenSpec change flow | Assistant instructions |
| [`CHANGELOG.md`](../CHANGELOG.md) | Everyone | **Single source of truth for version history** | Authoritative |
| [`LICENSE`](../LICENSE) | Everyone | GPLv2 or later | Authoritative |

### 1.2 `docs/` (Engineering Documentation)

| File | Responsibility | Status |
|------|----------------|--------|
| `README.md` (this file's Chinese original) | Documentation index, writing format, terminology and status-labeling rules | Meta standard |
| `README.en.md` (this file) | English mirror of the documentation index | Meta standard (EN) |
| `tasks.md` | Active / backlog task list | Unfinished items only |
| `tasks-done.md` | Archive of completed tasks | Append-only; keeps the main list compact |
| [`项目概述.md`](项目概述.md) / [`overview.en.md`](overview.en.md) | Background, goals, technology stack, architecture design, directory tour | Current (ZH / EN) |
| [`安装部署指南.md`](安装部署指南.md) / [`installation-guide.en.md`](installation-guide.en.md) | Environment requirements, installation and configuration, multi-environment setups, verification | Current (ZH / EN) |
| [`功能模块说明.md`](功能模块说明.md) | Per-module core features, business flow diagrams and key logic | Current (ZH only) |
| [`API接口文档.md`](API接口文档.md) / [`openapi.yaml`](openapi.yaml) | HTTP interface: path / method / parameters / responses / error codes / examples; OpenAPI 3.1 machine-readable contract | Current (ZH; `openapi.yaml` is language-neutral) |
| [`user-guide.md`](user-guide.md) / [`user-guide.en.md`](user-guide.en.md) | Roles and capabilities, operation flow diagrams, step-by-step procedures | Current (ZH / EN) |
| [`FAQ与故障排除.md`](FAQ与故障排除.md) / [`faq-troubleshooting.en.md`](faq-troubleshooting.en.md) | Frequently asked questions, troubleshooting matrix, diagnostics, emergency recovery | Current (ZH / EN) |
| [`优化建议.md`](优化建议.md) | Code / architecture / performance / UX evaluation with 12 optimization proposals (background, plan, impact, priority, implementation status) | Evaluation (all 12 implemented in 1.9.18) — ZH only |
| [`api-reference.md`](api-reference.md) / [`api-reference.en.md`](api-reference.en.md) | Quick reference for global functions, filters, options and core classes (T014) | Quick reference; the OpenAPI documents above are authoritative for the HTTP contract |
| [`临时文件管理规范.md`](临时文件管理规范.md) | Temporary-file directory, naming and cleanup conventions | Quick reference — ZH only |

### 1.3 Other Directories

| Directory | Contents | Rules |
|-----------|----------|-------|
| [`openspec/`](../openspec/) | Feature specs `specs/`, change proposals `changes/`, archive `archive/`, `project.md`, `AGENTS.md` | Spec and proposal documents must carry `OPENSPEC:START/END` markers |
| [`prototype/`](../prototype/) | Four static prototype files: `index.html`, `app.js`, `wpca-admin.css`, `wpca-components.css` | No PHP, no subdirectories, never loaded at runtime |
| [`archive/`](../archive/) | Legacy dead code archived in 1.9.12 (`legacy-includes-1.9.12/`) | **Read-only** — never reference, modify or copy back into the plugin |
| [`.github/`](../.github/) | CI, PR/Issue templates, `SECURITY.md`, `SUPPORT.md` | Process configuration |

> Directory boundaries and the hard constraint "the plugin directory holds runtime code only" are defined in `AGENTS.md` §2 and `CONTRIBUTING.md` §2.

### 1.4 Recommended Reading Paths

| Reader | Path |
|--------|------|
| Site administrators | [`用户操作指南`](user-guide.md) → [`安装部署指南`](安装部署指南.md) → [`FAQ与故障排除`](FAQ与故障排除.md) |
| Developers / extension authors | [`项目概述`](项目概述.md) → [`功能模块说明`](功能模块说明.md) → [`API接口文档`](API接口文档.md) + [`openapi.yaml`](openapi.yaml) |
| Contributors / AI assistants | Root [`CONTRIBUTING.md`](../CONTRIBUTING.md) → this index → [`tasks.md`](tasks.md) → [`优化建议.md`](优化建议.md) |
| English readers | this file → [`user-guide.en.md`](user-guide.en.md) → [`installation-guide.en.md`](installation-guide.en.md) → [`faq-troubleshooting.en.md`](faq-troubleshooting.en.md) → [`api-reference.en.md`](api-reference.en.md) → [`overview.en.md`](overview.en.md) |

---

## 2. Document Writing Format (Uniform)

1. **Headings**: ATX (`#`), continuously numbered sections, **no duplicate numbering within one document**; `##` is a top-level section, `###` a subsection.
2. **Language**: Simplified Chinese is primary. English documents use the `<original-name>.en.md` naming convention (e.g. `user-guide.en.md`) and mirror the Chinese original section by section. Technical identifiers (class names, function names, paths, commands) use inline code backticks.
3. **Path references**: use repository-root-relative paths written as `` `wpcleanadmin/includes/autoload.php:46` `` (path:line). Never reference paths that no longer exist (e.g. `prototype/ui/`, `prototype/php/`).
4. **Tables**: used for lists, mappings and status comparisons; headers and separator rows must be complete; status values are standardized to `待办` / `进行中` / `已完成` / `阻塞` (todo / in progress / done / blocked).
5. **Task lists**: use `- [ ]` / `- [x]`; `docs/tasks.md` holds unfinished items only, completed items move to `docs/tasks-done.md`.
6. **Code blocks**: must declare a language (`php`, `bash`, `json`, `text`); commands run at the repository root by default, and must state `cd wpcleanadmin` explicitly when they need to run inside `wpcleanadmin/`.
7. **OPENSPEC markers**: only spec and proposal documents under `openspec/` use `<!-- OPENSPEC:START -->` / `<!-- OPENSPEC:END -->`; root-level and `docs/` documents **do not**.
8. **File naming**: dated snapshots use `name_YYYYMMDD.md`; stable documents use fixed names. Historical files keep their original names and are never renamed.
9. **Encoding**: UTF-8 without BOM, LF line endings, trailing newline preserved.

---

## 3. Version and Status Labels (Mandatory)

- Every document header uses a blockquote (`>`) carrying `适用基线` (applicable baseline — the current plugin version), `更新日期` (last updated) and, where relevant, `状态` (authoritative / historical snapshot / in progress).
- The version must match `Version:` in `wpcleanadmin/wp-clean-admin.php`; **never hard-code an outdated version** in a document as "the current version".
- When a historical document mentions an older version (e.g. 1.8.3), it must also be labeled "historical baseline snapshot, does not reflect the current state" and marked as a historical snapshot in the §1 index.
- When referring to a specific commit, use the short hash (e.g. `5fd486d`); version history belongs in `CHANGELOG.md` only — other documents link to it instead of copying it.
- Standard status vocabulary: authoritative, quick reference, user documentation, assistant instructions, historical snapshot, in progress, todo, done, blocked, read-only.

---

## 4. Terminology Consistency

The complete glossary lives in `CONTRIBUTING.md` §10. Summary (left column is the required wording, right column lists outdated wording to avoid):

| Required wording | Avoid / outdated wording |
|------------------|--------------------------|
| Authoritative system (or the `modules/` system) | "new modular architecture (recommended)", "dual-system copies" |
| Archive layer (or `archive/`) | "legacy class files (backward compatible)", "traditional layer" |
| Task trait / composed trait | "sub-modules", "trait inside trait" |
| Governance split | the generic term "refactoring" |
| Prototype (four static files) | `prototype/ui/`, `prototype/php/`, dual UI entry points |
| Bootstrap main file | "Core class" (when referring to `wp-clean-admin.php`) |
| Version source (plugin header `Version:` + `WPCA_VERSION`) | each file maintaining its own version number |

---

## 5. Documentation Update Sync Checklist

After changing code, check whether documentation needs syncing using the table below (see `CONTRIBUTING.md` §9.2 for details):

| Change type | Documents that must be updated |
|-------------|------------------------------|
| Version bump | Per the `CONTRIBUTING.md` §9.2 checklist: `wp-clean-admin.php`, `readme.txt`, `README.md`/`README.en.md`, `CHANGELOG.md`, `prototype/index.html`, `AGENTS.md` §3, language file headers, `CONTRIBUTING.md`/`docs/README.md`/`docs/tasks.md` applicable baselines, and the `@version` header of each changed PHP file |
| Directory layout change | `CONTRIBUTING.md` §2, `DEVELOPMENT.md`, `AGENTS.md` §2, this file §1, `wpcleanadmin/includes/modules/README.md` |
| New or changed public API | `CHANGELOG.md`, the matching `openspec/specs/<feature>/spec.md` |
| Completed governance split | `docs/tasks.md` (move out) → `docs/tasks-done.md` (archive), `CHANGELOG.md` |
| CI / branching policy change | `.github/workflows/ci.yml`, `CONTRIBUTING.md` §6/§7, `docs/tasks.md` |
| Prototype layout change | `prototype/index.html`, `DEVELOPMENT.md`, this file §1.3 |
| New or changed English mirror | The matching `.en.md` file of the Chinese original (including the "applicable baseline" header and the trailing version-control table); when adding a Chinese document, register the English mirror status in this file §1.2 |

---

## 6. Known Historical Deviations (Already Reconciled)

| Location | Historical deviation | Resolution |
|----------|----------------------|------------|
| `DEVELOPMENT.md` | Referenced non-existent `prototype/php/`, `prototype/ui/`, `USE_MOCK`, `Core::init_modules()` | Rewritten against the current code |
| `AGENTS.md` | Two `## 3.` headings, §5.2 claiming every document needs OPENSPEC markers, §7 module table listing archived legacy class names | Corrected |
| `.github/PULL_REQUEST_TEMPLATE.md` | Referenced `CONTRIBUTING.md`, which did not exist at the time | File created |
| `README.md` / `README.en.md` / `SECURITY.md` / `openspec/AGENTS.md` | Inconsistent historical version labels (1.9.13 / 1.9.10 / 1.8.x / 1.8.5) | Unified to 1.9.14 on 2026-11-07, then kept in sync with each release (currently 1.9.18) |
| `wpcleanadmin/includes/modules/README.md` | Described non-existent `frontend/`, `admin/ajax/` and `Core::init_modules()` | Rewritten against the actual directories |
| `.github/workflows/ci.yml` | Triggered on a non-existent `develop` branch | Aligned to `main` + PRs |
| `docs/设计规范_20260808.md`, `docs/审计报告_20260808.md` | Based on the 1.8.3 baseline, describing an obsolete prototype layout | Deleted on 2026-11-25 (content had become invalid and no longer matched the current prototype); this index no longer references them |

---

## 7. Document Version Control

| Document version | Applies to plugin version | Date | Maintainer | Summary |
|------------------|---------------------------|------|------------|---------|
| 1.0 | 1.9.18 | 2026-11-25 | Tanox | English mirror of the documentation index: documentation map, writing format, version and status labels, terminology, sync checklist and historical deviations |
