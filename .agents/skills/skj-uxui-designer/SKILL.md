---
name: skj-uxui-designer
description: >-
  Expert Web UX/UI Designer skill for SKJ Live Chat. Use when crafting or enhancing web interfaces, responsive layouts, design tokens, micro-interactions, animations, chat widget components, live desk workflows, accessibility, or typography aligned with Suankularb Jiraprawat's Pink & Blue brand identity.
---

# SKJ Live Chat: Principal Web UX/UI Design System Manual

This skill equips the agent with world-class product design principles, design system tokens, and front-end UX best practices tailored specifically for the **SKJ Live Chat System** (`skj-chat`).

---

## 1. Brand Identity & Color Palette (Suankularb Pink & Blue: ชมพู-ฟ้า)

Suankularb Wittayalai (Jiraprawat) Nakhon Sawan carries a storied institutional identity defined by **Pink & Blue**. The digital experience must reflect prestige, warmth, modern accessibility, and official school authority.

### Color Tokens

```css
:root {
    /* Brand Primary: Pink (ชมพู) */
    --skj-pink:          #e91e63; /* Rose Pink - Main CTA & Accent */
    --skj-pink-hover:    #d81b60; /* Darker Pink for Hover/Active states */
    --skj-pink-dark:     #c2185b; /* High-contrast text & deep headers */
    --skj-pink-light:    #fce4ec; /* Soft badge background & tints */
    --skj-pink-subtle:   #fff1f5; /* Active item background & subtle glow */

    /* Brand Secondary: Blue (ฟ้า/น้ำเงิน) */
    --skj-blue:          #1976d2; /* Royal Blue - System / Secondary CTA */
    --skj-blue-hover:    #1565c0; /* Hover state for Blue elements */
    --skj-blue-dark:     #0d47a1; /* Deep navy tone for strong contrast */
    --skj-blue-light:    #e3f2fd; /* Light blue badge & visitor bubble */
    --skj-blue-subtle:   #f0f7ff; /* Card background & subtle tints */

    /* Brand Gradient */
    --skj-gradient:      linear-gradient(135deg, #e91e63 0%, #1976d2 100%);
    --skj-gradient-soft: linear-gradient(135deg, #fff1f5 0%, #f0f7ff 100%);

    /* Neutral & Surface Palette */
    --bg-app:            #f4f6fa; /* Canvas background */
    --bg-surface:        #ffffff; /* Card / Modal / Sidebar background */
    --border-light:      #e2e8f0; /* Default border */
    --border-subtle:     #f1f5f9; /* Subtle divider */

    /* Text Colors */
    --text-primary:      #1e293b; /* Slate 800 - Primary headings & text */
    --text-secondary:    #64748b; /* Slate 500 - Secondary captions & metadata */
    --text-muted:        #94a3b8; /* Slate 400 - Timestamps & placeholders */

    /* Functional Status */
    --status-online:     #10b981; /* Emerald 500 */
    --status-busy:       #f59e0b; /* Amber 500 */
    --status-offline:    #94a3b8; /* Slate 400 */
    --status-danger:     #ef4444; /* Red 500 */
}
```

---

## 2. Typography Hierarchy

- **Thai Primary UI Font**: `K2D` (Google Fonts: 300, 400, 500, 600, 700). K2D provides exceptional readability, modern curves, and clean geometric balance for Thai letterforms.
- **Latin & Numbers Secondary Font**: `Inter` (Google Fonts: 400, 500, 600, 700). Pairs seamlessly for timestamps, metrics, IDs, and English labels.
- **Thai Typography Rules**:
  - Always enforce `line-height: 1.6` to `1.7` for Thai body copy to prevent Thai tone marks and vowel diacritics (วรรณยุกต์ สระบน-ล่าง) from clipping.
  - Apply `overflow-wrap: anywhere; word-break: break-word;` on message bubbles to prevent long Thai words or URLs from overflowing containers.

