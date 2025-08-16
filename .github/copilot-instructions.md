# Copilot repository instructions (Drupal 11 — ARS Apps)

## Project scope & stack

- This repo is a **Drupal 11** site (PHP 8.3, Composer).
- Keep changes within our codebase (custom modules/themes) and committed config; do not modify `core/` or `vendor/`.

## How to work a user story (when I assign you)

- Create a branch: `feat/<issue-number>-<short-slug>`.
- Open a **Draft PR** immediately with `Closes #<ISSUE_NUMBER>` in the description.
- Use `/docs/story-template.md` to structure the PR description. Fill these sections:
  - **Functional requirements**
  - **Acceptance criteria**
  - **Technical implementation plan**
  - **QA testing steps**
- Keep commits **small and vertical**; explain key decisions in the PR description.

## Build & tooling (agent/CI safe)

- Install deps: `composer install --no-interaction --prefer-dist`.
- Run style checks if configured: `phpcs --standard=Drupal,DrupalPractice` (or skip if not present).
- If you add code that needs schema or config:
  - Put **config** under `config/sync/` in YAML.
  - Put **config schema** in the affected module: `<module>/config/schema/*.schema.yml`.
  - Ensure config files are committed (no local-only changes).

## Drupal 11 coding rules (follow strictly)

- **Security**
  - Escape output using Twig autoescape; use `t()` / `TranslatableMarkup` for strings.
  - Route access: use proper access checks/permissions; protect POST with CSRF tokens.
  - Sanitize/validate request data; never trust query params.
- **Performance**
  - Set render cache metadata (`#cache` contexts/tags/max-age) on render arrays.
  - Prefer injected services; avoid static service container calls.
  - Use entity queries/fields selectively; avoid loading all nodes.
- **Standards**
  - Follow Drupal coding standards and naming; use dependency injection in services/plugins.
  - Avoid deprecated APIs (target Drupal 11); note change records in PR if replacing APIs.
- **Testing (when practical)**
  - Prefer Kernel/Functional tests for nontrivial logic; otherwise, at least document manual QA steps clearly.

## File locations

- Custom modules: `web/modules/custom/<module_name>/`.
- Tests: `<module_name>/tests/src/...` (Kernel/Functional as appropriate).
- Config schema: `<module_name>/config/schema/*.schema.yml`.

## PR expectations

- PR must remain focused on the assigned story; split into follow-ups if scope grows.
- Respond to automated reviews (Claude PR Review & Security) and fix findings.
- Only commit what is required; no unrelated refactors.

## When information is missing

- Ask clarifying questions **in PR comments** and propose a minimal plan; wait for my confirmation before large design choices.

