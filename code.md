# code.md — what works in this system + how we ship code

> Note: `agent.md` is the maintainer's local memory — it is git-ignored and NOT in this
> public repo. The workflow below is for the maintainer; contributors just follow the
> README + tests.

## What works right now

| Module | Status | How to verify |
| --- | --- | --- |
| Project scaffold (Laravel 13.39 + React kit) | ✅ works | `php artisan test` → 27 passed; `npm run build`; `php artisan migrate` on MariaDB `aurora_pos` |
| Auth (starter-kit login/register) | ✅ works | `php artisan test --filter=Auth`; register at `/register` |
| Stores / RBAC / catalog / POS / ... | ⬜ not built | Phase 3+ |

Append a row here every time a module lands and is verified locally.

## The workflow (every session, every module)

1. **Read `agent.md` — only the LAST entry in `## SESSION LOG`** (the newest `###`
   compact). That tells you where we left off and the next step. Don't read the rest.
2. Pick up the next step from that compact.
3. Write the code (style rules below).
4. **Test locally** before anything else: `php artisan test`, plus a manual smoke pass
   of the module in the browser (`php artisan serve`). Fix what breaks.
5. **Commit** — Conventional Commits, one logical change per commit, message via heredoc
   and MUST end with the co-author trailer:

   ```bash
   git commit -F - <<'EOF'
   feat(pos): barcode scan adds item to cart

   Co-authored-by: CommandCodeBot <noreply@commandcode.ai>
   EOF
   ```

6. **Push** to `origin main` of `https://github.com/maileditorportfolio-star/aurora-pos.git`.
7. **Append a new compact to `agent.md` SESSION LOG** (where we are, what works, open
   issues, next step). `agent.md` is git-ignored — never commit it, never push it.

Extra workflows when needed:

- DB changes: always a migration + update `docs/database.md` (ERD per module).
- New permission: add to seeder, guard route + controller + hide in UI.
- Anything touching stock/money: DB transaction + stock movement + audit log + test.

## Code like a human, not an AI

- Short functions with real names. If a name needs a comment, rename it.
- No comment banners, no `// ===== section =====`, no narrating what the code obviously does.
- Three similar lines beat a premature helper. No abstractions for one-time operations.
- Validate at the edges (requests, user input), trust the internals. No defensive code
  for impossible states.
- Don't add error handling for scenarios that can't happen.
- Delete dead code completely — no commented-out blocks, no `_unused` renames.
- Follow what's already in the file. Consistency with the codebase beats personal taste.
- Money math: never floats. `decimal` columns + round to 2dp at the edges.
- One concern per class: controllers stay thin, logic lives in Services, SQL in models.
