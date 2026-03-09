# 📊 FLOWCHART SISTEM SMART COPY SMK

## 🎯 Overview Sistem

Smart Copy SMK adalah platform marketplace copywriting berbasis AI yang menghubungkan client (UMKM) dengan operator (siswa SMK), didukung teknologi Google Gemini AI.

**Last Updated:** 9 Maret 2026  
**Tech Stack:** Laravel 11 + Google Gemini 2.5 Flash + Tailwind CSS

---

## 👥 USER ROLES & ACCESS

```
┌─────────────────────────────────────────────────────────────┐
│                    SMART COPY SMK USERS                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  CLIENT  │  │ OPERATOR │  │   GURU   │  │  ADMIN   │   │
│  │  (UMKM)  │  │(Siswa SMK)│  │(Trainer) │  │(Manager) │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
│       │              │              │              │         │
│       ▼              ▼              ▼              ▼         │
│  AI Generator   Order Queue   ML Training   User Mgmt       │
│  Analytics      Workspace     Analytics     Reports         │
│  Brand Voice    Earnings      Review Data   Payments        │
│  Orders         Withdrawal    Approve/Reject Withdrawals    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 MAIN SYSTEM FLOWS

### 1. AI GENERATOR FLOW (Client)

```
┌─────────────────────────────────────────────────────────────────┐
│                    AI GENERATOR WORKFLOW                         │
└─────────────────────────────────────────────────────────────────┘

START
  │
  ├─► [1] Client Login
  │     │
  │     ├─► Check First Time User?
  │     │     ├─ YES → Show Tutorial (Mode Simpel)
  │     │     └─ NO  → Show Dashboard
  │     │
  │     └─► Navigate to AI Generator
  │
  ├─► [2] Choose Mode
  │     │
  │     ├─► MODE SIMPEL (Pemula)
  │     │     │
  │     │     ├─ Input 6 Pertanyaan:
  │     │     │   1. Jenis Usaha (12 pilihan)
  │     │     │   2. Nama Produk
  │     │     │   3. Harga (optional)
  │     │     │   4. Target Market (5 pilihan)
  │     │     │   5. Tujuan (4 pilihan)
  │     │     │   6. Platform (4 pilihan)
  │     │     │
  │     │     ├─ Generate Pertama?
  │     │     │   ├─ YES → 5 Variasi GRATIS 🎉
  │     │     │   └─ NO  → 1 Caption Terbaik
  │     │     │
  │     │     └─► Go to [3] AI Processing
  │     │
  │     └─► MODE ADVANCED (Professional)
  │           │
  │           ├─ Input Detail:
  │           │   • Kategori (15+ pilihan)
  │           │   • Subcategory (200+ pilihan)
  │           │   • Platform (50+ pilihan)
  │           │   • Brief/Deskripsi
  │           │   • Tone (6 pilihan)
  │           │   • Keywords (optional)
  │           │   • Generate Variations (1/5/10/15/20)
  │           │   • Auto Hashtag (on/off)
  │           │   • Bahasa Daerah (optional)
  │           │
  │           ├─ Load Brand Voice? (optional)
  │           │   ├─ YES → Auto-fill preferences
  │           │   └─ NO  → Manual input
  │           │
  │           └─► Go to [3] AI Processing
  │
  ├─► [3] AI Processing (Backend)
  │     │
  │     ├─► ModelFallbackManager
  │     │     │
  │     │     ├─ Detect Tier (Auto)
  │     │     │   ├─ Free Tier: 5-15 RPM
  │     │     │   └─ Tier 1 (Paid): 150-300 RPM
  │     │     │
  │     │     ├─ Get Best Available Model
  │     │     │   Priority:
  │     │     │   1. gemini-2.5-flash
  │     │     │   2. gemini-2.5-flash-lite
  │     │     │   3. gemini-3-flash-preview
  │     │     │   4. gemini-2.5-pro
  │     │     │   5. gemini-2.0-flash
  │     │     │
  │     │     └─ Check Rate Limits
  │     │         ├─ Available → Use Model
  │     │         └─ Exhausted → Fallback to Next
  │     │
  │     ├─► TemplatePrompts
  │     │     │
  │     │     ├─ Get Template for Subcategory
  │     │     │   • Task description
  │     │     │   • Format guidelines
  │     │     │   • Criteria
  │     │     │   • Tips
  │     │     │
  │     │     └─ Build Specialized Prompt
  │     │
  │     ├─► GeminiService
  │     │     │
  │     │     ├─ Send Request to Gemini API
  │     │     │   • Model: Selected by Fallback Manager
  │     │     │   • Temperature: 0.7
  │     │     │   • Max Tokens: 4096-8192
  │     │     │
  │     │     ├─ Handle Response
  │     │     │   ├─ Success → Parse Output
  │     │     │   └─ Error → Retry with Fallback
  │     │     │
  │     │     └─ Track Usage
  │     │         • RPM (Requests Per Minute)
  │     │         • RPD (Requests Per Day)
  │     │         • TPM (Tokens Per Minute)
  │     │
  │     ├─► OutputValidator
  │     │     │
  │     │     ├─ Validate Quality
  │     │     │   • Minimum length (15+ words)
  │     │     │   • Hashtag presence
  │     │     │   • CTA effectiveness
  │     │     │   • Emoji usage
  │     │     │   • Platform requirements
  │     │     │   • Spam patterns
  │     │     │
  │     │     ├─ Calculate Score (0-10)
  │     │     │
  │     │     └─ Should Retry?
  │     │         ├─ Score < 6.0 → Retry (max 2x)
  │     │         └─ Score ≥ 6.0 → Accept
  │     │
  │     └─► QualityScorer
  │           │
  │           ├─ Score Dimensions:
  │           │   • Hook Quality (20%)
  │           │   • Engagement Potential (20%)
  │           │   • CTA Effectiveness (15%)
  │           │   • Tone Match (15%)
  │           │   • Platform Optimization (10%)
  │           │   • Readability (10%)
  │           │   • Uniqueness (10%)
  │           │
  │           ├─ Calculate Total Score
  │           │
  │           └─ Generate Recommendation
  │
  ├─► [4] Output Display
  │     │
  │     ├─ Show Generated Caption(s)
  │     │   • 1-20 variasi (based on selection)
  │     │   • Quality score per caption
  │     │   • Grade (A+, A, B, C, D)
  │     │
  │     ├─ Actions Available:
  │     │   • Copy to Clipboard
  │     │   • Save for Analytics
  │     │   • Rate Caption (1-5 stars)
  │     │   • Regenerate
  │     │   • Save to Brand Voice
  │     │
  │     └─ Save to Caption History
  │
  └─► [5] Post-Generation
        │
        ├─► Save to Database
        │     • caption_history table
        │     • User preferences
        │     • Generation parameters
        │
        ├─► Track Analytics (optional)
        │     • Platform
        │     • Likes, Comments, Shares
        │     • Engagement Rate
        │
        └─► ML Training Data (if rated)
              • High-rated captions → Training data
              • Guru review → Approve/Reject
              • Model improvement

END
```

---

### 2. MARKETPLACE FLOW (Client → Operator)

```
┌───────────────────────────────────────────────────────────trends
        │
        ├─ Category Insights:
        │   • Best performing categories
        │   • Most used templates
        │   • Platform preferences
        │   • Tone effectiveness
        │
        └─ Recommendations:
            • Prompt improvements
            • Template updates
            • New category suggestions
            • Quality threshold adjustments

