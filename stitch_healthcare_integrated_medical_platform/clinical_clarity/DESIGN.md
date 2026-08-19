---
name: Clinical Clarity
colors:
  surface: '#f9f9ff'
  surface-dim: '#d4daea'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f1f3ff'
  surface-container: '#e8eeff'
  surface-container-high: '#e3e8f9'
  surface-container-highest: '#dde2f3'
  on-surface: '#161c27'
  on-surface-variant: '#424656'
  inverse-surface: '#2a303d'
  inverse-on-surface: '#ecf0ff'
  outline: '#727687'
  outline-variant: '#c2c6d8'
  surface-tint: '#0054d6'
  primary: '#0050cb'
  on-primary: '#ffffff'
  primary-container: '#0066ff'
  on-primary-container: '#f8f7ff'
  inverse-primary: '#b3c5ff'
  secondary: '#006a6a'
  on-secondary: '#ffffff'
  secondary-container: '#90efef'
  on-secondary-container: '#006e6e'
  tertiary: '#555a5d'
  on-tertiary: '#ffffff'
  tertiary-container: '#6d7276'
  on-tertiary-container: '#f4f8fc'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae1ff'
  primary-fixed-dim: '#b3c5ff'
  on-primary-fixed: '#001849'
  on-primary-fixed-variant: '#003fa4'
  secondary-fixed: '#93f2f2'
  secondary-fixed-dim: '#76d6d5'
  on-secondary-fixed: '#002020'
  on-secondary-fixed-variant: '#004f4f'
  tertiary-fixed: '#dfe3e7'
  tertiary-fixed-dim: '#c3c7cb'
  on-tertiary-fixed: '#171c1f'
  on-tertiary-fixed-variant: '#43474b'
  background: '#f9f9ff'
  on-background: '#161c27'
  surface-variant: '#dde2f3'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
  3xl: 64px
  container-max: 1440px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 32px
---

## Brand & Style
The design system is anchored in a philosophy of "Clinical Clarity"—a minimalist, professional aesthetic tailored for healthcare SaaS environments. It prioritizes cognitive ease, trust, and accessibility to support high-stakes decision-making. 

The visual style blends **Modern Corporate** reliability with **Minimalist** precision. It utilizes expansive whitespace to reduce visual noise, ensuring that critical patient data and clinical insights remain the focal point. The emotional response is one of calm, competence, and efficiency, avoiding the clinical "coldness" of legacy software by using soft surface treatments and approachable typography.

## Colors
The palette is built on a foundation of "Healthcare Blue" and "Teal," colors traditionally associated with stability and hygiene. 

- **Primary (#0066FF):** Used for primary actions, active states, and brand identifiers.
- **Secondary (#008080):** A calming teal used for secondary accents and categorized clinical data.
- **Backgrounds:** The interface utilizes a tiered white and light blue-gray system (`#F8FAFC`). Pure white is reserved for high-elevation cards and inputs, while the light gray provides a soft base for the dashboard background.
- **Typography:** Dark navy (`#1A202C`) provides high-contrast legibility for headings, while mid-range grays (`#4A5568`) are used for body text to reduce eye strain during prolonged use.
- **Semantic Colors:** Success, Warning, and Error colors are desaturated slightly to remain visible without being jarring, maintaining the professional tone of the design system.

## Typography
This design system utilizes **Inter** for its exceptional legibility and systematic approach to letter spacing. The typographic hierarchy is strictly enforced to guide users through complex medical records.

- **Headlines:** Use Bold and Semi-Bold weights with slight negative letter-spacing to create a clean, modern "editorial" feel for dashboard headers.
- **Body Text:** Uses a 1.5x line-height ratio to maximize readability in dense clinical notes.
- **Labels:** Small, uppercase labels with increased tracking (letter-spacing) are used for data categories and table headers to distinguish them from actionable content.
- **Accessibility:** Minimum body size is 14px for utility text, but 16px is the standard for primary information.

## Layout & Spacing
The layout follows a strict **8px grid system** to ensure consistency across web and mobile platforms. 

- **Dashboard Layout:** Utilizes a 12-column fluid grid for desktop with 24px gutters. Sidebars are fixed at 280px to maximize the available workspace for data tables and charts.
- **Mobile Layout:** Transitions to a single-column flow with 16px side margins. Bottom sheets are preferred over modals for complex inputs to improve thumb-reachability.
- **Spacing Rhythm:** Vertical spacing between cards and sections should primarily use `lg` (24px) or `xl` (32px) to maintain the "spacious" feel requested in the design narrative.

## Elevation & Depth
The design system communicates hierarchy through **Tonal Layers** and **Ambient Shadows**. 

1. **Base Level:** The background is the lowest layer, using a subtle blue-gray tint.
2. **Card Level:** Primary content lives on white surfaces with a very soft, diffused shadow (Blur: 15px, Opacity: 4%, Y-offset: 4px). This creates a "floating" effect that is modern and non-distracting.
3. **Interactive Level:** Hover states on cards or buttons trigger a slightly deeper shadow and a subtle upward translation (1-2px) to provide tactile feedback.
4. **Overlay Level:** Modals and dropdowns use a high-diffusion shadow with a 10% opacity black tint to create a clear separation from the interface background. 

Avoid heavy borders; use 1px strokes in a light gray (`#E2E8F0`) only when extra definition is needed between adjacent white elements.

## Shapes
The shape language is "Rounded," striking a balance between professional geometry and approachable software.

- **Primary Radius:** 8px (`rounded`) for buttons, input fields, and small UI components.
- **Container Radius:** 12px to 16px (`rounded-lg` or `rounded-xl`) for main dashboard cards and mobile content sections.
- **Badges:** Use a fully pill-shaped (32px+) radius to distinguish status indicators from clickable buttons.

## Components

- **Buttons:** 
  - *Primary:* Solid Primary Blue, white text, 8px radius. 
  - *Secondary:* Light blue-gray background with Primary Blue text for low-priority actions. 
  - *Ghost:* No background, Primary Blue text, used for navigation or auxiliary actions.
- **Cards:** White background, 16px radius, subtle 1px border (`#E2E8F0`), and the defined ambient shadow. Padding within cards should be a minimum of 24px.
- **Input Fields:** 8px radius, white background with a 1px light gray border. On focus, the border transitions to Primary Blue with a 2px soft glow (outer shadow).
- **Status Badges:** Compact, pill-shaped components. Use a "Soft Tint" approach: a 10% opacity background of the status color (Success/Warning/Error) with high-contrast bold text of the same color.
- **Lists:** Clean rows with 1px bottom dividers. Use 16px vertical padding for list items to ensure high touch-targets on mobile.
- **Data Visuals:** Charts should use the Primary and Secondary color palette, supplemented by neutral grays for axes and grids to maintain the minimalist aesthetic.