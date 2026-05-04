# ZASM INVESTMENT

Enterprise-grade Laravel investment platform supporting investor onboarding, deal/offering management, contract signing, waterfall modeling, distributions, partner operations, and multi-gateway payment processing.

---

## 1) Executive Summary

**ZASM INVESTMENT** is a Laravel 11 platform with two active investment domains:

1. **Deal/Offering investment domain (newer workflow)**
   - Deals, offerings, classes, investors, investment profiles
   - E-sign templates and Documenso-backed contract flows
   - Waterfall configuration and distribution record management
   - Partner portal and partner-level deal access controls

2. **Property investment domain (legacy but operational)**
   - Share-based property investments (`Invest`)
   - Installment plans, recurring profit schedules, cron-driven profit generation
   - Referral commission payout chain for deposit/invest/profit events

This repository also includes a generic **multi-gateway deposit/IPN engine**, ACH onboarding flows via Stripe Connect, and configurable API integrations.

---

## 2) Tech Stack

- **Backend:** Laravel 11, PHP 8.2
- **Frontend:** Blade + Vue 3 + Vite
- **Auth/Security:** Laravel auth + Sanctum + 2FA + KYC middleware + role/permission middleware
- **Storage/Processing:** Eloquent ORM, queue config (database default), cron execution route/controller
- **PDF & Document Tooling:** `barryvdh/laravel-dompdf`, `pdf-lib`, `pdfjs-dist`
- **Core Access Control:** `spatie/laravel-permission`

---

## 3) Business Modules (Code-Verified)

| Module | Purpose | Primary Code Areas |
|---|---|---|
| User onboarding & auth | Registration, login, verification, 2FA, profile completion | `app/Http/Controllers/User/Auth/*`, `AuthorizationController`, `UserController` |
| Deal management | Deal lifecycle, classes, offering linkage, settings, members/partners | `Admin/DealController`, `DealClassController`, `routes/admin.php`, `routes/partner_dashboard.php` |
| Offering management | Public/private visibility, assets/classes mapping, funding info, metrics | `Offering` model, `DealController@storeOffering/*offering*` |
| Investment onboarding (deal/offering) | Investor profiles, questionnaires, wire docs, ACH, investment creation | `User/OfferingDetailController`, `Admin/InvestmentController`, `User/StripeACHController` |
| Contract & e-sign | Template upload, field placement, recipient assignment, document send | `Admin/ESignTemplateController`, `DocumensoService`, Vue Documenso components |
| Waterfall modeling | Nested hurdle tree, split paths, GP/stop-hurdle support, default waterfall selection | `Admin/WaterFallController`, `Admin/DealController` waterfall builders |
| Distribution records | Deal distribution entry, visibility toggles, listing | `Admin/DistributionsController`, `Distribution` model |
| Property investments (legacy) | Installments, profit generation/discharge, contracts | `User/InvestController`, `Admin/InvestController`, `CronController`, `PropertyInvest` |
| Referral program | Multi-level commission matrix and payout transactions | `Admin/ReferralController`, `PropertyInvest::referralCommission`, `Referral` model |
| Partner operations | Partner onboarding, profile, deal access and management | `routes/partner_dashboard.php`, `routes/partner_management.php`, `PartnerController` |
| Payments/IPN | Deposit gateways, callbacks/webhooks, manual/automatic methods | `routes/ipn.php`, `Gateway/*/ProcessController`, `Gateway/PaymentController` |

---

## 4) Domain Relationships (Deals ↔ Offerings ↔ Investments)

### Core Eloquent relationships

- `Deal` has many `DealClass`, `Offering`, `Investment`, `WaterFall`, `Distribution`, `Document`, `Member`
- `Offering` belongs to `Deal`, belongs-to-many `DealClass`, has many `Investment`, has many `ESignTemplate`
- `Investment` belongs to `Deal`, `Offering`, `Investor`, `InvestorProfile`, `DealClass`

### Simplified relationship graph

```text
Deal
 ├─ DealClass (LP/GP/etc + hurdle parameters)
 ├─ Offering
 │   ├─ assets/media
 │   ├─ funding_info / manageoffering / key_metrics
 │   ├─ eSignTemplates
 │   └─ investments
 ├─ WaterFalls
 │   └─ WaterFallHurdles (tree, split paths, stop conditions, gp provisions)
 ├─ Distributions
 └─ Partners/Members

Investor/User
 ├─ InvestorProfile(s)
 ├─ Investment(s)
 └─ ESignTemplateRecipient(s)
```

---

## 5) Complete System Flow

## 5.1 Investment Flow

### A. User registration and verification

1. User signs up via `user/register`
2. Referral linkage is captured through `ref_by` if referral session exists
3. Email/mobile verification and optional 2FA are enforced (`AuthorizationController`)
4. Profile completion + optional KYC flow (`UserController@userData`, `kyc*`)

