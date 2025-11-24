# Reloved Marketplace - Dokumentasi Lengkap Project

**Versi**: 1.0  
**Tanggal**: 2025-01-XX  
**Status**: Development (Localhost)  
**Framework**: Laravel 10.x

---

## Daftar Isi

1. [Overview Project](#1-overview-project)
2. [Tech Stack & Environment](#2-tech-stack--environment)
3. [Arsitektur & Struktur Project](#3-arsitektur--struktur-project)
4. [Database Schema](#4-database-schema)
5. [Fitur yang Sudah Diimplementasikan](#5-fitur-yang-sudah-diimplementasikan)
6. [Fitur yang Belum Diimplementasikan](#6-fitur-yang-belum-diimplementasikan)
7. [Cara Kerja Fitur Utama](#7-cara-kerja-fitur-utama)
8. [Perbedaan dengan Plan Awal](#8-perbedaan-dengan-plan-awal)
9. [API Endpoints](#9-api-endpoints)
10. [Security & Best Practices](#10-security--best-practices)

---

## 1. Overview Project

**Reloved Marketplace** adalah platform C2C (Consumer-to-Consumer) marketplace untuk jual beli barang preloved. Platform ini dirancang sebagai **penghubung** antara pembeli dan penjual, dengan sistem **COD (Cash on Delivery)** tanpa escrow atau payment gateway.

### Karakteristik Utama
- **C2C Marketplace**: Platform untuk jual beli barang preloved antar konsumen
- **COD System**: Pembayaran langsung antara buyer-seller, platform hanya tracking
- **No Admin Panel**: Tidak ada admin untuk review atau manage konten
- **Localhost Only**: Dikembangkan untuk localhost (Laragon), tidak ada deployment
- **Design Based**: UI mengikuti Figma design yang sudah dibuat
- **UX Reference**: Flow dan interaksi mengikuti Carousell

---

## 2. Tech Stack & Environment

### 2.1 Backend

| Komponen | Teknologi | Versi | Status | Catatan |
|----------|-----------|-------|--------|---------|
| Framework | Laravel | 10.x | ✅ | LTS version |
| Database | PostgreSQL | 15+ | ✅ | Via Laragon |
| Authentication | Laravel Session | - | ✅ | Database session driver |
| OAuth | Laravel Socialite | 5.23 | ✅ | Google Login |
| Real-time | Laravel Reverb | 1.0 | ⚠️ | **Disabled** (no Redis) |
| Broadcasting | Laravel Broadcasting | - | ⚠️ | **Log driver** (no Redis) |
| File Storage | Cloudinary | 2.3 | ✅ | Image hosting |
| Image Processing | Intervention Image | 3.11 | ✅ | Image manipulation |
| HTTP Client | Guzzle | 7.2 | ✅ | External API calls |
| UUID | Ramsey UUID | 4.9 | ✅ | Primary keys |
| Query Builder | Spatie Query Builder | 6.3 | ✅ | Advanced filtering |

**Catatan Penting:**
- **Laravel Reverb**: Terinstall tapi **disabled** karena tidak ada Redis di Laragon
- **Broadcasting**: Menggunakan `log` driver, bukan real-time WebSocket
- **Chat System**: Menggunakan **polling** (refresh page untuk update), bukan real-time

### 2.2 Frontend

| Komponen | Teknologi | Versi | Status | Catatan |
|----------|-----------|-------|--------|---------|
| Template Engine | Laravel Blade | - | ✅ | Server-side rendering |
| CSS Framework | Tailwind CSS | 3.4.18 | ✅ | Via npm (no CDN) |
| JavaScript | Alpine.js | 3.15.1 | ✅ | Via npm (no CDN) |
| Build Tool | Laravel Vite | 5.0 | ✅ | Asset compilation |
| Real-time Client | Laravel Echo | 1.19.0 | ⚠️ | **Dummy object** (no Reverb) |
| Pusher JS | Pusher JS | 8.4.0 | ⚠️ | **Not used** (no Reverb) |

**Catatan Penting:**
- **No CDN**: Semua assets di-download via npm (Tailwind, Alpine.js)
- **Real-time Disabled**: Echo dibuat dummy object untuk prevent errors
- **Polling Based**: Chat menggunakan page refresh untuk update messages

### 2.3 External Services

| Service | Purpose | Status | API Key Location |
|---------|---------|--------|------------------|
| Cloudinary | Image storage & optimization | ✅ | `.env` (CLOUDINARY_URL) |
| RajaOngkir | Shipping cost calculation | ✅ | `.env` (RAJAONGKIR_API_KEY) |
| Gmail SMTP | Email OTP verification | ✅ | `.env` (MAIL_*) |
| Google OAuth | Social login | ✅ | `.env` (GOOGLE_*) |

### 2.4 Development Environment

- **OS**: Windows
- **Local Server**: Laragon
- **Database Client**: HeidiSQL (via Laragon)
- **PHP Version**: 8.1+
- **Node.js**: Via Laragon
- **Composer**: Via Laragon

---

## 3. Arsitektur & Struktur Project

### 3.1 Project Structure

```
preloved-marketplace/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # 13 controllers
│   │   ├── Requests/             # Form validation
│   │   └── Middleware/           # Custom middleware
│   ├── Models/                   # 12 models
│   ├── Services/                 # 3 service classes
│   ├── Events/                   # Broadcasting events
│   └── Mail/                     # Email templates
├── database/
│   ├── migrations/               # Database schema
│   └── seeders/                  # Dummy data
├── resources/
│   ├── views/                    # Blade templates
│   └── js/                       # Alpine.js + Echo
├── routes/
│   └── web.php                   # Web routes
└── config/                       # Configuration files
```

### 3.2 Controllers

| Controller | Purpose | Methods | Status |
|------------|---------|---------|--------|
| `AuthController` | Authentication & OAuth | register, login, logout, verifyEmail, Google OAuth | ✅ |
| `UserController` | Profile management | showProfile, editProfile, updateProfile | ✅ |
| `ProductController` | Product CRUD | index, show, create, store, edit, update, destroy | ✅ |
| `CategoryController` | Category pages | show | ✅ |
| `ChatController` | Chat & messaging | index, show, create, store, markAsRead | ✅ |
| `OfferController` | Offer & negotiation | store, accept, reject, counter | ✅ |
| `TransactionController` | Transaction tracking | index, show, store, updateShipping, markAsReceived, cancel | ✅ |
| `ReviewController` | Review & rating | index, create, store, edit, update, destroy | ✅ |
| `WishlistController` | Wishlist management | index, toggle, destroy, check | ✅ |
| `NotificationController` | Notifications | index, markAsRead, markAllAsRead, destroy | ✅ |
| `ShippingController` | RajaOngkir API | getProvinces, getCities, checkCost | ✅ |
| `HomeController` | Homepage | index, getRecommendedProducts | ✅ |

### 3.3 Models

| Model | Purpose | Relationships | Soft Deletes |
|-------|---------|---------------|--------------|
| `User` | User accounts | products, reviews, conversations | ✅ |
| `Product` | Product listings | user, category, images, wishlists | ✅ |
| `ProductImage` | Product images | product | ❌ |
| `Category` | Categories | parent, children, products | ❌ |
| `Conversation` | Chat conversations | product, buyer, seller, messages | ❌ |
| `Message` | Chat messages | conversation, sender | ❌ |
| `Offer` | Offers/negotiations | conversation, product, buyer, seller | ❌ |
| `Transaction` | Transactions | product, buyer, seller, reviews | ✅ |
| `Review` | Reviews & ratings | transaction, reviewer, reviewed, product | ❌ |
| `Wishlist` | User wishlist | user, product | ❌ |
| `Notification` | In-app notifications | user | ❌ |
| `EmailVerification` | OTP verification | - | ❌ |

### 3.4 Services

| Service | Purpose | Methods |
|--------|---------|---------|
| `ImageService` | Cloudinary integration | uploadAvatar, uploadProductImage, deleteImage |
| `RajaOngkirService` | Shipping API | getProvinces, getCities, checkCost, getCityId |
| `NotificationService` | Notification management | createNotification, notifyNewMessage, notifyNewOffer, dll |

---

## 4. Database Schema

### 4.1 Core Tables

#### users
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Key Fields**: name, email, phone (unique), password, avatar, city, province, google_id, rating_avg, review_count
- **Indexes**: email, phone, city, province

#### products
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Key Fields**: user_id, category_id, title, slug, description, price, condition, brand, stock, deal_method (json), status, view_count, favorite_count
- **Indexes**: user_id, category_id, status, stock, created_at, price
- **Constraints**: stock > 0 untuk available products

#### product_images
- **Primary Key**: `id` (UUID)
- **Key Fields**: product_id, cloudinary_public_id, cloudinary_url, is_primary, order
- **Max Images**: 10 per product

#### conversations
- **Primary Key**: `id` (UUID)
- **Key Fields**: product_id, buyer_id, seller_id, last_message_at
- **Indexes**: buyer_id, seller_id, product_id, last_message_at

#### messages
- **Primary Key**: `id` (UUID)
- **Key Fields**: conversation_id, sender_id, message, message_type (text/offer), offer_amount, offer_status, is_read
- **Indexes**: conversation_id, created_at

#### offers
- **Primary Key**: `id` (UUID)
- **Key Fields**: conversation_id, product_id, buyer_id, seller_id, amount, status, counter_count
- **Max Counter**: 3-5 kali (didefinisikan di model)

#### transactions
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Key Fields**: product_id, buyer_id, seller_id, price, deal_method, status, shipping_cost, tracking_number, meetup_location
- **Status Flow**: menunggu_transaksi → barang_dikirim → paket_diterima → selesai
- **Indexes**: buyer_id, seller_id, product_id, status, created_at

#### reviews
- **Primary Key**: `id` (UUID)
- **Key Fields**: transaction_id, reviewer_id, reviewed_id, product_id, rating (1-5), comment, images (json)
- **Unique Constraint**: reviewer_id + transaction_id (satu review per transaksi)

#### favorites (wishlist)
- **Primary Key**: `id` (UUID)
- **Key Fields**: user_id, product_id
- **Unique Constraint**: user_id + product_id

#### notifications
- **Primary Key**: `id` (UUID)
- **Key Fields**: user_id, type, title, message, data (json), is_read
- **Indexes**: user_id, is_read, created_at

#### email_verifications
- **Primary Key**: `id` (UUID)
- **Key Fields**: email, otp (6 digits), expires_at, is_used
- **OTP Lifetime**: 15 menit

### 4.2 Database Features

- **UUID Primary Keys**: Semua tabel menggunakan UUID (bukan auto-increment)
- **Soft Deletes**: users, products, transactions
- **Foreign Key Constraints**: Semua relationships memiliki FK constraints
- **Indexes**: Optimized untuk query performance
- **Timestamps**: created_at, updated_at untuk semua tabel

---

## 5. Fitur yang Sudah Diimplementasikan

### 5.1 Authentication & User Management ✅

#### Fitur
- ✅ User registration dengan email
- ✅ Email OTP verification (6 digit, 15 menit expiry)
- ✅ Login/Logout dengan session management
- ✅ Session regeneration setelah login
- ✅ Google OAuth login (Socialite)
- ✅ User profile CRUD
- ✅ Avatar upload ke Cloudinary
- ✅ Location management (province & city via RajaOngkir)
- ✅ Phone number management (unique constraint)
- ✅ Profile page dengan review display

#### Teknologi
- **Session Driver**: Database (bukan file)
- **OTP Generation**: Random 6 digit dengan expiry
- **OAuth**: Laravel Socialite untuk Google Login
- **Image Upload**: Cloudinary dengan auto-optimization

#### Alur Kerja
1. **Registration**: User daftar → Generate OTP → Kirim email → Verify OTP → Login
2. **Google Login**: Redirect ke Google → Callback → Link/create user → Auto-verify email → Login
3. **Profile Update**: Update data → Validasi unique phone → Upload avatar (jika ada) → Save

### 5.2 Product Management (Blom di optimasi)

#### Fitur
- ✅ Product CRUD (Create, Read, Update, Delete)
- ✅ Multiple image upload (max 10 images) ke Cloudinary
- ✅ Primary image (gambar pertama otomatis primary)
- ✅ Product categories dengan hierarchical structure
- ✅ Product detail page dengan image gallery
- ✅ Product listing dengan pagination (20 per page)
- ✅ Stock management (auto-decrement, hide when stock = 0)
- ✅ "Mark as Sold" functionality dengan buyer selection
- ✅ Product view count tracking
- ✅ Product favorite count
- ✅ Soft delete products

#### Teknologi
- **Image Storage**: Cloudinary (folder: `reloved/products`)
- **Image Optimization**: Auto-resize dan format optimization
- **Stock Management**: Auto-decrement saat transaction created
- **Search**: PostgreSQL Full-Text Search

#### Alur Kerja
1. **Create Product**: Upload images → Fill form → Save → Images uploaded to Cloudinary
2. **Update Product**: Edit data → Add new images (jika ada) → Update
3. **Delete Product**: Soft delete → Delete images from Cloudinary → Hide from search
4. **Mark as Sold**: Select buyer → Create transaction → Decrement stock → Update status

### 5.3 Search & Filter ✅

#### Fitur
- ✅ PostgreSQL Full-Text Search (Indonesian language)
- ✅ Filter by category (hierarchical)
- ✅ Filter by location (province & city)
- ✅ Filter by price range (min-max)
- ✅ Filter by condition (Baru, Lumayan Baru, Bekas, Rusak)
- ✅ Filter by brand (ILIKE search)
- ✅ Header search dengan province filter
- ✅ Combined filters (multiple filters sekaligus)

#### Teknologi
- **Full-Text Search**: PostgreSQL `to_tsvector` dan `plainto_tsquery`
- **Language**: Indonesian (bahasa Indonesia)
- **Fallback**: ILIKE search jika full-text tidak menemukan hasil

#### Alur Kerja
1. **Search**: Input keyword → Full-text search → Fallback ILIKE → Return results
2. **Filter**: Select filters → Apply to query → Paginate results
3. **Combined**: Search + multiple filters → Combined query → Results

### 5.4 Chat System ⚠️ (blom di testing)

#### Fitur
- ⚠️ Conversation management
- ⚠️ Message creation (text & offer)
- ⚠️ Message read status
- ⚠️ Chat list page
- ⚠️ Chat detail page
- ⚠️ **Real-time disabled** (menggunakan polling/page refresh)

#### Teknologi
- **Broadcasting**: Laravel Broadcasting dengan `log` driver
- **Real-time**: **Disabled** (no Redis, no Reverb)
- **Update Method**: Page refresh untuk melihat messages baru
- **Events**: `MessageSent` event (tapi tidak broadcast karena log driver)

#### Alur Kerja
1. **Create Conversation**: Buyer klik "Chat Penjual" → Create/find conversation → Redirect to chat
2. **Send Message**: Input message → Create message → Update last_message_at → Refresh page
3. **Read Status**: Mark messages as read saat chat dibuka

**Catatan**: Chat **tidak real-time** karena Reverb disabled. User harus refresh page untuk melihat messages baru.

### 5.5 Offer & Negotiation (blom dibuat)

#### Fitur
-⚠️ Offer creation langsung dari chat
- ⚠️ Offer accept/reject
- ⚠️ Counter offer dengan limit (MAX_COUNTER_COUNT = 3-5 kali)
- ⚠️ Offer history tracking
- ⚠️ Offer UI di chat interface
- ⚠️ Counter count display ("Tawaran ke-2 dari 5")
- ⚠️ Validasi: offer < harga produk, counter > offer sebelumnya

#### Teknologi
- **Offer Model**: Separate table untuk tracking offers
- **Counter Limit**: Defined di `Offer` model constant
- **Status Flow**: pending → accepted/rejected/counter_offer

#### Alur Kerja
1. **Create Offer**: Buyer input amount → Validasi (< harga produk) → Create offer + message → Notify seller
2. **Accept Offer**: Seller accept → Update status → Reject other offers → Create message → Notify buyer
3. **Counter Offer**: Seller counter → Validasi (> offer sebelumnya, < harga produk) → Check counter limit → Update offer → Create message

### 5.6 Transaction Management (blom dibuat)

#### Fitur
- ⚠️ Transaction creation dari "Mark as Sold"
- ⚠️ Transaction tracking untuk COD
- ⚠️ Status flow management:
  - **Meet-up**: Langsung "Selesai"
  - **Shipping**: "Menunggu Transaksi" → "Barang dikirim" → "Paket diterima" → "Selesai"
- ⚠️ Transaction detail page
- ⚠️ Transaction list dengan pagination
- ⚠️ Cancel transaction (restore stock)
- ⚠️Shipping tracking (manual tracking number input)

#### Teknologi
- **Status Management**: Method di `Transaction` model (markAsShipped, markAsReceived, markAsCompleted)
- **Stock Management**: Auto-decrement saat transaction created, restore saat cancel
- **Status Validation**: Method `canBeShipped()`, `canBeReceived()`, `canBeCompleted()`

#### Alur Kerja
1. **Create Transaction**: Seller "Mark as Sold" → Select buyer → Create transaction → Decrement stock
2. **Shipping Flow**: Seller "Barang dikirim" → Input tracking → Update status → Notify buyer
3. **Receive Flow**: Buyer "Paket diterima" → Auto complete → Notify seller
4. **Cancel**: Cancel transaction → Restore stock → Update product status

### 5.7 Shipping Integration (blom dibuat)

#### Fitur
- ✅ RajaOngkir API integration
- ✅ Province & city dropdown (dynamic dari API)
- ⚠️ Shipping cost calculation
- ⚠️ Manual tracking number input
- ⚠️ Shipping courier selection (JNE, TIKI, POS)

#### Teknologi
- **API**: RajaOngkir API (free tier: 10,000 requests/month)
- **Service**: `RajaOngkirService` untuk API calls
- **Caching**: Tidak ada (direct API calls)

#### Alur Kerja
1. **Load Provinces**: Fetch dari RajaOngkir API → Populate dropdown
2. **Load Cities**: Select province → Fetch cities → Populate dropdown
3. **Check Cost**: Input origin, destination, weight, courier → Calculate cost → Display

### 5.8 Review & Rating (blom di testing)

#### Fitur
- ⚠️ Review creation setelah transaksi selesai
- ⚠️ Rating system (1-5 stars)
- ⚠️ Optional comment & images
- ⚠️ One review per transaction (unique constraint)
- ⚠️ Review display di product & profile page
- ⚠️ Rating average calculation
- ⚠️ Review count update
- ⚠️ Review edit & delete

#### Teknologi
- **Rating Calculation**: Auto-update user `rating_avg` dan `review_count`
- **Image Upload**: Multiple images ke Cloudinary (folder: `reviews`)
- **Unique Constraint**: Database constraint untuk prevent duplicate reviews

#### Alur Kerja
1. **Create Review**: After "Paket diterima" → Create review → Upload images (optional) → Calculate rating → Update user stats
2. **Display Review**: Show di product detail & user profile → Calculate average → Display count

### 5.9 Wishlist/Favorites (blom di testing)

#### Fitur
- ⚠️ Add/remove product to wishlist
- ⚠️ Wishlist page dengan product listing
- ⚠️ Favorite count per product (auto-increment/decrement)
- ⚠️ Check favorite status API
- ⚠️ AJAX toggle (no page refresh)

#### Teknologi
- **AJAX**: Toggle favorite tanpa page refresh
- **Count Management**: Auto-increment/decrement `favorite_count` di product

#### Alur Kerja
1. **Toggle Favorite**: Click heart icon → AJAX request → Add/remove from wishlist → Update count → Update UI

### 5.10 Notification System (blom di testing)

#### Fitur
- ⚠️ In-app notifications
- ⚠️ Notification untuk:
  - New messages
  - New offers
  - Offer accepted/rejected
  - Transaction updates
  - Review received
- ⚠️ Notification badge di header
- ⚠️ Mark as read / Mark all as read
- ⚠️ Notification list page
- ⚠️ Unread count API

#### Teknologi
- **Service**: `NotificationService` untuk create notifications
- **Real-time**: **Disabled** (badge update via page refresh)

#### Alur Kerja
1. **Create Notification**: Event terjadi → Create notification → Store in database
2. **Display**: Load notifications → Show badge count → Display list
3. **Read**: Mark as read → Update badge count

### 5.11 Homepage Features ✅

#### Fitur
- ✅ **Produk Spotlight**: Algoritma scoring (view 40%, favorite 30%, seller rating 20%, recency 10%)
- ✅ **Barang Terbaru**: Produk terbaru dalam 7 hari
- ✅ **Mungkin Kamu Suka Ini**: Rekomendasi personalisasi:
  - Prioritas 1: Kategori dari wishlist
  - Prioritas 2: Kategori dari produk populer
  - Prioritas 3: Lokasi user
  - Fallback: Rekomendasi umum

#### Teknologi
- **Scoring Algorithm**: Raw SQL dengan weighted scoring
- **Recommendation**: Multi-priority fallback system

### 5.12 UI/UX Implementation (blom sesuai)

#### Fitur
- ⚠️ Figma design implementation
- ⚠️Responsive design (mobile-first)
- ✅ Tailwind CSS dengan design tokens
- ✅ Alpine.js untuk interaktivitas
- ✅ Image assets integration
- ✅ Consistent design system
- ✅ Category pages dengan custom layout

---

## 6. Fitur yang Belum Diimplementasikan

### 6.1 Reporting System ❌ (ngga dibuat, tidak ada admin)

**Status**: ❌ **Belum diimplementasikan**

**Fitur yang direncanakan:**
- Report barang (spam, fake, tidak sesuai deskripsi)
- Report user (scammer, tidak responsif)
- Report chat (harassment)
- Report disimpan untuk tracking (tidak ada admin review)

**Yang perlu dibuat:**
- `ReportController`
- `Report` model
- `reports` table migration
- Report form di product detail, profile, chat
- Report list page (untuk tracking)

### 6.2 Real-time Features ❌ 

**Status**: ⚠️ **Partially implemented (disabled)**

**Fitur yang direncanakan:**
- Real-time chat (WebSocket)
- Real-time notifications
- Real-time transaction updates

**Masalah:**
- Laravel Reverb terinstall tapi **disabled** (no Redis)
- Broadcasting menggunakan `log` driver (bukan WebSocket)
- Chat menggunakan **polling** (page refresh)

**Solusi yang diperlukan:**
- Setup Redis di Laragon
- Enable Reverb server
- Change broadcasting driver ke `reverb`
- Update frontend Echo configuration

### 6.3 Seller Verification ❌

**Status**: ❌ **Belum diimplementasikan**

**Fitur yang direncanakan:**
- KTP Verification → Badge "Verified Seller"
- Phone Verification → Badge "Phone Verified"
- Verification status di profile

**Yang perlu dibuat:**
- `verifications` table
- Verification upload form
- Badge display di profile
- Verification status management

### 6.4 Advanced Features ❌

**Status**: ❌ **Belum diimplementasikan**

**Fitur yang direncanakan:**
- Analytics Dashboard (untuk user sendiri)
- Promo/Voucher System
- Product sharing (social media)
- Email notifications (untuk event besar)
- Push notifications (mobile app)

### 6.5 Admin Features ❌

**Status**: ❌ **Tidak akan diimplementasikan** (sesuai requirement)

**Catatan**: Sesuai dengan requirement, platform ini **tidak memiliki admin panel**. Semua fitur adalah C2C tanpa admin intervention.

---

## 7. Cara Kerja Fitur Utama

### 7.1 User Registration & Email Verification

**Alur:**
```
1. User input data → Validation
2. Create user (password hashed)
3. Generate OTP (6 digit, random)
4. Store OTP di email_verifications table (expires 15 menit)
5. Send OTP via email (Gmail SMTP)
6. User input OTP → Verify
7. Update email_verified_at → Login user
```

**Teknologi:**
- **OTP Generation**: `EmailVerification::generate()` - Random 6 digit dengan expiry
- **Email Sending**: Laravel Mail dengan `OTPMail` mailable
- **Session**: Database session driver

**File terkait:**
- `app/Http/Controllers/AuthController.php`
- `app/Models/EmailVerification.php`
- `app/Mail/OTPMail.php`

### 7.2 Google OAuth Login

**Alur:**
```
1. User klik "Masuk dengan Google"
2. Redirect ke Google OAuth
3. User authorize di Google
4. Callback dengan authorization code
5. Exchange code untuk access token
6. Get user info dari Google API
7. Check user by google_id → Update avatar
   OR
   Check user by email → Link Google account
   OR
   Create new user → Auto-verify email
8. Login user → Redirect to home
```

**Teknologi:**
- **OAuth**: Laravel Socialite (Google provider)
- **Account Linking**: Link Google ke existing email
- **Auto-verify**: Google email otomatis verified

**File terkait:**
- `app/Http/Controllers/AuthController.php` (handleGoogleCallback)
- `config/services.php` (Google OAuth config)

### 7.3 Product Creation & Image Upload

**Alur:**
```
1. User upload images (max 10)
2. For each image:
   - Upload ke Cloudinary (folder: reloved/products)
   - Get public_id dan secure_url
   - Store di product_images table
   - First image = is_primary = true
3. Create product record
4. Link images to product
```

**Teknologi:**
- **Image Upload**: Cloudinary API via `ImageService`
- **Optimization**: Auto-resize dan format optimization
- **Storage**: Cloudinary cloud storage

**File terkait:**
- `app/Http/Controllers/ProductController.php`
- `app/Services/ImageService.php`

### 7.4 Search & Filter System

**Alur:**
```
1. User input keyword → Full-text search
   - PostgreSQL to_tsvector (Indonesian)
   - plainto_tsquery untuk query
2. Fallback: ILIKE search jika full-text tidak menemukan
3. Apply filters (category, location, price, condition, brand)
4. Combine all filters dengan AND logic
5. Paginate results (20 per page)
```

**Teknologi:**
- **Full-Text Search**: PostgreSQL native full-text search
- **Language**: Indonesian (`to_tsvector('indonesian', ...)`)
- **Scopes**: Eloquent scopes untuk filters

**File terkait:**
- `app/Http/Controllers/ProductController.php` (index method)
- `app/Models/Product.php` (scopeSearch, scopeByCategory, dll)

### 7.5 Chat & Messaging (Polling Based)

**Alur:**
```
1. Buyer klik "Chat Penjual"
2. Create/find conversation
3. Load messages (latest first)
4. User send message → Create message record
5. Update last_message_at
6. Create notification untuk recipient
7. User refresh page untuk melihat messages baru
```

**Teknologi:**
- **Storage**: Messages stored di database
- **Update Method**: Page refresh (polling)
- **Broadcasting**: Disabled (log driver)

**File terkait:**
- `app/Http/Controllers/ChatController.php`
- `app/Models/Conversation.php`
- `app/Models/Message.php`

### 7.6 Offer & Negotiation System

**Alur:**
```
1. Buyer create offer:
   - Input amount (< harga produk)
   - Create offer record (status: pending)
   - Create message (type: offer)
   - Notify seller

2. Seller actions:
   - Accept: Update offer status → Reject other offers → Notify buyer
   - Reject: Update offer status → Notify buyer
   - Counter: Check counter limit → Validasi amount → Update offer → Notify buyer

3. Counter offer limit:
   - Max 3-5 kali (defined di Offer::MAX_COUNTER_COUNT)
   - After limit, buyer bisa chat tapi tidak bisa tawar lagi
```

**Teknologi:**
- **Offer Model**: Separate table untuk tracking
- **Counter Limit**: Constant di model
- **Status Management**: pending → accepted/rejected/counter_offer

**File terkait:**
- `app/Http/Controllers/OfferController.php`
- `app/Models/Offer.php`
- `app/Models/Message.php` (offer message type)

### 7.7 Transaction Flow (COD System)

**Alur Meet-up:**
```
1. Seller "Mark as Sold" → Select buyer
2. Create transaction (status: selesai, deal_method: meetup)
3. Decrement product stock
4. If stock = 0 → Update product status to "sold"
5. Transaction langsung selesai (tidak perlu tracking)
```

**Alur Shipping:**
```
1. Seller "Mark as Sold" → Select buyer
2. Create transaction (status: menunggu_transaksi, deal_method: shipping)
3. Decrement product stock
4. Seller "Barang dikirim" → Input tracking number
   → Update status: barang_dikirim
5. Buyer "Paket diterima"
   → Update status: paket_diterima
   → Auto complete: status: selesai
6. Review bisa dilakukan setelah "Paket diterima"
```

**Teknologi:**
- **Status Management**: Method di Transaction model
- **Stock Management**: Auto-decrement, restore saat cancel
- **Status Validation**: canBeShipped(), canBeReceived(), canBeCompleted()

**File terkait:**
- `app/Http/Controllers/TransactionController.php`
- `app/Models/Transaction.php`

### 7.8 Review & Rating System

**Alur:**
```
1. After transaction "Paket diterima" atau "Selesai"
2. Buyer/Seller bisa create review:
   - Input rating (1-5 stars)
   - Optional comment
   - Optional images (upload ke Cloudinary)
3. Create review record
4. Update user stats:
   - Calculate rating_avg (average dari semua reviews)
   - Update review_count
5. Display review di product & profile page
```

**Teknologi:**
- **Rating Calculation**: Auto-update setelah review created/updated/deleted
- **Image Upload**: Cloudinary (folder: reviews)
- **Unique Constraint**: Satu review per transaction per reviewer

**File terkait:**
- `app/Http/Controllers/ReviewController.php`
- `app/Models/Review.php`
- `app/Models/User.php` (rating_avg, review_count)

### 7.9 Homepage Recommendation Algorithm

**Alur:**
```
1. Produk Spotlight:
   - Scoring: view_count (40%) + favorite_count (30%) + seller_rating (20%) + recency (10%)
   - Filter: Available products, seller rating >= 4.0 atau review_count >= 3
   - Order by: spotlight_score DESC, created_at DESC
   - Limit: 5 products

2. Barang Terbaru:
   - Filter: Available products, created_at >= 7 days ago
   - Order by: created_at DESC
   - Limit: 5 products

3. Mungkin Kamu Suka Ini (Personalized):
   - If guest: Rekomendasi umum
   - If logged in:
     a. Prioritas 1: Kategori dari wishlist user
     b. Prioritas 2: Kategori dari produk populer
     c. Prioritas 3: Lokasi user (province/city)
     d. Fallback: Rekomendasi umum
```

**Teknologi:**
- **Scoring**: Raw SQL dengan weighted calculation
- **Recommendation**: Multi-priority fallback system
- **Scopes**: Eloquent scopes untuk each recommendation type

**File terkait:**
- `app/Http/Controllers/HomeController.php`
- `app/Models/Product.php` (scopeSpotlight, scopeLatestProducts, scopeRecommendedByCategory, dll)

---

## 8. Perbedaan dengan Plan Awal

### 8.1 Real-time Chat

**Plan Awal:**
- Laravel Reverb dengan WebSocket
- Real-time messaging tanpa page refresh
- Presence channels untuk user tracking

**Implementasi Aktual:**
- ⚠️ Reverb terinstall tapi **disabled** (no Redis)
- ⚠️ Broadcasting menggunakan `log` driver
- ⚠️ Chat menggunakan **polling** (page refresh)
- ⚠️ Message storage dan read status working

**Alasan:**
- Redis tidak tersedia di Laragon (default setup)
- Reverb membutuhkan Redis untuk scaling
- Solusi: Disable Reverb, gunakan polling

### 8.2 Session Management

**Plan Awal:**
- Database atau Redis session driver
- Session regeneration setelah login
- Session invalidation setelah logout

**Implementasi Aktual:**
- ✅ Database session driver (sesuai plan)
- ✅ Session regeneration working
- ✅ Session invalidation working

**Status**: ✅ **Sesuai plan**

### 8.3 Package Versions

**Plan Awal:**
- Laravel Reverb 1.0
- Predis 2.0
- Spatie Query Builder 5.2

**Implementasi Aktual:**
- Laravel Reverb 1.0 ⚠️
- Predis 3.2 (updated) ⚠️
- Spatie Query Builder 6.3 (updated) ⚠️

**Status**: ⚠️ **Updated versions** (compatible)

### 8.4 Frontend Assets

**Plan Awal:**
- Tailwind CSS via npm (no CDN) ✅
- Alpine.js via npm (no CDN) ✅
- Laravel Echo + Pusher JS ✅

**Implementasi Aktual:**
- ✅ Tailwind CSS via npm
- ✅ Alpine.js via npm
- ⚠️ Laravel Echo: Dummy object (no Reverb)

**Status**: ⚠️ **Echo disabled** karena Reverb disabled

### 8.5 Google OAuth

**Plan Awal:**
- Tidak disebutkan di plan awal

**Implementasi Aktual:**
- ✅ Google OAuth login implemented
- ✅ Account linking (link Google ke existing email)
- ✅ Auto-verify email untuk Google users

**Status**: ✅ **Bonus feature** (tidak di plan awal)

### 8.6 Reporting System

**Plan Awal:**
- Report barang, user, chat
- Report disimpan untuk tracking

**Implementasi Aktual:**
- ❌ **Belum diimplementasikan**

**Status**: ❌ **Pending implementation**

### 8.7 Seller Verification

**Plan Awal:**
- KTP Verification
- Phone Verification
- Badge system

**Implementasi Aktual:**
- ❌ **Belum diimplementasikan**

**Status**: ❌ **Pending implementation**

---

## 9. API Endpoints

### 9.1 Authentication Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/register` | AuthController@showRegister | Show registration form |
| POST | `/register` | AuthController@register | Register new user |
| GET | `/login` | AuthController@showLogin | Show login form |
| POST | `/login` | AuthController@login | Login user |
| POST | `/logout` | AuthController@logout | Logout user |
| GET | `/verify-email` | AuthController@showVerifyEmail | Show OTP verification form |
| POST | `/verify-email` | AuthController@verifyEmail | Verify OTP |
| POST | `/resend-otp` | AuthController@resendOTP | Resend OTP |
| GET | `/auth/google` | AuthController@redirectToGoogle | Redirect to Google OAuth |
| GET | `/auth/google/callback` | AuthController@handleGoogleCallback | Google OAuth callback |

### 9.2 Product Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/products` | ProductController@index | List products (with filters) |
| GET | `/products/{id}` | ProductController@show | Show product detail |
| GET | `/products/create` | ProductController@create | Show create form (auth) |
| POST | `/products` | ProductController@store | Create product (auth) |
| GET | `/products/{id}/edit` | ProductController@edit | Show edit form (auth) |
| PUT | `/products/{id}` | ProductController@update | Update product (auth) |
| DELETE | `/products/{id}` | ProductController@destroy | Delete product (auth) |
| DELETE | `/products/{id}/images/{imageId}` | ProductController@deleteImage | Delete product image (auth) |

### 9.3 Chat Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/chat` | ChatController@index | List conversations (auth) |
| GET | `/chat/create` | ChatController@create | Create conversation (auth) |
| GET | `/chat/{id}` | ChatController@show | Show conversation (auth) |
| POST | `/chat/{id}` | ChatController@store | Send message (auth) |
| POST | `/chat/{id}/read` | ChatController@markAsRead | Mark as read (auth) |
| GET | `/api/products/{productId}/buyers` | ChatController@getBuyersForProduct | Get buyers list (auth) |

### 9.4 Offer Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| POST | `/chat/{conversationId}/offers` | OfferController@store | Create offer (auth) |
| POST | `/chat/{conversationId}/offers/{messageId}/accept` | OfferController@accept | Accept offer (auth) |
| POST | `/chat/{conversationId}/offers/{messageId}/reject` | OfferController@reject | Reject offer (auth) |
| POST | `/chat/{conversationId}/offers/{messageId}/counter` | OfferController@counter | Counter offer (auth) |

### 9.5 Transaction Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/transactions` | TransactionController@index | List transactions (auth) |
| GET | `/transactions/{id}` | TransactionController@show | Show transaction detail (auth) |
| POST | `/transactions` | TransactionController@store | Create transaction (auth) |
| POST | `/transactions/{id}/shipping` | TransactionController@updateShipping | Update shipping (auth) |
| POST | `/transactions/{id}/received` | TransactionController@markAsReceived | Mark as received (auth) |
| POST | `/transactions/{id}/cancel` | TransactionController@cancel | Cancel transaction (auth) |

### 9.6 Review Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/reviews` | ReviewController@index | List reviews |
| GET | `/reviews/create/{transactionId}` | ReviewController@create | Show create form (auth) |
| POST | `/reviews` | ReviewController@store | Create review (auth) |
| GET | `/reviews/{id}` | ReviewController@show | Show review |
| GET | `/reviews/{id}/edit` | ReviewController@edit | Show edit form (auth) |
| PUT | `/reviews/{id}` | ReviewController@update | Update review (auth) |
| DELETE | `/reviews/{id}` | ReviewController@destroy | Delete review (auth) |

### 9.7 Wishlist Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/wishlist` | WishlistController@index | List wishlist (auth) |
| POST | `/wishlist/{productId}/toggle` | WishlistController@toggle | Toggle favorite (auth) |
| DELETE | `/wishlist/{id}` | WishlistController@destroy | Remove from wishlist (auth) |
| GET | `/api/wishlist/{productId}/check` | WishlistController@check | Check favorite status (auth) |

### 9.8 Notification Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/notifications` | NotificationController@index | List notifications (auth) |
| POST | `/notifications/{id}/read` | NotificationController@markAsRead | Mark as read (auth) |
| POST | `/notifications/read-all` | NotificationController@markAllAsRead | Mark all as read (auth) |
| DELETE | `/notifications/{id}` | NotificationController@destroy | Delete notification (auth) |
| GET | `/api/notifications/unread-count` | NotificationController@unreadCount | Get unread count (auth) |

### 9.9 Shipping API Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/api/shipping/provinces` | ShippingController@getProvinces | Get provinces list |
| GET | `/api/shipping/cities` | ShippingController@getCities | Get cities list |
| POST | `/api/shipping/check-cost` | ShippingController@checkCost | Calculate shipping cost |
| POST | `/api/shipping/city-id` | ShippingController@getCityId | Get city ID by name |

### 9.10 Category Routes

| Method | Route | Controller | Purpose |
|--------|-------|------------|---------|
| GET | `/categories/{slug}` | CategoryController@show | Show category page |

---

## 10. Security & Best Practices

### 10.1 Authentication Security

- ✅ **Password Hashing**: bcrypt (Laravel default)
- ✅ **Session Security**: Database session, regenerate after login
- ✅ **CSRF Protection**: Laravel CSRF tokens
- ✅ **Email Verification**: Required sebelum login
- ✅ **OTP Security**: 6 digit random, 15 menit expiry, one-time use

### 10.2 Authorization

- ✅ **Middleware**: `auth` middleware untuk protected routes
- ✅ **Authorization Checks**: Manual checks di controllers (abort 403)
- ✅ **Resource Ownership**: Check user ownership sebelum edit/delete

### 10.3 Input Validation

- ✅ **Form Requests**: Dedicated request classes untuk validation
- ✅ **Database Constraints**: Unique constraints, foreign keys
- ✅ **SQL Injection Prevention**: Eloquent ORM (prepared statements)

### 10.4 Data Protection

- ✅ **Soft Deletes**: users, products, transactions
- ✅ **UUID Primary Keys**: Prevent enumeration attacks
- ✅ **Sensitive Data**: Password tidak disimpan plain text

### 10.5 File Upload Security

- ✅ **Image Validation**: File type, size validation
- ✅ **Cloudinary Storage**: External storage (tidak di server)
- ✅ **Image Optimization**: Auto-resize dan format optimization

### 10.6 API Security

- ✅ **Rate Limiting**: Laravel default rate limiting
- ✅ **CORS**: Configured untuk localhost
- ✅ **Input Sanitization**: Laravel automatic sanitization

---

## 11. Known Issues & Limitations

### 11.1 Real-time Features

**Issue**: Chat tidak real-time, menggunakan polling  
**Impact**: User harus refresh page untuk melihat messages baru  
**Workaround**: Refresh page secara manual  
**Future Fix**: Setup Redis dan enable Reverb

### 11.2 Broadcasting

**Issue**: Broadcasting menggunakan `log` driver  
**Impact**: Events tidak broadcast ke clients  
**Workaround**: N/A  
**Future Fix**: Enable Reverb dengan Redis

### 11.3 Session Management

**Issue**: Session menggunakan database (bukan Redis)  
**Impact**: Slightly slower than Redis, but acceptable  
**Status**: ✅ Working as intended

### 11.4 Image Upload

**Issue**: Tidak ada image compression sebelum upload  
**Impact**: Upload time mungkin lebih lama untuk large images  
**Status**: ✅ Cloudinary handles optimization

---

## 12. Development Notes

### 12.1 Localhost Setup

- **Database**: PostgreSQL via Laragon
- **Session**: Database driver (sessions table)
- **Cache**: File cache (no Redis)
- **Queue**: Sync driver (no queue worker)

### 12.2 Environment Variables

**Required `.env` variables:**
```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=preloved_marketplace
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database

# Broadcasting (disabled)
BROADCAST_DRIVER=log

# Cloudinary
CLOUDINARY_URL=cloudinary://...

# RajaOngkir
RAJAONGKIR_API_KEY=...

# Google OAuth
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

### 12.3 Database Setup

```bash
# Run migrations
php artisan migrate

# Run seeders (if available)
php artisan db:seed
```

### 12.4 Asset Compilation

```bash
# Install dependencies
npm install

# Development
npm run dev

# Production build
npm run build
```

---

## 13. Testing Checklist

### 13.1 Authentication ✅
- [x] User registration
- [x] Email OTP verification
- [x] Login/Logout
- [x] Google OAuth login
- [x] Session management

### 13.2 Product Management ⚠️
- [x] Create product
- [x] Upload multiple images ⚠️
- [x] Edit product
- [x] Delete product ⚠️
- [x] Mark as sold
- [x] Stock management ⚠️

### 13.3 Search & Filter ⚠️
- [x] Full-text search
- [x] Category filter
- [x] Location filter ⚠️
- [x] Price range filter ⚠️
- [x] Combined filters ⚠️

### 13.4 Chat & Offers ⚠️
- [x] Create conversation⚠️
- [x] Send message⚠️
- [x] Create offer⚠️
- [x] Accept/reject offer⚠️
- [x] Counter offer⚠️
- [x] Counter limit⚠️

### 13.5 Transactions ⚠️
- [x] Create transaction⚠️
- [x] Shipping flow⚠️
- [x] Mark as received⚠️
- [x] Cancel transaction⚠️
- [x] Stock restore⚠️

### 13.6 Reviews (Tidak dibuat)
- [x] Create review
- [x] Upload review images
- [x] Edit review
- [x] Delete review
- [x] Rating calculation

---

## 14. Future Improvements

### 14.1 High Priority
1. **Enable Real-time Chat**: Setup Redis, enable Reverb
2. **Reporting System**: Implement report functionality
3. **Seller Verification**: KTP and phone verification

### 14.2 Medium Priority
1. **Email Notifications**: Send email untuk important events
2. **Analytics Dashboard**: User statistics dan insights
3. **Product Sharing**: Social media sharing

### 14.3 Low Priority
1. **Promo/Voucher System**: Discount codes
2. **Advanced Search**: More search filters
3. **Mobile App**: Native mobile application

---

## 15. Conclusion

**Reloved Marketplace** adalah platform C2C marketplace yang sudah memiliki **core features** lengkap untuk operasi dasar jual beli barang preloved. Platform ini menggunakan **COD system** tanpa escrow, dengan fokus pada **tracking** dan **communication** antara buyer dan seller.

### Status Summary
- ✅ **Core Features**: 40% complete
- ⚠️ **Real-time Features**: Disabled (no Redis)
- ❌ **Reporting System**: Not implemented
- ❌ **Seller Verification**: Not implemented

### Ready for
- ✅ Localhost development
- ✅ Testing dan demo
- ✅ College assignment submission

### Not Ready for
- ❌ Production deployment 
- ❌ Real-time features

---

**Last Updated**: 10 Nov 2025
**Documentation Version**: 1.0  
**Project Status**: Under Develpment