END
```

---

### 4. BRAND VOICE FLOW

```
versions table:
  │     │   • Version number
  │     │   • Training date
  │     │   • Dataset size
  │     │   • Performance metrics
  │     │   • Notes
  │     │
  │     └─ Version Comparison:
  │         • Accuracy improvement
  │         • Engagement rate increase
  │         • User satisfaction
  │
  └─► [5] Analytics & Insights
        │
        ├─ Model Performance:
        │   • Total training data
        │   • Approved vs Rejected ratio
        │   • Average quality score
        │   • Engagement rate a table:
  │     │   • Input parameters
  │     │   • Output caption
  │     │   • Performance metrics
  │     │   • Quality score
  │     │   • Guru approval
  │     │
  │     └─ Dataset Structure:
  │         • Category
  │         • Subcategory
  │         • Platform
  │         • Tone
  │         • Target audience
  │         • Brief/description
  │         • Generated output
  │         • Engagement metrics
  │         • Quality score
  │
  ├─► [4] Model Versioning
  │     │
  │     ├─ Track in ml_model_
  │     │   • Performance metrics
  │     │   • Client feedback
  │     │
  │     ├─ Review Criteria:
  │     │   • Quality (1-10)
  │     │   • Relevance to brief
  │     │   • Tone appropriateness
  │     │   • Platform optimization
  │     │   • Engagement potential
  │     │
  │     └─ Decision:
  │         ├─ APPROVE → Add to training dataset
  │         ├─ REJECT → Remove from pool
  │         └─ REVISE → Request improvements
  │
  ├─► [3] Training Dataset
  │     │
  │     ├─ Store in ml_training_datrming captions → Training pool
  │     │       Criteria:
  │     │       • Engagement Rate > 5%
  │     │       • Client rating ≥ 4 stars
  │     │
  │     └─ Source 2: Operator Work
  │         │
  │         ├─ Completed orders
  │         │   • Client approved
  │         │   • High rating (≥ 4 stars)
  │         │
  │         └─ Quality copywriting → Training pool
  │
  ├─► [2] Guru Review Interface
  │     │
  │     ├─ View Pending Training Data
  │     │   • Caption/content
  │     │   • Generation parametersW                          │
└─────────────────────────────────────────────────────────────────┘

START
  │
  ├─► [1] Data Collection
  │     │
  │     ├─ Source 1: Caption Analytics
  │     │   │
  │     │   ├─ Client saves caption for tracking
  │     │   │   • Platform
  │     │   │   • Likes, Comments, Shares
  │     │   │   • Reach, Impressions
  │     │   │
  │     │   ├─ Auto-calculate Engagement Rate
  │     │   │   Formula: (Likes + Comments + Shares) / Reach × 100
  │     │   │
  │     │   └─ High-perfoe Disputes
  │     │
  │     └─ Ensure Quality
  │
  └─► [3] Withdrawal Approval
        │
        ├─ Review Withdrawal Request
        │   • Operator balance
        │   • Bank account
        │   • Amount
        │
        ├─ Decision:
        │   ├─ Approve → Process transfer
        │   └─ Reject → Notify operator
        │
        └─ Mark as Completed

END
```

---

### 3. ML TRAINING FLOW (Guru)

```
┌─────────────────────────────────────────────────────────────────┐
│                    ML TRAINING WORKFLO  • Amount
        │
        └─ Wait Admin Approval
            ├─ Approved → Transfer processed
            └─ Rejected → Reason provided

ADMIN SIDE:
  │
  ├─► [1] Payment Verification
  │     │
  │     ├─ Review Payment Proof
  │     │   • Screenshot
  │     │   • Amount match
  │     │   • Transaction ID
  │     │
  │     └─ Decision:
  │         ├─ Verify → Order activated
  │         └─ Reject → Notify client
  │
  ├─► [2] Order Monitoring
  │     │
  │     ├─ View All Orders
  │     │
  │     ├─ Handl as completed
  │     │
  │     └─ Order Status: COMPLETED
  │
  ├─► [3] Revision (if requested)
  │     │
  │     ├─ View Client Feedback
  │     │
  │     ├─ Make Changes
  │     │
  │     └─ Resubmit
  │
  └─► [4] Earnings & Withdrawal
        │
        ├─ Track Earnings:
        │   • Completed orders
        │   • Total earnings
        │   • Pending balance
        │   • Available balance
        │
        ├─ Request Withdrawal:
        │   • Minimum: Rp 50,000
        │   • Bank account info
        │   │         └─ Reject Order → Back to Queue
  │
  ├─► [2] Workspace (AI-Assisted)
  │     │
  │     ├─ View Order Details
  │     │   • Client brief
  │     │   • Requirements
  │     │   • Deadline
  │     │   • Reference files
  │     │
  │     ├─ Work on Order:
  │     │   • Use AI Generator (if needed)
  │     │   • Manual copywriting
  │     │   • Upload drafts
  │     │   • Request clarification
  │     │
  │     ├─ Submit Work
  │     │   • Upload final result
  │     │   • Add notes
  │     │   • Mark │     └─ Decision:
  │         ├─ Accept Order → Go to [2]
 files
        │   • Request revision
        │
        └─ Final Actions:
            • Download result
            • Rate operator (1-5 stars)
            • Write review
            • Request revision (if needed)

OPERATOR SIDE:
  │
  ├─► [1] Order Queue
  │     │
  │     ├─ View Available Orders
  │     │   • Category
  │     │   • Budget
  │     │   • Deadline
  │     │   • Client rating
  │     │
  │     ├─ Filter Orders:
  │     │   • By category
  │     │   • By budget
  │     │   • By deadline
  │     │
 Admin Verification
  │
  └─► [4] Order Tracking
        │
        ├─ Monitor Status:
        │   • Pending → Waiting operator
        │   • Accepted → Operator assigned
        │   • In Progress → Being worked on
        │   • Revision → Changes requested
        │   • Completed → Ready for review
        │   • Approved → Order finished
        │
        ├─ Communication:
        │   • Chat with operator
        │   • Upload reference • Category
  │     │   • Brief/Description
  │     │   • Deadline
  │     │   • Budget
  │     │   • Choose Operator (optional)
  │     │
  │     ├─ Submit Request
  │     │
  │     └─ Order Status: PENDING
  │
  ├─► [3] Payment
  │     │
  │     ├─ Choose Payment Method:
  │     │   • Manual Transfer (primary)
  │     │   • Midtrans (optional)
  │     │
  │     ├─ Upload Payment Proof
  │     │   • Screenshot transfer
  │     │   • Transaction ID
  │     │   • Notes
  │     │
  │     └─ Wait    │   • Price Range
  │     │
  │     └─ Filter & Search
  │         • By category
  │         • By rating
  │         • By price
  │
  ├─► [2] Request Order
  │     │
  │     ├─ Fill Order Form:
  │     │   • Select Package
  │     │  ───────┘

CLIENT SIDE:
  │
  ├─► [1] Browse Operators
  │     │
  │     ├─ View Operator Profiles
  │     │   • Portfolio
  │     │   • Rating & Reviews
  │     │   • Specialization
  │  WORKFLOW                          │
└────────────────────────────────────────────────────────────────┐
│                    MARKETPLACE 
┌─────────────────────────────────────────────────────────────────┐
│                    BRAND VOICE WORKFLOW                          │
└─────────────────────────────────────────────────────────────────┘

