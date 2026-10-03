# CLAUDE.md

@AGENTS.md

## Claude Code

- Skills in `.claude/skills/`: the architecture skills (`application-layer`, `dependency-injection`, `error-handling`, `validation`, `events`, `testing-architecture`, `domain-layer-cqrs`, `plugin-boundaries`), `fortify-security` (read it for any change to input, output, access or the scanners), `plugin-upgrades` (anything that reaches an installed site), `plugin-testing`, `testing-best-practices`, and October's own `octobercms-plugin-development`, `octobercms-model-development`, `octobercms-backend-controllers`, `octobercms-ajax-framework`.
- A changed PHP file is formatted by the `PostToolUse` hook in `.claude/settings.json`; still run `make ready` before you say a change is done, and report its result.
- The five modules live in sibling repositories (`../oc-fortify-admin-ip-access-plugin`, `../oc-fortify-ip-blocker-plugin`, `../oc-fortify-smart-ip-blocker-plugin`, `../oc-fortify-csp-plugin`, `../oc-fortify-input-sanitizer-plugin`). A change to the contract listed in AGENTS.md is checked against each of them.
- Never push to `main`: work on a branch and open a pull request (see *Git workflow* in AGENTS.md). Write everything in English.
