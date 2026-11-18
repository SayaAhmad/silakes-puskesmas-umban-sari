# Product

<!-- impeccable:product-schema 1 -->

> Repository evidence: `docs/PRD.md` v1.0 (17 Nov 2026), `README.md`, `GLOSSARY.md`, `docs/PANDUAN.md`, `docs/adr/0001-stack-dan-antrian-per-poli.md`, plus routes, controllers, models, and views at commit `8b37538`. Two product-interview rounds ran on 18 Nov 2026 and their answers are the authoritative ones. Facts carried over from the repo alone are marked **FROM REPO** where they could otherwise be read as confirmed intent.

## Platform

web

## Users

**Masyarakat (anonymous)** — warga around Puskesmas Umban Sari, Pekanbaru. They arrive on mid-range Android phones over mobile data. Their job: find out whether a service runs today, what to bring, and take a queue number from home so they do not have to queue at the loket early. There is no account and no sign-up: zero-friction entry is a product decision, not a temporary gap.

**Petugas** — loket/administrasi staff working the panel from a desk PC. Their job: run the day's queue (panggil, selesai, lewat, batal, register walk-ins at the counter) and keep pengumuman and FAQ current. They are not IT staff; the panel has to explain itself.

**Admin** — the person responsible for this installation. Everything petugas can do, plus poli, layanan, dokter, jadwal, pengguna, pengaturan, and the activity log.

One installation per puskesmas. Accounts exist only for petugas and admin, created by an admin; public registration is not part of the product.

## Product Purpose

SILAKES (Sistem Informasi Layanan Kesehatan) is the service-information and queue system for Puskesmas Umban Sari. The public site in Bahasa Indonesia answers what is available, when, on which poli, and what the visitor must bring; the panel is where puskesmas staff maintain that content and run the daily queue.

It exists because the puskesmas' own report identified the same three problems: information about schedules, requirements, and flow is hard to find; people come to the counter only to ask; and the morning rush stacks up at the loket because everyone must take a number in person.

Success, in the puskesmas' own order of importance (**interview**):

1. Less crowding at the loket before opening, because numbers are taken from home.
2. Fewer repeat questions at the counter, because the site answers them completely.
3. Measurable wait time and throughput in the panel dashboard.

**FROM REPO** the stated boundary is that the system enforces no diagnosis, does not replace consultation with health staff, and holds no electronic medical record. It manages service information and visit administration only.

## Positioning

A puskesmas profile site informs; SILAKES informs *and* produces an action in the same visit. Two mechanisms carry that and neither is copied from a neighbouring site:

1. **Antrian online** — a resident takes a number before arriving and gets a digital ticket (`kode`, `nomor`, estimasi jam dilayani, sisa antrian di depan). The number is what changes the morning; the information only supports it.
2. **Layar panggilan real-time** — the waiting room screen is fed by the same queue the petugas drive, so a number called at the loket is legible on the TV without anyone shouting it.

Everything published is server-rendered from one database and edited in the panel, so a schedule change by admin is live on the public site without a deploy.

## Operating Context

- **Real deployment.** SILAKES is intended to run for the real Puskesmas Umban Sari (**interview**); treat its content as production truth, not demo data.
- **Who runs it.** Puskesmas staff. Petugas handle daily content and the queue; one designated admin manages accounts, poli, jadwal, and settings. There is no dedicated IT team on site (**interview**).
- **Devices in play** (**interview**): the layar panggilan is a full-screen browser in the waiting room (TV or large display); the panel is used on desk PCs at the puskesmas; the public site is read on warga's mid-range Android phones over mobile data.
- **Live queue transport** is plain polling: the layar and the ticket page read `/antrian/status` (JSON) on a timer and re-render. No websockets, no push infrastructure.
- **Installation** — **FROM REPO** `GLOSSARY.md`: "Userland install", PHP/MariaDB under `~/.local` without root, because sudo is not available on the target VPS. Deployment is expected to survive that constraint.
- **Time and language**: `Asia/Jakarta`, locale `id`, Indonesian date and time formatting throughout; the whole interface is Bahasa Indonesia with no i18n switcher.
- **Queue rules that shape every screen**: kode = huruf poli + 3 digits (`U-001`, `G-002`), unique per poli per day; numbering is per poli per tanggal; an online number is refused when the poli has no active jadwal that weekday; estimasi jam dilayani = last called number's time + (selisih nomor × durasi layanan) and is never shown as a time already past; the antrian status flow is `menunggu` → `dipanggil` → `selesai` with `lewat` and `batal` as branches, and `sumber` is `online` or `loket`.
- **Huruf poli** is kept unique (U, G, K, A, z, T) so codes cannot collide across poli.
- **Demo accounts and demo seeds exist** (`admin@silakes.test`, `petugas@silakes.test`, seeded content). They are development fixtures; the passwords must be changed before real use and seeded content must never be presented as the puskesmas' actual data.
- Real service requirement lists, doctor schedules, and puskesmas details exist and will be entered or imported by staff (**interview**).

## Capabilities and Constraints

**Public site** — beranda with automatic buka/tutup from operating hours and today's jadwal, live queue per poli, layanan unggulan, pengumuman, FAQ; layanan list and detail (persyaratan, durasi, poli, alur); jadwal praktik per hari and poli; alur pendaftaran; antrian online form and digital ticket with status; layar panggilan; pengumuman and FAQ; kontak with map and WhatsApp link.