START
  │
  ├─► [1] Create Brand Voice
  │     │
  │     ├─ Input Brand Info:
  │     │   • Brand name
  │     │   • Brand description
  │     │   • Industry/niche
  │     │   • Target audience
  │     │   • Tone preference
  │     │   • Keywords favorit
  │     │   • Platform favorit
  │     │   • Bahasa daerah (optional)
  │     │
  │     ├─ Set as Default? (optional)
  │     │   ├─ YES → Auto-load on generate
  │     │   └─ NO  → Manual load
  │     │
  │     └─ Save to Database
  │         • brand_voices table
  │         • User ID
  │         • Preferences JSON
  │
  ├─► [2] Load Brand Voice
  │     │
  │     ├─ On AI Generator Page:
  │     │   │
  │     │   ├─ Default Brand Voice?
  │     │   │   ├─ YES → Auto-fill form
  │     │   │   └─ NO  → Show dropdown
  │     │   │
  │     │   └─ Select from Dropdown
  │     │       • List all saved brand voices
  │     │       • Quick preview
  │     │       • Load with 1 click
  │     │
  │     └─ Auto-fill Form Fields:
  │         • Industry → Category
  │         • Target audience → Auto-detect
  │         • Tone → Pre-select
  │         • Keywords → Pre-fill
  │         • Platform → Pre-select
  │         • Bahasa daerah → Pre-select
  │
  ├─► [3] Manage Brand Voices
  │     │
  │     ├─ View All Brand Voices
  │     │   • List view
  │     │   • Card view
  │     │   • Search & filter
  │     │
  │     ├─ Actions:
  │     │   • Edit brand voice
  │     │   • Delete brand voice
  │     │   • Set as default
  │     │   • Duplicate
  │     │
  │     └─ Use Cases:
  │         • Agency: Multiple clients
  │         • Business: Multiple products
  │         • Freelancer: Multiple brands
  │
  └─► [4] Benefits
        │
        ├─ Time Saving:
        │   • No need to re-enter preferences
        │   • Quick switch between brands
        │   • Consistent output
        │
        ├─ Consistency:
        │   • Same tone across campaigns
        │   • Brand voice maintained
        │   • Quality assurance
        │
        └─ Efficiency:
            • Faster generation
            • Better results
            • Less manual work

END
```

---

## 🔐 AUTHENTICATION & AUTHORIZATION FLOW

```
┌─────────────────────────────────────────────────────────────────┐
│                    AUTH & AUTHORIZATION                          │
└─────────────────────────────────────────────────────────────────┘

REGISTRATION:
  │
  ├─► [1] User Registration
  │     │
  │     ├─ Input:
  │     │   • Name
  │     │   • Email
  │     │   • Password
  │     │   • Role (Client/Operator/Guru)
  │     │
  │     ├─ Validation:
  │     │   • Email unique
  │     │   • Password min 8 chars
  │     │   • Role valid
  │     │
  │     └─ Create Account
  │         • Hash password (bcrypt)
  │         • Generate verification token
  │         • Send verification email
  │
  ├─► [2] Email Verification
  │     │
  │     ├─ User clicks verification link
  │     │
  │     ├─ Verify token
  │     │   ├─ Valid → Mark email as verified
  │     │   └─ Invalid → Show error
  │     │
  │     └─ Redirect to Dashboard
  │
  └─► [3] Login
        │
        ├─ Input:
        │   • Email
        │   • Password
        │
        ├─ Validation:
        │   • Credentials match
        │   • Email verified
        │   • Account active
        │
        ├─ Create Session
        │
        └─ Redirect by Role:
            ├─ Client → /dashboard (client view)
            ├─ Operator → /dashboard (operator view)
            ├─ Guru → /dashboard (guru view)
            └─ Admin → /dashboard (admin view)

AUTHORIZATION:
  │
  ├─► Middleware: role:client
  │     │
  │     ├─ Check user role
  │     │   ├─ Client → Allow access
  │     │   └─ Other → Redirect to dashboard
  │     │
  │     └─ Protected Routes:
  │         • /ai-generator
  │         • /analytics
  │         • /brand-voices
  │         • /orders
  │         • /feedback
  │
  ├─► Middleware: role:operator
  │     │
  │     ├─ Check user role
  │     │   ├─ Operator → Allow access
  │     │   └─ Other → Redirect to dashboard
  │     │
  │     └─ Protected Routes:
  │         • /operator/queue
  │         • /operator/workspace
  │         • /operator/earnings
  │         • /operator/withdrawal
  │
  ├─► Middleware: role:guru
  │     │
  │     ├─ Check user role
  │     │   ├─ Guru → Allow access
  │     │   └─ Other → Redirect to dashboard
  │     │
  │     └─ Protected Routes:
  │         • /guru/training
  │         • /guru/analytics
  │         • /guru/training-history
  │
  └─► Middleware: role:admin
        │
        ├─ Check user role
        │   ├─ Admin → Allow access
        │   └─ Other → Redirect to dashboard
        │
        └─ Protected Routes:
            • /admin/users
            • /admin/packages
            • /admin/payments
            • /admin/withdrawals
            • /admin/feedback
            • /admin/reports
            • /admin/ai-usage
            • /admin/ml-analytics

SECURITY:
  │
  ├─► CSRF Protection
  │     • All POST/PUT/DELETE requests
  │     • Token validation
  │
  ├─► SQL Injection Prevention
  │     • Eloquent ORM
  │     • Prepared statements
  │
  ├─► XSS Protection
  │     • Blade templating
  │     • Auto-escaping
  │
  ├─► Password Security
  │     • Bcrypt hashing
  │     • Min 8 characters
  │     • No plain text storage
  │
  └─► API Key Security
        • .env file
        • Not in version control
        • Server-side only

END
```

---

## 📊 DATABASE SCHEMA FLOW

```
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE RELATIONSHIPS                        │
└─────────────────────────────────────────────────────────────────┘

CORE TABLES:

users
  ├─► id (PK)
  ├─► name
  ├─► email (unique)
  ├─► password (hashed)
  ├─► role (client/operator/guru/admin)
  ├─► email_verified_at
  └─► timestamps

  Relationships:
  ├─ hasMany → orders (as client)
  ├─ hasMany → orders (as operator)
  ├─ hasMany → brand_voices
  ├─ hasMany → caption_history
  ├─ hasMany → caption_analytics
  ├─ hasMany → ml_training_data (as reviewer)
  ├─ hasMany → notifications
  ├─ hasMany → feedback
  └─ hasOne → operator_profile

packages
  ├─► id (PK)
  ├─► name
  ├─► description
  ├─► price
  ├─► features (JSON)
  ├─► is_active
  └─► timestamps

  Relationships:
  └─ hasMany → orders

orders
  ├─► id (PK)
  ├─► client_id (FK → users)
  ├─► operator_id (FK → users, nullable)
  ├─► package_id (FK → packages)
  ├─► status (pending/accepted/in_progress/revision/completed/approved/cancelled)
  ├─► brief (text)
  ├─► deadline
  ├─► budget
  ├─► result (text, nullable)
  ├─► client_rating (1-5, nullable)
  ├─► client_review (text, nullable)
  └─► timestamps

  Relationships:
  ├─ belongsTo → users (client)
  ├─ belongsTo → users (operator)
  ├─ belongsTo → packages
  ├─ hasOne → payment
  └─ hasMany → order_revisions

AI & ANALYTICS TABLES:

