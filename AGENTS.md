# SKJ Live Chat System: Master Agent Engineering & UX/UI Directives

This repository contains the official **SKJ Live Chat System** (`skj-chat`) developed for **โรงเรียนสวนกุหลาบวิทยาลัย (จิรประวัติ) นครสวรรค์** (Suankularb Wittayalai Jiraprawat Nakhon Sawan School).

All AI agents operating within this workspace must operate as a **Senior Lead Full-Stack Architect** and **Principal Web UX/UI Designer**, upholding the highest standards of code reliability, performance, aesthetic beauty, and school brand dignity.

---

## 1. Architectural Invariants (Must Never Violate)

1. **Root Front Controller**:
   - The application uses `index.php` located directly at the project root (`FCPATH = __DIR__ . DIRECTORY_SEPARATOR`).
   - The Apache Document Root is `/var/www/html` (NOT `/var/www/html/public`).
   - Asset URLs must use `base_url('public/assets/...')` or configured aliases. Do not modify the document root layout without updating both `Dockerfile` and `index.php`.

2. **CodeIgniter 4 MVC Structure**:
   - Routes are defined explicitly in `app/Routes.php`. Auto-routing is disabled.
   - Controllers extend `App\Controllers\BaseController` to automatically inherit session management, authenticated agent context (`$this->currentAgent`), and database connection (`$this->db`).
   - Authentication must always be guarded via `checkAuth()` or `requireRole()`.

3. **Hybrid AI & Human Agent Handover**:
   - AI Assistant persona: **น้องกุหลาบ (SKJ AI Assistant)** using Google Gemini API + RAG from `tb_chat_ai_knowledge`.
   - When a human staff member sends a message in `ChatDesk::sendMessage`, the session's `is_bot_paused` flag is set to `1` automatically.
   - The AI Bot in `WidgetApi::sendMessage` will NOT reply if `is_bot_paused == 1`. Staff can unpause the bot when done.

4. **Security & Data Sanitization**:
   - Always use prepared statements via CodeIgniter 4 Query Builder or Models.
   - Escape all user-supplied data in views using `esc()`.
   - File uploads in `uploadAttachment` must strictly check extensions against whitelisted mime-types: `['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip']`.

---

## 2. Web UX/UI Design Standard: Suankularb Pink & Blue (ชมพู-ฟ้า)

Every page, widget, modal, and component must adhere to the school's heritage colors:

| Token | Hex Code | Purpose |
| :--- | :--- | :--- |
| `--skj-pink` | `#e91e63` | Primary brand pink, active tab, send buttons, highlight rings |
| `--skj-pink-dark` | `#c2185b` | High contrast pink for headings and text |
| `--skj-pink-light`| `#fce4ec` | Background tints for badges, avatars, and notifications |
| `--skj-pink-subtle`| `#fff1f5`| Active chat session card background |
| `--skj-blue` | `#1976d2` | Royal blue, secondary actions, system notices |
| `--skj-blue-dark` | `#0d47a1` | Deep blue accents |
| `--skj-blue-light`| `#e3f2fd` | Soft blue visitor message bubble background |

### Typography
- **Thai**: `K2D` font (Weights: 300, 400, 500, 600, 700). Set `line-height: 1.6` for all Thai text to prevent diacritic truncation.
- **English & Numbers**: `Inter` font (Weights: 400, 500, 600, 700) for timestamps, counts, and metrics.
- Enforce `overflow-wrap: anywhere; word-break: break-word;` on all message containers.

### Component Design Aesthetics
- No generic, basic HTML looks. Interfaces must feature subtle glassmorphism, soft drop shadows (`0 8px 24px rgba(233, 30, 99, 0.12)`), smooth transitions (`cubic-bezier(0.34, 1.56, 0.64, 1)`), and responsive layouts.
- Buttons must have micro-interactions (`transform: scale(0.98)` on `:active`, subtle lift on hover).
- Provide clear visual indicators for `online` (green `#10b981`), `busy` (amber `#f59e0b`), and `offline` (grey `#94a3b8`).

---

## 3. Specialized Workspace Skills Available

- **`skj-fullstack-dev`** (`.agents/skills/skj-fullstack-dev/SKILL.md`):
  Detailed blueprints for CI4 backend, MySQL database queries, Gemini RAG integration, Telegram alerts, and API optimization.
- **`skj-uxui-designer`** (`.agents/skills/skj-uxui-designer/SKILL.md`):
  Detailed design tokens, micro-interactions, responsive chat desk workflows, and widget styling rules.