**Panel** — login with rate limiting and role separation; dasbor with today's KPIs, a 7-day chart drawn in SVG/CSS with no external chart library, per-poli composition, recent activity; antrian management by tanggal and poli with panggil, selesai, lewat, batal, and one-click panggil-berikutnya plus manual walk-in registration; full CRUD for poli, layanan, dokter, jadwal, pengumuman, FAQ; site settings (identity, operating hours per day, running text, queue quota); pengguna management and activity log for admin only.

**Boundaries** — **FROM REPO** `docs/PRD.md` §8: electronic medical records, digital prescriptions, payment/BPJS, automatic WhatsApp/SMS notification, native mobile apps, teleconsultation, and P-Care/Kemenkes integration are out of v1 scope. Also out: enkel-diagnosis, and any storage of medical records.

**Terminology is binding.** `GLOSSARY.md` is the canonical vocabulary for code, docs, and UI: Poli · Layanan · Tenaga kesehatan · Jadwal · Antrian · Kode antrian · Huruf poli · Status antrian · Sumber · Layar panggilan · Panel · Petugas/Admin · Pengaturan · Estimasi. Do not introduce synonyms in interface copy.

**Technical constraints** — Laravel 12 on PHP 8.2+ (**FROM REPO** `composer.json`), Blade server-rendered, Tailwind CSS 4 with Alpine.js 3 through Vite 7, MariaDB 11.4 (MySQL-compatible). Public pages stay under ~200 KB with no heavy external asset build. Mobile-first, verified at 375 px and 1440 px. CSRF on every form, server-side validation, escaped Blade output, bcrypt passwords, login throttled at 5 attempts/minute, inactive accounts cannot sign in.

**Personal data.** The online form collects nama, NIK (16 digits, nullable), no. HP (nullable), jenis kelamin, and a short free-text keluhan. Retention is governed by a confirmed rule: **keep it only as long as the visit needs it, then prune/anonymise** (**interview**) — this is a binding rule for future work, not an optional preference.

**Stated in the PRD but not true in the code yet** — verify before promising any of it:

- "One NIK may hold only one active antrian per day per poli" (anti-dobel). `App\Http\Controllers\Publik\AntrianController::simpan` has no NIK uniqueness check and `nik` is nullable.
- "Antrian closes automatically outside operating hours." The form checks only that the poli has an active jadwal that weekday; operating hours are not consulted.
- The PRD's status words are `lewat`/`batal`; the UI labels render as "Terlewat"/"Dibatalkan".

**Undecided (do not invent)**:

- The exact retention window and who executes the prune. No scheduled command or job exists (`routes/console.php` defines only `inspire`), so retention is currently unimplemented.
- Whether the free-text `keluhan` field counts as health data under the short-retention rule, and what consent or notice text the public form must carry.
- Whether a NIK or phone number is required at all, or whether a weaker anti-duplicate identifier would do.

## Brand Commitments

- **Name**: SILAKES — Sistem Informasi Layanan Kesehatan — for Puskesmas Umban Sari, Pekanbaru.
- **Voice**: Bahasa Indonesia, direct and operational, in the register the puskesmas uses with the public. It states facts, times, and requirements; it never diagnoses, advises treatment, or makes marketing claims.
- **Visual identity**: nothing is binding (**interview**). There is no official logo, no institutional or Kemenkes palette, no enforced typography, and no requirement to match any puskesmas or ministry identity. Real address and contact details exist as content, not as brand assets.
- **No claims may be invented**: no testimonials, certifications, partner logos, patient numbers, or accreditation may be fabricated for this product.

## Evidence on Hand

- `docs/PRD.md` v1.0 (17 Nov 2026) — requirements, roles, features, data rules, non-functional requirements, acceptance criteria.
- `docs/PANDUAN.md`, `GLOSSARY.md`, `README.md`, `docs/adr/0001-stack-dan-antrian-per-poli.md`.
- `docs/screenshots/` — `beranda.png`, `layar-panggilan.png`, `panel-antrian.png`, `tiket-antrian.png` (captured from the running app; may predate current tokens).
- Working application code: 16 controllers, 10 models, 34 Blade views, Tailwind theme, seeders and demo data.
- Real content on hand: service requirement lists, doctor schedules, and puskesmas details, to be entered or imported (**interview**).
- **Absent**: photographs of the puskesmas, user research or interview notes, traffic or reliability statistics, brand guidelines, and any institutional identity assets.

## Product Principles

1. **Information becomes action.** Any page about a service must lead the visitor to the antrian it belongs to; a page that only describes has under-delivered.
2. **Zero friction for warga.** No account, no app install, no form field that is not needed to serve the visit; it must work on a mid-range phone over mobile data.
3. **The puskesmas owns its content.** Petugas change schedules, requirements, and announcements in the panel and they are live immediately; never hardcode what staff are meant to edit.
4. **Say the truth about right now.** Open or closed, which number is being called, how long the wait is — computed from the server clock, never a stale estimate or an optimistic guess.
5. **Non-medical and minimum-data by design.** No diagnosis, no medical record, and no personal detail kept past the visit it was collected for.

## Accessibility & Inclusion

**FROM REPO** `docs/PRD.md` §7, binding:

- Contrast at WCAG AA for text at its actual size; status is never carried by color alone — every state has a text label beside its color.
- Touch targets ≥ 44 px, explicit form labels on every field, keyboard operation throughout.
- The layar panggilan must be legible from across a waiting room: large type, high contrast, readable by people with reduced vision who are not standing at the screen.
- Bahasa Indonesia and Indonesian date formats everywhere, including durations and times.