caption_history
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► category
  ├─► subcategory
  ├─► platform
  ├─► brief (text)
  ├─► tone
  ├─► keywords
  ├─► generated_caption (text)
  ├─► quality_score (0-10)
  ├─► grade (A+/A/B/C/D)
  ├─► user_rating (1-5, nullable)
  ├─► model_used
  └─► timestamps

  Relationships:
  ├─ belongsTo → users
  └─ hasOne → caption_analytics

caption_analytics
  ├─► id (PK)
  ├─► caption_history_id (FK → caption_history)
  ├─► user_id (FK → users)
  ├─► platform
  ├─► likes (integer)
  ├─► comments (integer)
  ├─► shares (integer)
  ├─► reach (integer)
  ├─► impressions (integer)
  ├─► clicks (integer, nullable)
  ├─► engagement_rate (calculated)
  └─► timestamps

  Relationships:
  ├─ belongsTo → caption_history
  └─ belongsTo → users

brand_voices
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► name
  ├─► description
  ├─► industry
  ├─► target_audience
  ├─► tone
  ├─► keywords (JSON)
  ├─► platform
  ├─► local_language (nullable)
  ├─► is_default (boolean)
  └─► timestamps

  Relationships:
  └─ belongsTo → users

ml_training_data
  ├─► id (PK)
  ├─► caption_history_id (FK → caption_history, nullable)
  ├─► order_id (FK → orders, nullable)
  ├─► input_params (JSON)
  ├─► output_caption (text)
  ├─► performance_metrics (JSON)
  ├─► quality_score (0-10)
  ├─► guru_approved (boolean)
  ├─► reviewed_by (FK → users, nullable)
  ├─► review_notes (text, nullable)
  └─► timestamps

  Relationships:
  ├─ belongsTo → caption_history
  ├─ belongsTo → orders
  └─ belongsTo → users (reviewer)

ml_model_versions
  ├─► id (PK)
  ├─► version
  ├─► training_date
  ├─► dataset_size
  ├─► performance_metrics (JSON)
  ├─► notes (text)
  └─► timestamps

FINANCIAL TABLES:

payments
  ├─► id (PK)
  ├─► order_id (FK → orders)
  ├─► user_id (FK → users)
  ├─► amount
  ├─► payment_method (manual_transfer/midtrans)
  ├─► payment_proof (file path, nullable)
  ├─► transaction_id (nullable)
  ├─► status (pending/verified/rejected)
  ├─► verified_by (FK → users, nullable)
  ├─► verified_at (nullable)
  ├─► notes (text, nullable)
  └─► timestamps

  Relationships:
  ├─ belongsTo → orders
  ├─ belongsTo → users
  └─ belongsTo → users (verifier)

payment_settings
  ├─► id (PK)
  ├─► payment_method (manual_transfer/midtrans)
  ├─► bank_name (nullable)
  ├─► account_number (nullable)
  ├─► account_name (nullable)
  ├─► midtrans_server_key (nullable)
  ├─► midtrans_client_key (nullable)
  ├─► is_active (boolean)
  └─► timestamps

withdrawal_requests
  ├─► id (PK)
  ├─► operator_id (FK → users)
  ├─► amount
  ├─► bank_name
  ├─► account_number
  ├─► account_name
  ├─► status (pending/approved/rejected/completed)
  ├─► processed_by (FK → users, nullable)
  ├─► processed_at (nullable)
  ├─► notes (text, nullable)
  └─► timestamps

  Relationships:
  ├─ belongsTo → users (operator)
  └─ belongsTo → users (processor)

operator_profiles
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► bio (text)
  ├─► specialization (JSON)
  ├─► portfolio (JSON)
  ├─► hourly_rate
  ├─► available (boolean)
  ├─► total_earnings
  ├─► pending_balance
  ├─► available_balance
  └─► timestamps

  Relationships:
  └─ belongsTo → users

SYSTEM TABLES:

notifications
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► type
  ├─► title
  ├─► message (text)
  ├─► data (JSON, nullable)
  ├─► read_at (nullable)
  └─► timestamps

  Relationships:
  └─ belongsTo → users

feedback
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► type (bug/feature/improvement/question)
  ├─► title
  ├─► description (text)
  ├─► screenshot (file path, nullable)
  ├─► page_url
  ├─► browser_info (JSON)
  ├─► status (open/in_progress/resolved/closed)
  ├─► priority (low/medium/high/critical)
  ├─► admin_response (text, nullable)
  ├─► responded_by (FK → users, nullable)
  ├─► responded_at (nullable)
  └─► timestamps

  Relationships:
  ├─ belongsTo → users
  └─ belongsTo → users (responder)

order_revisions
  ├─► id (PK)
  ├─► order_id (FK → orders)
  ├─► revision_number
  ├─► requested_by (FK → users)
  ├─► reason (text)
  ├─► status (pending/completed)
  └─► timestamps

  Relationships:
  ├─ belongsTo → orders
  └─ belongsTo → users

projects
  ├─► id (PK)
  ├─► user_id (FK → users)
  ├─► name
  ├─► description (text)
  ├─► status (active/completed/archived)
  └─► timestamps

  Relationships:
  └─ belongsTo → users

copywriting_requests (Legacy)
  ├─► id (PK)
  ├─► order_id (FK → orders)
  ├─► operator_id (FK → users, nullable)
  ├─► content_type
  ├─► brief (text)
  ├─► result (text, nullable)
  ├─► status
  └─► timestamps

  Relationships:
  ├─ belongsTo → orders
  └─ belongsTo → users (operator)

END
```

---

## 🎨 FRONTEND ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND STRUCTURE                            │
└─────────────────────────────────────────────────────────────────┘

TECH STACK:
  ├─► Blade Templates (Laravel)
  ├─► Tailwind CSS (CDN)
  ├─► Alpine.js (Reactive components)
  ├─► Chart.js (Analytics charts)
  └─► Heroicons (SVG icons)

LAYOUT STRUCTURE:

app.blade.php (Main Layout)
  │
  ├─► Header
  │     ├─ Logo
  │     ├─ Navigation (role-based)
  │     ├─ Notifications dropdown
  │     └─ User menu
  │
  ├─► Sidebar (Icon-only)
  │     │
  │     ├─ Role-based color:
  │     │   • Client: Blue
  │     │   • Operator: Green
  │     │   • Guru: Purple
  │     │   • Admin: Red
  │     │
  │     ├─ Navigation items:
  │     │   • Dashboard
  │     │   • AI Generator (client)
  │     │   • Analytics (client)
  │     │   • Brand Voices (client)
  │     │   • Orders
  │     │   • Queue (operator)
  │     │   • Workspace (operator)
  │     │   • Training (guru)
  │     │   • Users (admin)
  │     │   • Reports (admin)
  │     │
  │     └─ Tooltips on hover
  │
  ├─► Main Content
  │     └─ @yield('content')
  │
  └─► Footer
        ├─ Copyright
        ├─ Privacy Policy
        ├─ Terms of Service
        └─ Contact

RESPONSIVE DESIGN:

Desktop (≥ 1024px)
  ├─ Sidebar always visible
  ├─ Full navigation
  └─ Multi-column layouts

Tablet (768px - 1023px)
  ├─ Sidebar toggle
  ├─ Hamburger menu
  └─ 2-column layouts

Mobile (< 768px)
  ├─ Sidebar toggle + overlay
  ├─ Hamburger menu
  ├─ Single column
  └─ Touch-optimized

COMPONENTS:

Alpine.js Components:
  │
  ├─► AI Generator Form
  │     ├─ Mode toggle (Simple/Advanced)
  │     ├─ Dynamic form fields
  │     ├─ Brand voice loader
  │     ├─ Real-time validation
  │     └─ Loading states
  │
  ├─► Caption Display
  │     ├─ Copy to clipboard
  │     ├─ Rating stars
  │     ├─ Quality score badge
  │     └─ Action buttons
  │
  ├─► Analytics Charts
  │     ├─ Line chart (engagement over time)
  │     ├─ Bar chart (platform comparison)
  │     ├─ Pie chart (category distribution)
  │     └─ Interactive tooltips
  │
  ├─► Notifications Dropdown
  │     ├─ Real-time updates
  │     ├─ Mark as read
  │     ├─ Delete notification
  │     └─ View all link
  │
  └─► Modal Dialogs
        ├─ Confirmation dialogs
        ├─ Form modals
        ├─ Image preview
        └─ Loading overlays

STYLING:

Tailwind CSS Classes:
  │
  ├─► Colors:
  │     • Primary: Blue (client)
  │     • Success: Green (operator)
  │     • Warning: Yellow
  │     • Danger: Red (admin)
  │     • Info: Purple (guru)
  │
  ├─► Typography:
  │     • Headings: font-bold
  │     • Body: font-normal
  │     • Small: text-sm
  │     • Muted: text-gray-500
  │
  ├─► Spacing:
  │     • Container: max-w-7xl mx-auto
  │     • Padding: p-4, p-6, p-8
  │     • Margin: m-4, m-6, m-8
  │
  └─► Components:
        • Buttons: rounded-lg shadow-sm
        • Cards: bg-white rounded-lg shadow
        • Inputs: border rounded-lg focus:ring
        • Badges: rounded-full px-3 py-1

END
```