### B. Investment creation (Deal/Offering path)

1. User opens offering detail/invest pages
2. User submits investor profile, questionnaire(s), address/W-9 style form
3. User submits investment amount + funding method
4. Investment record is created under the offering (`offering_id`, `deal_id`, profile/class linkage)
5. Funding method maps to contribution methods (`wire_transfer`, `ach_payment`, `check_payment`)

### C. Contract start and signing

When admin creates/updates investment with status `document_started`:

1. System finds matching e-sign template by offering + profile type
2. Documenso recipients are created (supports multi-recipient flows like joint tenancy)
3. Saved template fields are mapped and pushed to Documenso
4. Document is distributed via Documenso send API
5. Recipient token(s) are stored; user frontend loads signing token for embedded signing session

### D. Return/profit logic in codebase

The repository has two return engines:

- **Deal/Offering domain:** investment/distribution records + waterfall definitions (calculation orchestration is primarily structural/configurational in this code)
- **Property legacy domain:** actual profit amount math + payout transactions via `PropertyInvest` and cron/admin discharge

---

## 5.2 Deals & Offerings System

### Deal lifecycle

- Deal creation captures sponsor/entity context and initializes default document sections
- Deals include classes, buckets, assets, offerings, members, and partner assignments
- Partner access is filtered by partner-deal mapping/middleware

### Offering lifecycle

- Offering is created under a deal with UUID
- Assets and classes are attached (many-to-many)
- Key metric defaults are seeded
- Funding info, insights, and manage-offering preferences are maintained
- Public preview exists when offering is flagged for public visibility

### Relationship behavior

- Deals can host multiple offerings
- Offerings can map to multiple classes
- Investments are persisted against both deal and offering for end-to-end traceability

---

## 5.3 Waterfall Distribution Logic (Step-by-Step)

Waterfall logic is implemented in two layers:

1. **Template/build layer (`DealController`)**
   - Builds a “Basic Waterfall” from class/bucket hurdle inputs
   - Handles single-path (100% share) and split-path (multi-share) strategies
   - Generates split hurdles, class allocations, and optional stop-hurdle conditions

2. **CRUD tree layer (`WaterFallController`)**
   - Accepts nested hurdle structures
   - Stores hurdles recursively with:
     - `parent_id`
     - `path` (branch identifier)
     - `sort_order`
   - Persists optional `stop_hurdle` and `gp_provision` nodes per hurdle

### Priority/hierarchy model

- Hurdles are ordered using `sort_order`
- Split hurdles branch into explicit paths
- Child hurdles inherit branch context via `path`
- Optional stop conditions can terminate or cap progression

---

## 5.4 Distribution System

### Trigger points (verified)

- Admin/partner endpoints allow creation/listing/updating/deleting distribution records per deal
- Separate distribution listing controller supports visibility toggling (`is_visible`)

### Stored distribution attributes include

- source, date range, distribution date
- waterfall reference fields
- compounding/calculation metadata
- amount/memo/visibility markers

### Execution note

- The codebase clearly supports **distribution record management** and waterfall configuration.
- Direct bank payout orchestration for deal distributions is not centrally implemented in a single payout engine in this repo (payout-style logic is explicit in the legacy property flow and referral payout transactions).

---

## 5.5 Partner / Referral Program

## A) Partner program

- Dedicated partner auth and dashboard routes
- Partner role-based deal visibility/control
- Partner management module for assigning/removing deals
- Deal member invitation and role linking via pivot (`partner_deals`)

## B) Referral program

- Multi-level referral matrix configured by commission type:
  - `deposit_commission`
  - `invest_commission`
  - `profit_commission`
- Commission entries are stored by level and percent
- Runtime payouts:
  - Walk up referral chain (`ref_by`)
  - Credit wallet balance
  - Create transaction records and notifications

---

## 5.6 Third-Party Integrations (Critical)

Only verified integrations from code are listed below.