```html
<!-- Font Import Declaration -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

---

## 3. Surface 1: Agent Live Chat Desk (`chat/desk`) UX Standards

The Agent Desk is an intensive, high-throughput productivity dashboard used by teachers and administrative staff. It must be blazing fast, ergonomic, and eliminate cognitive overload.

### Key Layout Zones
1. **Queue Panel (Left - 320px - 360px)**:
   - Filter Tabs: `ทั้งหมด (All)`, `รอตอบ (Unassigned)`, `ของฉัน (Mine)`, `ปิดแล้ว (Closed)`.
   - Each tab includes live count badges.
   - Real-time search filter for visitor name, phone number, and internal notes.
   - Session Card states:
     - `Active / Selected`: Left pink accent bar (`3px solid var(--skj-pink)`), light pink background (`#fff1f5`).
     - `Unread User Message`: Bold name, pulsing pink unread dot, high-contrast preview snippet.
     - `Bot Handled`: Small robot badge (`<i class="fa-solid fa-robot"></i> น้องกุหลาบ`).

2. **Active Chat Conversation (Center - Flex 1)**:
   - Header: Visitor identity (Name, Tel, Assigned Agent dropdown, Bot Pause toggle button, Session Close button).
   - Scrollable Message Log:
     - **Visitor Message**: Left-aligned, light grey/blue bubble (`#f1f5f9` or `#e3f2fd`), dark slate text.
     - **Admin Message**: Right-aligned, brand gradient (`linear-gradient(135deg, #e91e63, #d81b60)`), crisp white text.
     - **AI Assistant Message**: Left-aligned with subtle purple/pink border and `"น้องกุหลาบ AI"` badge.
     - **System Notices**: Centered, subtle pill badge (`#f8fafc`, border `#e2e8f0`).
   - Sticky Input Area:
     - Multi-line autosizing textarea (`Shift+Enter` = newline, `Enter` = send).
     - Quick Action bar: Canned replies modal trigger (`/` shortcut), File attachment button, Emoji picker trigger.

3. **Visitor Context & Internal Notes (Right Drawer - 280px)**:
   - Collapsible panel showing visitor device, IP, session start time, assigned staff member, and editable internal staff notes.

---

## 4. Surface 2: Public Client Widget (`skj-chat-widget.js`) UX Standards

The embeddable widget represents the first touchpoint for parents, prospective students, and community members on `skj.ac.th`.

### Design Guidelines
- **Launcher Button**:
  - Size: `62px x 62px` circle with 2px white border and soft pink/blue drop shadow (`0 8px 24px rgba(233, 30, 99, 0.35)`).
  - Hover micro-interaction: Scale `1.08` with cubic spring easing (`cubic-bezier(0.34, 1.56, 0.64, 1)`).
  - Unread badge: Red notification dot with pulse ring.
- **Chat Window**:
  - Size: `380px` wide, max `560px` height (with responsive `calc(100vw - 32px)` fallback on mobile).
  - Border radius: Modern `20px` smoothed corners.
  - Header: Rich brand gradient with school emblem, online status indicator ("พร้อมให้บริการ"), and minimize button.
  - Pre-chat / Welcome Screen:
    - Warm welcome header featuring "น้องกุหลาบ" AI avatar.
    - Quick Inquiry Chips (e.g. `สมัครเรียน`, `ตารางเรียน`, `ติดต่อฝ่ายวิชาการ`) allowing 1-click prompt dispatch.
- **Mobile Experience**:
  - On screens `<= 480px`, transition smoothly to fullscreen mode with bottom-safe-area padding for iOS Safari.

---

## 5. Micro-Animations & Sensory Feedback

- **Typing Indicator**:
  - 3 bouncing dots using `var(--skj-pink)` with staggered CSS animation delay (`0s`, `0.2s`, `0.4s`).
- **Sound Design**:
  - Discreet, non-intrusive sound chime when receiving new visitor messages (with user-configurable mute toggle in topbar).
- **Haptic & Visual Feedback**:
  - SweetAlert2 notifications customized with brand pink confirmation buttons (`#e91e63`).
  - Active button states: CSS `:active { transform: scale(0.97); }`.
  - Skeleton shimmer loaders when queues or messages are fetching.