---

## 🔄 API ENDPOINTS

```
┌─────────────────────────────────────────────────────────────────┐
│                    API ENDPOINTS FLOW                            │
└─────────────────────────────────────────────────────────────────┘

PUBLIC ROUTES:
  │
  ├─► GET /
  │     • Welcome page
  │     • Landing page
  │
  ├─► GET /packages
  │     • List all packages
  │     • Public pricing
  │
  ├─► GET /packages/{id}
  │     • Package details
  │     • Features & pricing
  │
  └─► Legal Pages:
        ├─ GET /privacy-policy
        ├─ GET /terms-of-service
        ├─ GET /refund-policy
        └─ GET /contact

AUTHENTICATED ROUTES:

Client Routes (role:client):
  │
  ├─► AI Generator:
  │     ├─ GET /ai-generator
  │     │   • Show generator form
  │     │
  │     ├─ POST /api/ai/generate
  │     │   • Generate caption
  │     │   • Request body:
  │     │     {
  │     │       "mode": "simple|advanced",
  │     │       "category": "string",
  │     │       "subcategory": "string",
  │     │       "platform": "string",
  │     │       "brief": "string",
  │     │       "tone": "string",
  │     │       "keywords": "string",
  │     │       "variations": 1-20,
  │     │       "auto_hashtag": boolean,
  │     │       "local_language": "string"
  │     │     }
  │     │   • Response:
  │     │     {
  │     │       "success": boolean,
  │     │       "captions": [
  │     │         {
  │     │           "text": "string",
  │     │           "quality_score": 0-10,
  │     │           "grade": "A+|A|B|C|D"
  │     │         }
  │     │       ],
  │     │       "model_used": "string"
  │     │     }
  │     │
  │     └─ GET /api/check-first-time
  │         • Check if first generation
  │         • Response: { "is_first_time": boolean }
  │
  ├─► Analytics:
  │     ├─ GET /analytics
  │     │   • List all analytics
  │     │
  │     ├─ POST /analytics
  │     │   • Save caption analytics
  │     │   • Request body:
  │     │     {
  │     │       "caption_history_id": integer,
  │     │       "platform": "string",
  │     │       "likes": integer,
  │     │       "comments": integer,
  │     │       "shares": integer,
  │     │       "reach": integer,
  │     │       "impressions": integer
  │     │     }
  │     │
  │     ├─ GET /analytics/{id}
  │     │   • View analytics detail
  │     │
  │     ├─ PUT /analytics/{id}
  │     │   • Update analytics
  │     │
  │     ├─ GET /analytics/export-pdf
  │     │   • Export to PDF
  │     │
  │     ├─ GET /analytics/export-csv
  │     │   • Export to CSV
  │     │
  │     └─ GET /analytics-insights
  │         • Get insights & recommendations
  │
  ├─► Brand Voice:
  │     ├─ GET /brand-voices
  │     │   • List all brand voices
  │     │
  │     ├─ POST /brand-voices
  │     │   • Create brand voice
  │     │   • Request body:
  │     │     {
  │     │       "name": "string",
  │     │       "description": "string",
  │     │       "industry": "string",
  │     │       "target_audience": "string",
  │     │       "tone": "string",
  │     │       "keywords": ["string"],
  │     │       "platform": "string",
  │     │       "is_default": boolean
  │     │     }
  │     │
  │     └─ DELETE /brand-voices/{id}
  │         • Delete brand voice
  │
  ├─► Caption History:
  │     ├─ GET /caption-history
  │     │   • List all history
  │     │
  │     ├─ GET /caption-history/{id}
  │     │   • View history detail
  │     │
  │     ├─ DELETE /caption-history/{id}
  │     │   • Delete history
  │     │
  │     ├─ POST /caption-history/clear-all
  │     │   • Clear all history
  │     │
  │     └─ POST /api/caption/{id}/rate
  │         • Rate caption (1-5 stars)
  │
  ├─► Orders:
  │     ├─ GET /orders
  │     │   • List all orders
  │     │
  │     ├─ GET /orders/create/{package_id}
  │     │   • Show order form
  │     │
  │     ├─ POST /orders
  │     │   • Create order
  │     │
  │     ├─ GET /orders/{id}
  │     │   • View order detail
  │     │
  │     ├─ POST /orders/{id}/revision
  │     │   • Request revision
  │     │
  │     ├─ POST /orders/{id}/rate
  │     │   • Rate operator
  │     │
  │     └─ POST /orders/{id}/approve
  │         • Approve order
  │
  ├─► Feedback:
  │     ├─ GET /feedback
  │     │   • List my feedback
  │     │
  │     ├─ GET /feedback/create
  │     │   • Show feedback form
  │     │
  │     ├─ POST /feedback
  │     │   • Submit feedback
  │     │
  │     └─ GET /feedback/{id}
  │         • View feedback detail
  │
  └─► Browse Operators:
        ├─ GET /browse-operators
        │   • List all operators
        │
        └─ POST /request-order
            • Request order to operator

Operator Routes (role:operator):
  │
  ├─► Queue:
  │     ├─ GET /operator/queue
  │     │   • View available orders
  │     │
  │     ├─ POST /operator/orders/{id}/accept
  │     │   • Accept order
  │     │
  │     └─ POST /operator/orders/{id}/reject
  │         • Reject order
  │
  ├─► Workspace:
  │     ├─ GET /operator/workspace/{order_id}
  │     │   • View workspace
  │     │
  │     └─ POST /operator/workspace/{order_id}/submit
  │         • Submit work
  │
  ├─► Earnings:
  │     └─ GET /operator/earnings
  │         • View earnings summary
  │
  └─► Withdrawal:
        ├─ GET /operator/withdrawal/create
        │   • Show withdrawal form
        │
        ├─ POST /operator/withdrawal
        │   • Request withdrawal
        │
        └─ GET /operator/withdrawal/history
            • View withdrawal history

Guru Routes (role:guru):
  │
  ├─► Training:
  │     ├─ GET /guru/training
  │     │   • List pending training data
  │     │
  │     ├─ GET /guru/training/{id}
  │     │   • View training data detail
  │     │
  │     ├─ POST /guru/training
  │     │   • Approve/reject training data
  │     │
  │     └─ POST /guru/training/caption
  │         • Train from caption analytics
  │
  ├─► History:
  │     └─ GET /guru/training-history
  │         • View training history
  │
  └─► Analytics:
        └─ GET /guru/analytics
            • View ML analytics

Admin Routes (role:admin):
  │
  ├─► Users:
  │     ├─ GET /admin/users
  │     │   • List all users
  │     │
  │     ├─ GET /admin/users/create
  │     │   • Show create form
  │     │
  │     ├─ POST /admin/users
  │     │   • Create user
  │     │
  │     ├─ GET /admin/users/{id}/edit
  │     │   • Show edit form
  │     │
  │     ├─ PUT /admin/users/{id}
  │     │   • Update user
  │     │
  │     ├─ DELETE /admin/users/{id}
  │     │   • Delete user
  │     │
  │     ├─ POST /admin/users/{id}/verify
  │     │   • Verify operator
  │     │
  │     └─ POST /admin/users/{id}/unverify
  │         • Unverify operator
  │
  ├─► AI Usage:
  │     ├─ GET /admin/ai-usage
  │     │   • View AI usage analytics
  │     │
  │     ├─ GET /admin/ai-usage/{user_id}
  │     │   • View user AI usage
  │     │
  │     └─ GET /admin/ai-usage/{user_id}/stats
  │         • Get user stats
  │
  ├─► ML Analytics:
  │     ├─ GET /admin/ml-analytics
  │     │   • View ML insights
  │     │
  │     └─ GET /admin/ml-analytics/export
  │         • Export training data
  │
  ├─► Payments:
  │     ├─ GET /admin/payments
  │     │   • List pending payments
  │     │
  │     ├─ POST /admin/payments/{id}/verify
  │     │   • Verify payment
  │     │
  │     └─ POST /admin/payments/{id}/reject
  │         • Reject payment
  │
  ├─► Withdrawals:
  │     ├─ GET /admin/withdrawals
  │     │   • List withdrawal requests
  │     │
  │     ├─ POST /admin/withdrawals/{id}/approve
  │     │   • Approve withdrawal
  │     │
  │     ├─ POST /admin/withdrawals/{id}/reject
  │     │   • Reject withdrawal
  │     │
  │     └─ POST /admin/withdrawals/{id}/complete
  │         • Mark as completed
  │
  ├─► Feedback:
  │     ├─ GET /admin/feedback
  │     │   • List all feedback
  │     │
  │     ├─ GET /admin/feedback/{id}
  │     │   • View feedback detail
  │     │
  │     ├─ PUT /admin/feedback/{id}
  │     │   • Update feedback (respond)
  │     │
  │     └─ DELETE /admin/feedback/{id}
  │         • Delete feedback
  │
  └─► Reports:
        └─ GET /admin/reports
            • View financial reports

NOTIFICATIONS:
  │
  └─► GET /api/notifications
        • Get recent notifications
        • Response:
          {
            "notifications": [
              {
                "id": integer,
                "type": "string",
                "title": "string",
                "message": "string",
                "read_at": "datetime|null",
                "created_at": "datetime"
              }
            ]
          }

END
```