| Integration | Why it is used | Where it is used | Workflow impact |
|---|---|---|---|
| **Stripe (core + ACH + Connect)** | ACH bank linking, customer/account creation, onboarding links, bank verification, transfers | `StripeACHService`, `User/Admin StripeACHController`, `StripeWebhookController`, gateway Stripe IPN controllers | Investor/deal bank setup, ACH micro-deposits, connected-account onboarding, investor charge + account transfer flows |
| **Documenso** | Digital document lifecycle for e-sign templates and recipient signing | `DocumensoService`, `ESignTemplateController`, `InvestmentController`, Vue Documenso components | Contract document creation, field mapping, recipient assignment, distribution/send, tokenized signing |
| **Plaid** | Bank account linking/token exchange + Stripe bank account tokenization | `PlaidService`, `Admin/PlaidController`, `resources/js/components/fundingInfo/plaid.vue` | Admin-side bank linking and Stripe payment initiation helper path |
| **Payment gateways (multi-provider IPN)** | Deposits via many third-party processors | `routes/ipn.php`, `Gateway/*/ProcessController` | Unified deposit confirmation path and callback processing across providers |
| **SendGrid / Mailjet / SMTP / PHP mail** | Transactional and system emails | `Notify/Email.php`, admin notification settings | Configurable outbound email delivery by provider |
| **Twilio / Vonage (Nexmo) / MessageBird / others** | SMS notifications and verification messaging | `Notify/SmsGateway.php`, notification admin settings | Configurable SMS delivery channels |
| **Social login providers (via Socialite)** | User/partner social auth | Socialite controllers and auth routes | Alternative sign-in methods |
| **BTCPay / Coinbase Commerce / Coingate / Mollie / Razorpay / etc.** | Gateway-specific payment callbacks | Gateway process controllers + IPN routes | Payment confirmation and settlement state transitions |

### Webhook notes

- `StripeWebhookController` handles `customer.source.verified` updates for ACH verification state.
- `WebhookController` includes Documenso-style webhook processing logic and CSRF exception support (`/webhooks/documenso`), but public route registration in `routes/web.php` is currently commented out.

---

## 5.7 Investor Lifecycle (End-to-End)

```text
Signup
  -> Email/SMS/2FA authorization gates
  -> Profile completion and optional KYC
  -> Offering selection
  -> Investor profile + questionnaire + address capture
  -> Investment creation
  -> Contract template matching (by profile type)
  -> Documenso recipient + field assignment
  -> Document signing (embedded token flow)
  -> Funding (wire / ACH / check / gateway path)
  -> Earnings/profit accrual paths (domain-dependent)
  -> Distribution visibility and transaction history
  -> Withdrawal flow (wallet-based withdrawal module)
```

---

## 5.8 System Architecture Overview

## Application layering

- **Routes:** separated by context (`admin`, `user`, `partner`, `ipn`, `web`)
- **Controllers:** domain-centric orchestration for deals, investments, onboarding, gateways
- **Models:** rich relationship graph and casting (money/percentage/json)
- **Services:** external API wrappers (`DocumensoService`, `StripeACHService`, `PlaidService`)
- **Lib layer:** financial operations (`PropertyInvest`) and helpers
- **Middleware:** auth, role/permission, status/KYC/registration state, partner deal access

## Queue, jobs, and schedulers

- Queue backend defaults to `database` (`config/queue.php`)
- `app/Jobs` currently exists but is empty
- Scheduled/periodic behavior is primarily driven by `CronController` and cron-job tables, triggered by route (`/cron`) and cron config records

## Event-driven aspects

- Laravel auth registration events are used (`Registered` event)
- Webhook handlers exist for Stripe and Documenso-style payloads

---

## 6) Installation & Local Setup

## Prerequisites

- PHP 8.2+
- Composer
- Node.js + npm
- Database (MySQL/PostgreSQL/SQLite)

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

Open the application at the configured app URL (default `http://localhost:8000`).

---

## 7) Configuration Checklist

## Core

- `APP_URL`, DB credentials, cache/session/queue drivers

## Stripe ACH / Connect

- Seeded gateway code `114` stores `secret_key` and `publishable_key`
- Configure via gateway/admin settings or env-backed seeder values

## Documenso

- `api_integrations` entry with code `documenso`
- Set `api_url`, `api_key`, and enable status in API integrations admin section

## Plaid

- `PLAID_CLIENT_ID`, `PLAID_SECRET`, `PLAID_BASE_URL`

## Email/SMS

- Configure provider in notification settings (SMTP/SendGrid/Mailjet and chosen SMS provider)

## Gateway/IPN

- Configure gateway credentials and callback URLs for enabled payment methods

---

## 8) Operations & Risk Notes

- This platform handles investment and payment-adjacent workflows; secure environment management and strict credential control are mandatory.
- Validate production webhook exposure and signatures for Stripe and Documenso.
- For deal distribution cash movement, verify whether downstream payout automation is implemented in your deployment layer or handled operationally.
- Ensure logging/monitoring, backup, and access-control policies match your compliance requirements.

---

## 9) Developer Notes

- Use route files as bounded contexts:
  - `routes/admin.php`
  - `routes/user.php`
  - `routes/partner_dashboard.php`
  - `routes/ipn.php`
- Core deal/offering orchestration is centered in `Admin/DealController`.
- E-sign behavior depends on both database templates/fields and Documenso API availability.
- Referral payout behavior is centralized in `PropertyInvest::referralCommission`.

---

## Contact

Mateen Zahid  
Email: mateenzahid1598@gmail.com