---

## 🤖 AI PROCESSING PIPELINE

```
┌─────────────────────────────────────────────────────────────────┐
│                    AI PROCESSING PIPELINE                        │
└─────────────────────────────────────────────────────────────────┘

INPUT → PROCESSING → VALIDATION → OUTPUT

[1] INPUT PREPARATION
  │
  ├─► Parse User Input
  │     • Mode (simple/advanced)
  │     • Category & subcategory
  │     • Platform
  │     • Brief/description
  │     • Tone
  │     • Keywords
  │     • Variations count
  │     • Auto hashtag flag
  │     • Local language
  │
  ├─► Load Brand Voice (if selected)
  │     • Merge with user input
  │     • Override defaults
  │
  └─► Prepare Context
        • Target audience detection
        • Pain points analysis
        • Desired action inference

[2] MODEL SELECTION
  │
  ├─► ModelFallbackManager
  │     │
  │     ├─ Auto-detect Tier
  │     │   • Check recent API responses
  │     │   • Check high-volume success
  │     │   • Default to free tier
  │     │
  │     ├─ Get Available Models
  │     │   Free Tier:
  │     │   • gemini-2.5-flash (10 RPM)
  │     │   • gemini-2.5-flash-lite (15 RPM)
  │     │   • gemini-3-flash-preview (10 RPM)
  │     │   • gemini-2.5-pro (5 RPM)
  │     │   • gemini-2.0-flash (10 RPM)
  │     │
  │     │   Tier 1 (Paid):
  │     │   • gemini-2.5-flash (300 RPM)
  │     │   • gemini-2.5-flash-lite (300 RPM)
  │     │   • gemini-3-flash-preview (300 RPM)
  │     │   • gemini-2.5-pro (150 RPM)
  │     │   • gemini-2.0-flash (300 RPM)
  │     │
  │     ├─ Check Rate Limits
  │     │   • RPM (Requests Per Minute)
  │     │   • RPD (Requests Per Day)
  │     │   • TPM (Tokens Per Minute)
  │     │
  │     └─ Select Best Model
  │         • Priority order
  │         • Availability check
  │         • Fallback if exhausted
  │
  └─► Model Configuration
        • Temperature: 0.7
        • Top K: 40
        • Top P: 0.95
        • Max Tokens: 4096 (5 var) / 8192 (20 var)

[3] PROMPT ENGINEERING
  │
  ├─► TemplatePrompts
  │     │
  │     ├─ Get Template for Subcategory
  │     │   • 200+ specialized templates
  │     │   • Task description
  │     │   • Format guidelines
  │     │   • Criteria
  │     │   • Tips
  │     │
  │     └─ Build Prompt
  │         Structure:
  │         1. Context (platform, audience, tone)
  │         2. Task (from template)
  │         3. Format (from template)
  │         4. Criteria (from template)
  │         5. Brief (user input)
  │         6. Keywords (if provided)
  │         7. Local language (if selected)
  │         8. Auto hashtag instruction
  │         9. Generate instruction
  │
  ├─► Audience Context
  │     • Auto-detect from brief
  │     • Target audience traits
  │     • Pain points
  │     • Desired action
  │
  └─► Platform Optimization
        • Character limits
        • Hashtag recommendations
        • Best practices
        • Tone adjustment

[4] API REQUEST
  │
  ├─► GeminiService
  │     │
  │     ├─ Build Request
  │     │   • Model name
  │     │   • Prompt
  │     │   • Generation config
  │     │   • Safety settings
  │     │
  │     ├─ Send to Gemini API
  │     │   Endpoint: https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent
  │     │   Headers:
  │     │   • Content-Type: application/json
  │     │   • x-goog-api-key: {GEMINI_API_KEY}
  │     │
  │     └─ Handle Response
  │         ├─ Success (200)
  │         │   • Parse JSON
  │         │   • Extract text
  │         │   • Track usage
  │         │
  │         ├─ Rate Limit (429)
  │         │   • Get fallback model
  │         │   • Retry with fallback
  │         │   • Update tier detection
  │         │
  │         └─ Error (4xx/5xx)
  │             • Log error
  │             • Return error message
  │             • Suggest retry
  │
  └─► Usage Tracking
        • Increment RPM counter
        • Increment RPD counter
        • Increment TPM counter
        • Cache for 1 minute/1 day

[5] OUTPUT VALIDATION
  │
  ├─► OutputValidator
  │     │
  │     ├─ Quality Checks:
  │     │   • Minimum length (15+ words)
  │     │   • Hashtag presence (if requested)
  │     │   • CTA effectiveness
  │     │   • Emoji usage (tone-appropriate)
  │     │   • Repetition check
  │     │   • Platform requirements
  │     │   • Spam patterns
  │     │   • Language quality
  │     │
  │     ├─ Calculate Score (0-10)
  │     │   Penalties:
  │     │   • Too short: -3
  │     │   • No hashtag: -1.5
  │     │   • Weak CTA: -1.5
  │     │   • Too similar: -5
  │     │   • Platform mismatch: -1 to -2
  │     │   • Spam patterns: -1
  │     │
  │     └─ Retry Decision
  │         • Score < 6.0 → Retry (max 2x)
  │         • Score ≥ 6.0 → Accept
  │         • Max retries reached → Accept anyway
  │
  └─► QualityScorer
        │
        ├─ Score Dimensions:
        │   1. Hook Quality (20%)
        │      • First sentence impact
        │      • Length (5-15 words ideal)
        │      • Question/curiosity
        │      • Emotional words
        │
        │   2. Engagement Potential (20%)
        │      • Questions count
        │      • Engagement words
        │      • Emoji usage
        │      • Storytelling elements
        │
        │   3. CTA Effectiveness (15%)
        │      • CTA presence
        │      • CTA strength (strong/medium/weak)
        │      • Urgency words
        │      • Contact method
        │
        │   4. Tone Match (15%)
        │      • Casual markers (kak, bun, gaes)
        │      • Formal markers (kami, anda)
        │      • Funny markers (wkwk, haha)
        │      • Persuasive markers (terbukti, dijamin)
        │      • Emotional markers (bahagia, terharu)
        │
        │   5. Platform Optimization (10%)
        │      • Word count (platform-specific)
        │      • Hashtag count
        │      • Character limit (Twitter)
        │
        │   6. Readability (10%)
        │      • Average sentence length
        │      • Paragraph breaks
        │      • Scannable structure
        │
        │   7. Uniqueness (10%)
        │      • Similarity to recent captions
        │      • Originality score
        │
        ├─ Calculate Total Score
        │   • Weighted average
        │   • Scale: 0-10
        │
        ├─ Assign Grade
        │   • 9.0+: A+
        │   • 8.5-8.9: A
        │   • 8.0-8.4: A-
        │   • 7.5-7.9: B+
        │   • 7.0-7.4: B
        │   • 6.5-6.9: B-
        │   • 6.0-6.4: C+
        │   • 5.5-5.9: C
        │   • 5.0-5.4: C-
        │   • <5.0: D
        │
        └─ Generate Recommendation
            • Based on weakest dimension
            • Actionable advice
            • Improvement suggestions

[6] OUTPUT FORMATTING
  │
  ├─► Parse Variations
  │     • Split by delimiter
  │     • Clean formatting
  │     • Remove artifacts
  │
  ├─► Add Metadata
  │     • Quality score
  │     • Grade
  │     • Model used
  │     • Generation time
  │
  └─► Prepare Response
        {
          "success": true,
          "captions": [
            {
              "text": "Caption text...",
              "quality_score": 8.5,
              "grade": "A",
              "breakdown": {
                "hook_quality": 9.0,
                "engagement_potential": 8.5,
                "cta_effectiveness": 8.0,
                "tone_match": 9.0,
                "platform_optimization": 8.0,
                "readability": 8.5,
                "uniqueness": 8.0
              },
              "recommendation": "Excellent caption!"
            }
          ],
          "model_used": "gemini-2.5-flash",
          "generation_time": "2.5s"
        }

[7] SAVE TO DATABASE
  │
  ├─► caption_history table
  │     • user_id
  │     • category
  │     • subcategory
  │     • platform
  │     • brief
  │     • tone
  │     • keywords
  │     • generated_caption
  │     • quality_score
  │     • grade
  │     • model_used
  │     • created_at
  │
  └─► Track for ML
        • High-rated captions
        • Performance metrics
        • Training data pool

END
```

---

## 📈 ANALYTICS & TRACKING FLOW

```
┌─────────────────────────────────────────────────────────────────┐
│                    ANALYTICS & TRACKING                          │
└─────────────────────────────────────────────────────────────────┘

[1] CAPTION ANALYTICS TRACKING
  │
  ├─► Client saves caption for tracking
  │     • Select caption from history
  │     • Choose platform
  │     • Input metrics:
  │       - Likes
  │       - Comments
  │       - Shares
  │       - Reach
  │       - Impressions
  │       - Clicks (optional)
  │
  ├─► Auto-calculate Engagement Rate
  │     Formula: (Likes + Comments + Shares) / Reach × 100
  │
  ├─► Save to caption_analytics table
  │
  └─► Track over time
        • Update metrics periodically
        • Compare performance
        • Identify trends

[2] AI USAGE ANALYTICS (Admin)
  │
  ├─► Track per user:
  │     • Total generations
  │     • Category breakdown
  │     • Platform distribution
  │     • Average quality score
  │     • Success rate
  │
  ├─► System-wide metrics:
  │     • Total API calls
  │     • Model usage distribution
  │     • Rate limit hits
  │     • Error rate
  │     • Average response time
  │
  └─► Cost tracking:
        • API costs per model
        • Cost per user
        • Monthly spending
        • ROI analysis

[3] ML ANALYTICS (Guru)
  │
  ├─► Training data metrics:
  │     • Total approved data
  │     • Approval rate
  │     • Category distribution
  │     • Quality score trends
  │
  ├─► Model performance:
  │     • Accuracy improvement
  │     • Engagement rate increase
  │     • User satisfaction
  │
  └─► Insights:
        • Best performing categories
        • Most effective tones
        • Platform preferences
        • Optimization opportunities

END
```

---

## 🔄 SYSTEM INTEGRATION FLOW

```
┌─────────────────────────────────────────────────────────────────┐
│                    SYSTEM INTEGRATIONS                           │
└─────────────────────────────────────────────────────────────────┘

GOOGLE GEMINI AI:
  │
  ├─► API Configuration
  │     • Endpoint: generativelanguage.googleapis.com
  │     • API Key: From .env (GEMINI_API_KEY)
  │     • Models: 5 models with fallback
  │     • Rate limits: Auto-detected tier
  │
  ├─► Request Flow
  │     1. Select best available model
  │     2. Build specialized prompt
  │     3. Send API request
  │     4. Handle response/errors
  │     5. Track usage
  │     6. Fallback if needed
  │
  └─► Error Handling
        • Rate limit → Fallback model
        • API error → Retry with exponential backoff
        • Invalid response → Validation & retry
        • Network error → User notification

PAYMENT INTEGRATION:
  │
  ├─► Manual Transfer (Primary)
  │     • Admin configures bank accounts
  │     • Client uploads payment proof
  │     • Admin verifies manually
  │     • Order activated on approval
  │
  └─► Midtrans (Optional)
        • Server key & client key in .env
        • Snap payment gateway
        • Automatic verification
        • Webhook for status updates

EMAIL NOTIFICATIONS:
  │
  ├─► Laravel Mail
  │     • SMTP configuration
  │     • Email templates
  │     • Queue for async sending
  │
  └─► Notification Types:
        • Email verification
        • Order status updates
        • Payment confirmation
        • Withdrawal approval
        • System announcements

FILE STORAGE:
  │
  ├─► Local Storage (Development)
  │     • storage/app/public
  │     • Symlink to public/storage
  │
  └─► Cloud Storage (Production)
        • AWS S3 / Google Cloud Storage
        • CDN for faster delivery
        • Automatic backups

CACHING:
  │
  ├─► Rate Limit Tracking
  │     • Redis/File cache
  │     • TTL: 1 minute (RPM), 1 day (RPD)
  │     • Automatic expiration
  │
  └─► Tier Detection
        • Cache detected tier
        • TTL: 1 hour
        • Auto-refresh on API response

END
```

---

## 📊 PERFORMANCE OPTIMIZATION

```
┌─────────────────────────────────────────────────────────────────┐
│                    PERFORMANCE OPTIMIZATIONS                     │
└─────────────────────────────────────────────────────────────────┘

BACKEND OPTIMIZATIONS:
  │
  ├─► Database
  │     • Indexes on foreign keys
  │     • Indexes on frequently queried columns
  │     • Eager loading relationships
  │     • Query optimization
  │
  ├─► Caching
  │     • Rate limit counters
  │     • Tier detection
  │     • User preferences
  │     • Static content
  │
  ├─► API Optimization
  │     • Connection pooling
  │     • Request batching
  │     • Async processing
  │     • Retry with exponential backoff
  │
  └─► Code Optimization
        • Lazy loading
        • Service layer pattern
        • Repository pattern
        • Dependency injection

FRONTEND OPTIMIZATIONS:
  │
  ├─► Asset Optimization
  │     • Tailwind CSS CDN
  │     • Minified JavaScript
  │     • Lazy loading images
  │     • Icon sprites
  │
  ├─► Rendering
  │     • Server-side rendering (Blade)
  │     • Client-side reactivity (Alpine.js)
  │     • Minimal JavaScript
  │     • Progressive enhancement
  │
  └─► User Experience
        • Loading states
        • Skeleton screens
        • Optimistic UI updates
        • Error boundaries

AI PROCESSING OPTIMIZATIONS:
  │
  ├─► Model Selection
  │     • Auto-detect best model
  │     • Fallback on rate limits
  │     • Load balancing across models
  │
  ├─► Prompt Engineering
  │     • Specialized templates
  │     • Concise prompts
  │     • Clear instructions
  │     • Optimal token usage
  │
  ├─► Output Validation
  │     • Fast quality checks
  │     • Smart retry logic
  │     • Parallel processing
  │
  └─► Usage Tracking
        • Efficient caching
        • Minimal database writes
        • Batch updates

END
```

---

## 🎯 KEY FEATURES SUMMARY

```
┌─────────────────────────────────────────────────────────────────┐
│                    KEY FEATURES OVERVIEW                         │
└─────────────────────────────────────────────────────────────────┘

✅ AI GENERATOR
  • Mode Simpel: 6 pertanyaan → 5 variasi GRATIS
  • Mode Advanced: 200+ templates, 1-20 variasi
  • 12 Industry Presets khusus UMKM
  • Auto hashtag Indonesia
  • Bahasa daerah (Jawa, Sunda, Betawi, Minang, Batak)
  • Quality scoring (0-10) dengan grade
  • Multi-model fallback (5 Gemini models)
  • Auto tier detection (Free/Paid)

✅ BRAND VOICE MANAGEMENT
  • Save unlimited brand voices
  • Set default brand voice
  • Quick load dengan 1 klik
  • Auto-fill form preferences
  • Perfect untuk agency/multiple brands

✅ CAPTION ANALYTICS
  • Track performance (likes, comments, shares, reach)
  • Auto-calculate engagement rate
  • Export PDF & CSV
  • Platform comparison
  • Category insights
  • Performance trends

✅ MARKETPLACE SYSTEM
  • Browse operators (siswa SMK)
  • Request order
  • Payment verification
  • Order tracking
  • Revision system
  • Rating & reviews
  • Earnings & withdrawal

✅ ML TRAINING SYSTEM
  • Guru review interface
  • Approve/reject training data
  • Model versioning
  • Performance analytics
  • Quality scoring
  • Dataset export

✅ FEEDBACK & SUPPORT
  • 4 types: Bug, Feature, Improvement, Question
  • Screenshot upload
  • Auto-capture page info
  • Status tracking
  • Admin response
  • Timeline visualization

✅ ADMIN DASHBOARD
  • User management
  • AI usage analytics
  • ML analytics
  • Payment verification
  • Withdrawal approval
  • Feedback management
  • Financial reports

END
```

---

## 🚀 DEPLOYMENT FLOW

```
┌─────────────────────────────────────────────────────────────────┐
│                    DEPLOYMENT WORKFLOW                           │
└─────────────────────────────────────────────────────────────────┘

DEVELOPMENT:
  │
  ├─► Local Setup
  │     1. Clone repository
  │     2. composer install
  │     3. Copy .env.example → .env
  │     4. php artisan key:generate
  │     5. Configure database
  │     6. php artisan migrate --seed
  │     7. php artisan serve
  │
  └─► Testing
        • Manual testing
        • Feature testing
        • API testing
        • Browser testing

STAGING:
  │
  ├─► Server Setup
  │     • PHP 8.2+
  │     • Composer
  │     • MySQL/PostgreSQL
  │     • Nginx/Apache
  │     • SSL certificate
  │
  ├─► Deployment
  │     1. Pull latest code
  │     2. composer install --no-dev
  │     3. php artisan migrate
  │     4. php artisan config:cache
  │     5. php artisan route:cache
  │     6. php artisan view:cache
  │
  └─► Testing
        • Smoke testing
        • Integration testing
        • Performance testing
        • Security testing

PRODUCTION:
  │
  ├─► Pre-deployment
  │     • Backup database
  │     • Backup files
  │     • Maintenance mode
  │
  ├─► Deployment
  │     1. Pull latest code
  │     2. composer install --no-dev --optimize-autoloader
  │     3. php artisan migrate --force
  │     4. php artisan config:cache
  │     5. php artisan route:cache
  │     6. php artisan view:cache
  │     7. php artisan optimize
  │
  ├─► Post-deployment
  │     • Verify deployment
  │     • Test critical features
  │     • Monitor logs
  │     • Exit maintenance mode
  │
  └─► Monitoring
        • Error tracking
        • Performance monitoring
        • API usage tracking
        • User activity monitoring

END
```

---

## 📝 CONCLUSION

Smart Copy SMK adalah platform marketplace copywriting berbasis AI yang komprehensif dengan fitur-fitur:

1. **AI Generator** - 200+ templates dengan multi-model fallback
2. **Brand Voice** - Konsistensi brand untuk multiple clients
3. **Analytics** - Track performa caption secara real-time
4. **Marketplace** - Hubungkan client dengan operator (siswa SMK)
5. **ML Training** - Continuous improvement dengan guru review
6. **Admin Dashboard** - Complete management & analytics

**Tech Stack:**
- Backend: Laravel 11 + PHP 8.2
- AI: Google Gemini 2.5 Flash (5 models)
- Frontend: Blade + Tailwind CSS + Alpine.js
- Database: MySQL/SQLite

**Status:** ✅ Production Ready

---

**Last Updated:** 9 Maret 2026  
**Version:** 1.0.0  
**Author:** Wahyu

