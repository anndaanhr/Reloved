# Reloved Marketplace - Dokumentasi Lengkap

**Platform C2C Marketplace untuk Jual Beli Barang Preloved**

---

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Tech Stack & Environment](#tech-stack--environment)
3. [Struktur Projek](#struktur-projek)
4. [Database Schema](#database-schema)
5. [Fitur Utama](#fitur-utama)
6. [Alur Aplikasi](#alur-aplikasi)
7. [Setup & Installation](#setup--installation)
8. [API Endpoints](#api-endpoints)
9. [Services & Integrations](#services--integrations)
10. [Security & Best Practices](#security--best-practices)
11. [Catatan Penting](#catatan-penting)

---

## Overview

### Deskripsi Projek

**Reloved Marketplace** adalah platform C2C (Consumer-to-Consumer) marketplace untuk jual beli barang preloved. Platform ini menghubungkan pembeli dan penjual dengan sistem **COD (Cash on Delivery)** tanpa escrow atau payment gateway.

### Karakteristik Utama

- ✅ **C2C Marketplace**: Platform untuk jual beli barang preloved antar konsumen
- ✅ **COD System**: Pembayaran langsung antara buyer-seller, platform hanya tracking
- ✅ **No Admin Panel**: Tidak ada admin untuk review atau manage konten
- ✅ **Localhost Only**: Dikembangkan untuk localhost (Laragon), tidak ada deployment
- ✅ **Design Based**: UI mengikuti Figma design yang sudah dibuat
- ✅ **UX Reference**: Flow dan interaksi mengikuti Carousell

---

## Tech Stack & Environment

### Backend

| Komponen | Teknologi | Versi | Status |
|----------|-----------|-------|--------|
| Framework | Laravel | 10.x | ✅ |
| Database | PostgreSQL | 15+ | ✅ |
| Authentication | Laravel Session | - | ✅ |
| OAuth | Laravel Socialite | 5.23 | ✅ |
| Real-time | Laravel Reverb | 1.0 | ✅ Active (with Redis) |

### Frontend

| Komponen | Teknologi | Versi | Status |
|----------|-----------|-------|--------|
| Template Engine | Blade | - | ✅ |
| CSS Framework | Tailwind CSS | 3.4.18 | ✅ |
| JavaScript | Alpine.js | 3.15.1 | ✅ |
| Build Tool | Vite | 5.0.0 | ✅ |
| Real-time | Laravel Echo | 1.19.0 | ✅ Used for real-time chat |

### Database & Storage

| Komponen | Teknologi | Status |
|----------|-----------|--------|
| Database | PostgreSQL 15+ (via Laragon) | ✅ |
| File Storage | Cloudinary | ✅ |
| Session Storage | Database | ✅ |
| Cache | Redis | ✅ |
| Broadcasting | Redis | ✅ |

### External APIs

| Service | Purpose | Status |
|---------|---------|--------|
| **Cloudinary** | Image storage & CDN | ✅ |
| **RajaOngkir** | Shipping cost calculation | ✅ |
| **Wilayah.id** | Indonesian regions data (free API) | ✅ |
| **Gmail SMTP** | Email OTP & notifications | ✅ |
| **Google OAuth** | Social login | ✅ |

### Development Environment

- **OS**: Windows
- **Local Server**: Laragon
- **Database Client**: HeidiSQL (via Laragon)
- **PHP Version**: 8.1+
- **Node.js**: Via Laragon
- **Composer**: Via Laragon

---

## Struktur Projek

```
preloved-marketplace/
├── app/
│   ├── Console/
│   │   └── Kernel.php
│   ├── Events/
│   │   └── MessageSent.php           # Broadcasting event untuk chat
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/               # 16 controllers
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ChatController.php
│   │   │   ├── OfferController.php
│   │   │   ├── TransactionController.php
│   │   │   ├── ReviewController.php
│   │   │   ├── WishlistController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ShippingController.php
│   │   │   ├── PageController.php
│   │   │   ├── HomeController.php
│   │   │   └── Auth/
│   │   │       ├── ForgotPasswordController.php
│   │   │       └── ResetPasswordController.php
│   │   ├── Middleware/                # 9 middleware
│   │   ├── Requests/                  # Form validation
│   │   │   ├── ProductRequest.php
│   │   │   ├── ReviewRequest.php
│   │   │   └── UpdateProfileRequest.php
│   │   └── Kernel.php
│   ├── Mail/
│   │   ├── ContactMail.php           # Contact form email
│   │   └── OTPMail.php               # OTP verification email
│   ├── Models/                        # 12 models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── ProductImage.php
│   │   ├── Category.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── Offer.php
│   │   ├── Transaction.php
│   │   ├── Review.php
│   │   ├── Wishlist.php
│   │   ├── Notification.php
│   │   └── EmailVerification.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── TelescopeServiceProvider.php
│   └── Services/                      # 5 service classes
│       ├── ImageService.php          # Cloudinary integration
│       ├── RajaOngkirService.php     # Shipping API
│       ├── WilayahIndonesiaService.php # Free region API
│       ├── NotificationService.php   # Notification helper
│       └── ChatService.php           # Chat automation
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/                    # 24 migrations
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── CategorySeeder.php
├── resources/
│   ├── css/
│   │   └── app.css                   # Tailwind CSS
│   ├── js/
│   │   ├── app.js                    # Alpine.js + Echo setup
│   │   └── bootstrap.js
│   └── views/
│       ├── auth/                      # 6 auth views
│       ├── categories/
│       ├── chat/                      # 2 chat views
│       ├── emails/                    # 3 email templates
│       ├── layouts/
│       │   ├── app.blade.php         # Main layout
│       │   └── auth.blade.php        # Auth layout
│       ├── pages/                     # 10 static pages
│       ├── products/                  # 4 product views
│       ├── profile/                   # 2 profile views
│       ├── reviews/                   # 4 review views
│       ├── transactions/              # 2 transaction views
│       ├── wishlist/
│       ├── notifications/
│       ├── home.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php                        # Main routes
├── config/                            # Laravel configuration
├── public/
│   ├── images/                        # Static images
│   │   ├── blog1.png, blog2.png, blog3.png
│   │   ├── logo/
│   │   └── kategorisection/
│   └── index.php
└── storage/                           # File storage, logs, cache
```

---

## Database Schema

### Tabel Utama

#### 1. `users`
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Fields**:
  - `name`, `email`, `phone`, `password`
  - `avatar` (Cloudinary URL)
  - `province`, `city` (text, not FK)
  - `rating_avg` (decimal 3,2), `review_count` (integer)
  - `google_id` (untuk OAuth)
  - `email_verified_at`
- **Relationships**:
  - `hasMany` Product
  - `hasMany` Transaction (as buyer & seller)
  - `hasMany` Conversation
  - `hasMany` Review
  - `hasMany` Wishlist

#### 2. `products`
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Fields**:
  - `user_id`, `category_id`
  - `title`, `slug`, `description`
  - `price` (decimal 10,2)
  - `condition` (enum: baru, seperti_baru, bekas_bagus, bekas_cukup)
  - `brand`, `size`, `model`
  - `stock` (integer)
  - `deal_method` (json array: meetup, shipping)
  - `status` (enum: active, sold)
  - `view_count`, `favorite_count`
  - **Dynamic attributes**: `expired_date`, `weight`, `author`, `publisher`, `year`
- **Relationships**:
  - `belongsTo` User, Category
  - `hasMany` ProductImage, Transaction, Review, Wishlist

#### 3. `product_images`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `product_id`
  - `cloudinary_public_id`, `cloudinary_url`
  - `is_primary` (boolean)
  - `order` (integer)
- **Relationships**:
  - `belongsTo` Product

#### 4. `categories`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `name`, `slug`
  - `parent_id` (nullable, untuk sub-kategori)
  - `description`, `icon` (nullable)
  - `is_active` (boolean)
- **Relationships**:
  - Self-referencing `parent` (hasMany children)
  - `hasMany` Product

#### 5. `conversations`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `product_id` (nullable), `buyer_id`, `seller_id`
  - `last_message_at` (datetime, nullable)
- **Relationships**:
  - `belongsTo` Product (nullable), User (buyer & seller)
  - `hasMany` Message, Offer

#### 6. `messages`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `conversation_id`, `sender_id`
  - `message` (text, nullable)
  - `message_type` (enum: text, offer, image, receipt)
  - `offer_amount` (decimal 10,2, nullable, untuk message_type = offer)
  - `offer_status` (enum: pending, accepted, rejected, counter_offer, nullable)
  - `offer_counter_count` (integer, default 0)
  - `is_read` (boolean, default false)
- **Relationships**:
  - `belongsTo` Conversation, User (sender)

#### 7. `offers`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `conversation_id`, `product_id`
  - `buyer_id`, `seller_id`
  - `amount` (decimal 10,2)
  - `status` (enum: pending, accepted, rejected, counter_offer)
  - `counter_count` (integer) - jumlah counter-offer yang sudah dilakukan
- **Relationships**:
  - `belongsTo` Conversation, Product, User (buyer & seller)

#### 8. `transactions`
- **Primary Key**: `id` (UUID)
- **Soft Deletes**: ✅
- **Fields**:
  - `product_id`, `buyer_id`, `seller_id`
  - `price` (decimal 10,2)
  - `deal_method` (enum: meetup, shipping)
  - `status` (enum: menunggu_transaksi, barang_dikirim, paket_diterima, selesai, dibatalkan)
  - `meetup_location` (nullable)
  - `shipping_courier`, `shipping_service`
  - `shipping_cost` (decimal 10,2, nullable)
  - `tracking_number` (nullable)
  - `origin_city_id`, `origin_city_name`
  - `destination_city_id`, `destination_city_name`
  - Timestamps: `seller_confirmed_at`, `shipping_confirmed_at`, `received_confirmed_at`, `completed_at`
- **Relationships**:
  - `belongsTo` Product, User (buyer & seller)
  - `hasMany` Review

#### 9. `reviews`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `transaction_id`, `reviewer_id`, `reviewed_id`, `product_id`
  - `rating` (integer 1-5)
  - `comment` (text, nullable)
  - `images` (json array, nullable)
- **Unique Constraint**: `(reviewer_id, transaction_id)` - satu review per transaksi
- **Relationships**:
  - `belongsTo` Transaction, User (reviewer & reviewed), Product

#### 10. `favorites` (Wishlist)
- **Table Name**: `favorites` (model menggunakan `Wishlist`)
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `user_id`, `product_id`
- **Unique Constraint**: `(user_id, product_id)`
- **Relationships**:
  - `belongsTo` User, Product

#### 11. `notifications`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `user_id`, `type`, `title`, `message`
  - `notifiable_id`, `notifiable_type` (polymorphic)
  - `is_read` (boolean)
  - `read_at` (datetime, nullable)
- **Types**: `chat`, `offer`, `transaction`, `review`
- **Relationships**:
  - `belongsTo` User
  - `morphTo` notifiable (polymorphic)

#### 12. `email_verifications`
- **Primary Key**: `id` (UUID)
- **Fields**:
  - `email`, `otp` (6 digits)
  - `expires_at` (datetime)
  - `is_used` (boolean)
- **Note**: Tidak ada FK ke users, karena OTP dibuat sebelum user verified
- **Methods**:
  - `generate($email)`: Generate OTP baru (hapus OTP lama untuk email yang sama)
  - `isValid()`: Cek apakah OTP masih valid (belum digunakan & belum expired)
  - `markAsUsed()`: Tandai OTP sebagai sudah digunakan

### Indexes & Constraints

- UUID primary keys di semua tabel
- Foreign key constraints dengan `onDelete('cascade')`
- Unique constraints:
  - `users`: `email`, `phone`
  - `products`: `slug`
  - `reviews`: `(reviewer_id, transaction_id)`
  - `favorites`: `(user_id, product_id)`
- Indexes pada:
  - `users`: `email`, `phone` (unique indexes)
  - `products`: `user_id`, `category_id`, `status`, `slug` (unique), `stock`, `created_at`, `price`
  - `transactions`: `buyer_id`, `seller_id`, `product_id`, `status`
  - `conversations`: `product_id` (nullable), `buyer_id`, `seller_id`, `last_message_at`
  - `messages`: `conversation_id`, `created_at`
  - `reviews`: `reviewer_id`, `reviewed_id`, `product_id`, `transaction_id` (unique)
  - `product_images`: `product_id`, `order`
  - `email_verifications`: `email`, `otp`, `expires_at`
  - `notifications`: `user_id`, `is_read`, `created_at`

---

## Fitur Utama

### 1. Authentication & User Management

#### Registrasi & Login
- ✅ Email/Password registration
- ✅ Email OTP verification (6 digits, 15 menit expiry)
- ✅ Google OAuth login
- ✅ Password reset via email
- ✅ Session management (database driver)

#### Profile Management
- ✅ Edit profil (nama, phone, lokasi, avatar)
- ✅ Upload avatar ke Cloudinary
- ✅ Lokasi (province & city sebagai text, bukan FK)
- ✅ Rating & review count otomatis

### 2. Product Management

#### Create & Edit Product
- ✅ Upload maksimal 10 gambar (gambar pertama otomatis menjadi primary)
- ✅ 2-level category selection (parent → sub-category via AJAX)
- ✅ Dynamic attributes berdasarkan kategori:
  - Fashion: brand, size, model
  - Makanan: expired_date, weight
  - Buku: author, publisher, year
  - Dll
- ✅ Kondisi produk: `baru`, `seperti_baru`, `bekas_bagus`, `bekas_cukup`
- ✅ Pilih metode deal: Meet-up, Shipping, atau keduanya (JSON array)
- ✅ Stock management (auto-decrement saat transaksi, auto-hide jika stock = 0)
- ✅ Delete image dengan validasi minimal 1 gambar tersisa
- ✅ Auto-set primary image baru jika primary image dihapus

#### Product Display
- ✅ Homepage dengan 3 section:
  - **Produk Spotlight**: Algoritma scoring (view_count 40%, favorite_count 30%, seller rating 20%, recency 10%) - hanya dari seller dengan rating >= 4.0 atau review_count >= 3
  - **Barang Terbaru**: Produk terbaru dalam 7 hari terakhir
  - **Mungkin Kamu Suka Ini**: Rekomendasi personalisasi berdasarkan wishlist, kategori populer, atau lokasi user (fallback ke rekomendasi umum)
- ✅ Product detail dengan galeri gambar
- ✅ Increment view_count otomatis saat produk dilihat
- ✅ Search dengan PostgreSQL Full-Text Search (Indonesian language) dengan fallback ILIKE
- ✅ Filter: kategori, lokasi (province/city), harga, kondisi, brand
- ✅ Sorting: latest, price_low, price_high, popular
- ✅ Related products dari seller yang sama (maksimal 4 produk)
- ✅ Reviews display (5 review terbaru)
- ✅ Wishlist status check

#### Mark as Sold
- ✅ Seller bisa mark produk sebagai "sold" (hanya jika status = active)
- ✅ Pilih buyer dari list yang pernah chat/offer untuk produk tersebut
- ✅ Otomatis create transaction dengan deal_method yang dipilih
- ✅ Stock otomatis berkurang (decrementStock)
- ✅ Produk otomatis hidden jika stock = 0
- ✅ Validasi: buyer harus sudah pernah chat/offer, buyer tidak boleh sama dengan seller

### 3. Chat System

#### Conversation
- ✅ Buyer bisa chat seller dari product detail
- ✅ Satu conversation per product (buyer + seller)
- ✅ List semua conversation user (diurutkan berdasarkan last_message_at)
- ✅ Unread message counter (hanya untuk message_type != 'offer', offer messages masuk ke notifications)
- ✅ Auto-mark as read saat conversation dibuka
- ✅ Seller bisa lihat list buyers yang pernah chat/offer untuk produk mereka (untuk "Mark as Sold")

#### Messaging
- ✅ **Real-time** dengan Laravel Reverb & Redis
- ✅ WebSocket connection untuk instant messaging
- ✅ Laravel Echo untuk client-side real-time updates
- ✅ Broadcasting menggunakan Redis pub/sub
- ✅ MessageSent event di-broadcast ke private channel
- ✅ Text messages
- ✅ Image messages
- ✅ Receipt messages (otomatis saat seller konfirmasi shipping)
- ✅ Read status tracking
- ✅ Timestamp dengan timezone Asia/Jakarta

#### How Real-time Chat Works
1. User mengirim pesan → `ChatController@store` membuat `Message`
2. `MessageSent` event di-broadcast ke private channel `conversation.{id}`
3. Redis pub/sub mengirim event ke Reverb server
4. Reverb server mengirim ke connected clients via WebSocket
5. Laravel Echo di client menerima event dan update UI real-time
6. Tidak perlu refresh halaman untuk melihat pesan baru

### 4. Offer & Negotiation

#### Offer Flow
- ✅ Buyer bisa buat tawaran harga
- ✅ Seller bisa terima/tolak/tawar balik
- ✅ Buyer bisa counter-offer setelah seller counter
- ✅ Maksimal counter-offer: 5 kali (didefinisikan di `Offer::MAX_COUNTER_COUNT`)
- ✅ Status tracking: pending, accepted, rejected, counter_offer

#### Offer UI
- ✅ Custom modal (bukan browser popup)
- ✅ Button states (disabled setelah accept/reject)
- ✅ Badge notifikasi untuk offer baru

### 5. Transaction Management

#### Transaction Flow

**Meet-up (COD):**
1. Seller buat transaksi dari "Mark as Sold" (pilih deal_method = meetup)
2. Status langsung `selesai` (completed_at di-set)
3. Buyer & seller bisa review

**Shipping:**
1. Seller buat transaksi → Status: `menunggu_transaksi`
2. Seller konfirmasi pengiriman → Status: `barang_dikirim`
   - Optional: Input tracking number
   - Jika ada tracking: Otomatis kirim receipt ke chat
3. Buyer terima pesanan → Status: `paket_diterima`
4. Buyer review → Status: `selesai`

#### Transaction Features
- ✅ Transaction list untuk buyer & seller
- ✅ Transaction detail dengan tracking info
- ✅ Cancel transaction (dengan validasi)
- ✅ Stock otomatis restore jika dibatalkan
- ✅ Shipping cost calculation (RajaOngkir)

### 6. Review & Rating

#### Review System
- ✅ Review bisa dibuat setelah status `paket_diterima` atau `selesai`
- ✅ Rating 1-5 bintang
- ✅ Optional comment & images
- ✅ Satu review per transaksi (per reviewer)
- ✅ Auto-update user rating_avg & review_count

#### Review Display
- ✅ Review list di product detail
- ✅ Review list di user profile
- ✅ Edit & delete review (hanya reviewer sendiri)

### 7. Wishlist / Favorites

- ✅ Add/remove produk ke wishlist
- ✅ Wishlist page dengan list produk
- ✅ Favorite count di produk
- ✅ Badge di product card jika sudah di wishlist

### 8. Notification System

- ✅ Notifikasi untuk:
  - New offer (price negotiation)
  - Transaction updates (created, shipped, received, cancelled)
  - Review received
  - **Note**: Chat messages tidak masuk ke notifications (menggunakan badge terpisah di header)
- ✅ Unread counter di header (hanya untuk offer & transaction, bukan chat)
- ✅ Mark as read / mark all as read
- ✅ Notification list page (paginated, 15 per page)
- ✅ Delete notification

### 9. Shipping Integration

#### Wilayah.id (Free API)
- ✅ Get provinces
- ✅ Get cities by province
- ✅ Get districts by city
- ✅ Digunakan untuk form alamat & shipping

#### RajaOngkir
- ✅ Shipping cost calculation
- ✅ Multiple courier support (JNE, J&T, SiCepat, TIKI, POS)
- ✅ Rate limit handling & caching
- ⚠️ **Daily limit**: 250 requests/day (free tier)
- ✅ Caching untuk mengurangi API calls

### 10. Static Pages

- ✅ About Us (`/tentang-kami`)
- ✅ Career (`/karir`)
- ✅ Blog (`/blog`, `/blog/{id}`)
- ✅ Help Center (`/pusat-bantuan`)
- ✅ Contact (`/hubungi-kami`) dengan form email
- ✅ FAQ (`/faq`)
- ✅ Terms & Conditions (`/syarat-ketentuan`)
- ✅ Privacy Policy (`/kebijakan-privasi`)
- ✅ Return Policy (`/kebijakan-pengembalian`)

---

## Alur Aplikasi

### Flow 1: Buyer Membeli Barang (Meet-up)

```
1. Buyer browse/search produk
2. Buyer lihat detail produk
3. Buyer chat seller (optional, bisa langsung deal)
4. Buyer/Seller nego harga (optional)
5. Seller mark as sold → pilih buyer
6. Transaction dibuat (status: selesai)
7. Buyer & seller review masing-masing
8. Selesai
```

### Flow 2: Buyer Membeli Barang (Shipping)

```
1. Buyer browse/search produk
2. Buyer lihat detail produk
3. Buyer chat seller
4. Buyer/Seller nego harga
5. Buyer cek ongkir (RajaOngkir)
6. Seller mark as sold → pilih buyer
7. Transaction dibuat (status: menunggu_transaksi)
8. Seller konfirmasi pengiriman:
   - Input tracking number (optional)
   - Jika ada tracking → otomatis kirim receipt ke chat
   - Status: barang_dikirim
9. Buyer terima pesanan → Status: paket_diterima
10. Buyer review → Status: selesai
11. Seller bisa review buyer
12. Selesai
```

### Flow 3: Offer & Negotiation

```
1. Buyer buat tawaran harga
2. Message type: "offer" dibuat di chat
3. Seller lihat tawaran:
   - Terima → Transaction dibuat
   - Tolak → Offer ditolak
   - Tawar balik → Counter-offer dibuat
4. Jika counter-offer:
   - Buyer bisa terima/tolak/tawar lagi
   - Bisa beberapa kali counter (terbatas)
5. Jika diterima → Transaction dibuat
```

---

## Setup & Installation

### Prerequisites

1. **Laragon** (Windows)
   - PostgreSQL 15+
   - PHP 8.1+
   - Composer
   - Node.js & NPM
   - **Redis** (untuk real-time chat & broadcasting)

2. **Database**: Buat database `preloved_marketplace` di Laragon

3. **Redis Server**: Pastikan Redis server berjalan (port 6379 default)

4. **External Services**:
   - Cloudinary account (image storage)
   - RajaOngkir API key (shipping)
   - Gmail SMTP (email)

### Installation Steps

```bash
# 1. Clone atau copy project ke folder lokal

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env file:
# - DB_DATABASE=preloved_marketplace
# - DB_USERNAME=root
# - DB_PASSWORD=(kosong atau sesuai Laragon)
# - REDIS_HOST=127.0.0.1
# - REDIS_PASSWORD=null
# - REDIS_PORT=6379
# - BROADCAST_DRIVER=reverb (atau redis)
# - CACHE_DRIVER=redis
# - REVERB_APP_KEY, REVERB_APP_SECRET, REVERB_APP_ID
# - VITE_ENABLE_REVERB=true
# - VITE_REVERB_APP_KEY, VITE_REVERB_HOST, VITE_REVERB_PORT
# - CLOUDINARY_URL=(dari Cloudinary dashboard)
# - RAJAONGKIR_API_KEY=(dari RajaOngkir)
# - MAIL_* (Gmail SMTP settings)
# - GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET (untuk OAuth)

# 6. Run migrations
php artisan migrate

# 7. Seed database (kategori)
php artisan db:seed

# 8. Build assets
npm run build

# 9. Link storage (jika perlu)
php artisan storage:link
```

### Running the Project

```powershell
# Pastikan Redis server berjalan terlebih dahulu
# (Laragon biasanya sudah include Redis, atau install terpisah)

# Terminal 1: Laravel Reverb Server (untuk real-time chat)
php artisan reverb:start

# Terminal 2: Laravel Server
php artisan serve

# Terminal 3: Vite Dev Server (untuk development dengan hot reload)
npm run dev
```

Access: `http://localhost:8000`

**Catatan**: Reverb server harus berjalan untuk fitur chat real-time berfungsi.

### Development Commands

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build

# Run migrations
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Create migration
php artisan make:migration create_table_name

# Create controller
php artisan make:controller ControllerName

# Create model
php artisan make:model ModelName -m

# Tinker (interactive shell)
php artisan tinker
```

---

## API Endpoints

### Public Routes

#### Authentication
- `GET /register` - Registration form
- `POST /register` - Submit registration
- `GET /login` - Login form
- `POST /login` - Submit login
- `GET /auth/google` - Google OAuth redirect
- `GET /auth/google/callback` - Google OAuth callback
- `GET /verify-email` - Email verification form
- `POST /verify-email` - Submit OTP
- `POST /resend-otp` - Resend OTP
- `GET /forgot-password` - Forgot password form
- `POST /forgot-password` - Send reset link
- `GET /reset-password/{token}` - Reset password form
- `POST /reset-password` - Submit new password

#### Products
- `GET /products` - Product list (dengan search & filter)
- `GET /products/{id}` - Product detail
- `GET /categories/{slug}` - Category page

#### Shipping (Public API)

**Wilayah.id (Free API)**:
- `GET /api/shipping/provinces` - Get all provinces
- `GET /api/shipping/cities?province_id={id}` - Get regencies (kabupaten/kota) by province
- `GET /api/shipping/districts?regency_id={id}` - Get districts (kecamatan) by regency
- `GET /api/shipping/search-provinces?query={name}` - Search provinces by name
- `GET /api/shipping/search-regencies?query={name}&province_id={id}` - Search regencies by name

**RajaOngkir (Shipping Cost Only)**:
- `POST /api/shipping/check-cost` - Calculate shipping cost (legacy)
- `POST /api/shipping/calculate-cost` - Calculate shipping cost (API V2)
- `POST /api/shipping/subdistrict-id` - Get subdistrict ID from city name
- `GET /api/shipping/search-destinations?query={name}&limit={n}` - Search destinations (API V2)

**Legacy (Backward Compatibility)**:
- `POST /api/shipping/city-id` - Get city ID (legacy)
- `POST /api/shipping/search-cities` - Search cities (legacy)

#### Static Pages
- `GET /tentang-kami` - About page
- `GET /karir` - Career page
- `GET /blog` - Blog list
- `GET /blog/{id}` - Blog detail
- `GET /pusat-bantuan` - Help center
- `GET /hubungi-kami` - Contact page
- `POST /hubungi-kami` - Submit contact form
- `GET /faq` - FAQ page
- `GET /syarat-ketentuan` - Terms page
- `GET /kebijakan-privasi` - Privacy page
- `GET /kebijakan-pengembalian` - Return policy page

### Authenticated Routes

#### Profile
- `GET /profile` - View profile
- `GET /profile/edit` - Edit profile form
- `PUT /profile` - Update profile

#### Products (Seller)
- `GET /products/create` - Create product form
- `POST /products` - Submit product
- `GET /products/{id}/edit` - Edit product form
- `PUT /products/{id}` - Update product
- `DELETE /products/{id}` - Delete product
- `DELETE /products/{id}/images/{imageId}` - Delete image
- `GET /api/products/sub-categories?parent_id={id}` - Get sub-categories

#### Chat
- `GET /chat` - Chat list
- `GET /chat/create?product_id={id}` - Start chat
- `GET /chat/{id}` - Chat detail
- `POST /chat/{id}` - Send message
- `POST /chat/{id}/read` - Mark as read
- `GET /api/chat/unread-count` - Get unread chat message count (excludes offer messages)
- `GET /api/products/{productId}/buyers` - Get list of buyers who chatted/offered (seller only, untuk "Mark as Sold")

#### Offers
- `POST /chat/{conversationId}/offers` - Create offer
- `POST /chat/{conversationId}/offers/{messageId}/accept` - Accept offer
- `POST /chat/{conversationId}/offers/{messageId}/reject` - Reject offer
- `POST /chat/{conversationId}/offers/{messageId}/counter` - Seller counter-offer
- `POST /chat/{conversationId}/offers/{messageId}/buyer-counter` - Buyer counter-offer

#### Transactions
- `GET /transactions` - Transaction list
- `GET /transactions/{id}` - Transaction detail
- `POST /transactions` - Create transaction (from mark as sold)
- `POST /transactions/{id}/shipping` - Confirm shipping
- `POST /transactions/{id}/received` - Mark as received
- `POST /transactions/{id}/cancel` - Cancel transaction

#### Reviews
- `GET /reviews` - Review list
- `GET /reviews/create/{transactionId}` - Create review form
- `POST /reviews` - Submit review
- `GET /reviews/{id}` - Review detail
- `GET /reviews/{id}/edit` - Edit review form
- `PUT /reviews/{id}` - Update review
- `DELETE /reviews/{id}` - Delete review

#### Wishlist (Favorites)
- `GET /wishlist` - Wishlist page
- `POST /wishlist/{productId}/toggle` - Add/remove wishlist (update `favorite_count` di product)
- `DELETE /wishlist/{id}` - Remove from wishlist
- `GET /api/wishlist/{productId}/check` - Check if favorited
- **Note**: Table name di database adalah `favorites`, bukan `wishlists`

#### Notifications
- `GET /notifications` - Notification list
- `POST /notifications/{id}/read` - Mark as read
- `POST /notifications/read-all` - Mark all as read
- `DELETE /notifications/{id}` - Delete notification
- `GET /api/notifications/unread-count` - Get unread notification count (only offer & transaction types, excludes chat)

---

## Services & Integrations

### 1. ImageService

**File**: `app/Services/ImageService.php`

**Purpose**: Handle image upload to Cloudinary

**Methods**:
- `uploadAvatar($file)` - Upload user avatar (400x400, face crop, folder: `reloved/avatars`)
- `uploadProductImage($file, $order)` - Upload product image (1200x1200, limit crop, folder: `reloved/products`)
- `uploadImage($file, $folder)` - Generic image upload untuk folder tertentu (digunakan untuk review images, folder: `reloved/{folder}`)
- `deleteImage($publicId)` - Delete image from Cloudinary

**Usage**:
```php
$imageService = app(ImageService::class);
$result = $imageService->uploadProductImage($request->file('image'), 1);
// Returns: ['cloudinary_public_id' => '...', 'cloudinary_url' => '...']
```

### 2. RajaOngkirService

**File**: `app/Services/RajaOngkirService.php`

**Purpose**: Interact with RajaOngkir API for shipping costs

**Methods**:
- `searchDestination($query, $limit)` - Search cities/districts (API V2)
- `getSubdistrictId($cityName, $subdistrictName)` - Get subdistrict ID (with caching, 30 days)
- `calculateCost($originSubdistrictId, $destinationSubdistrictId, $weight, $couriers)` - Calculate shipping cost (with caching, 1 day)
- `checkCost($origin, $destination, $weight, $courier)` - Legacy method (redirects to calculateCost)
- `getCityId($cityName, $provinceName)` - Legacy method (redirects to getSubdistrictId)
- `searchCities($query, $limit)` - Search cities by name (redirects to searchDestination)
- `getProvinces()` - Get all provinces (legacy, may not work on API V2)
- `getCities($provinceId)` - Get cities by province (legacy, may not work on API V2)

**Features**:
- ✅ Caching untuk mengurangi API calls
- ✅ Rate limit handling (HTTP 429)
- ✅ Error handling & logging

**Caching**:
- `getSubdistrictId`: 30 days cache
- `calculateCost`: 1 day cache

### 3. WilayahIndonesiaService

**File**: `app/Services/WilayahIndonesiaService.php`

**Purpose**: Fetch Indonesian regional data from free API (Wilayah.id)

**Methods**:
- `getProvinces()` - Get all provinces (cached 30 days)
- `getRegencies($provinceId)` - Get regencies (kabupaten/kota) by province (cached 30 days)
- `getDistricts($regencyId)` - Get districts (kecamatan) by regency (cached 30 days)
- `getVillages($districtId)` - Get villages (kelurahan/desa) by district (cached 30 days)
- `searchProvinces($query)` - Search provinces by name
- `searchRegencies($query, $provinceId)` - Search regencies by name
- `normalizeName($name)` - Normalize city name (remove "Kota", "Kabupaten" prefix)
- `findRegencyIdByRajaOngkirName($rajaOngkirCityName, $provinceId)` - Bridge between Wilayah.id and RajaOngkir

**API**: `https://wilayah.id/api/`

### 4. NotificationService

**File**: `app/Services/NotificationService.php`

**Purpose**: Create notifications for users

**Methods**:
- `create($userId, $type, $title, $message, $notifiable)` - Generic notification creator
- `notifyNewMessage($user, $conversation)` - Notify new chat message
- `notifyNewOffer($user, $offer)` - Notify new price offer
- `notifyOfferAccepted($user, $offer)` - Notify offer accepted
- `notifyOfferRejected($user, $offer)` - Notify offer rejected
- `notifyTransactionCreated($user, $transaction)` - Notify new transaction
- `notifyTransactionShipped($user, $transaction)` - Notify package shipped
- `notifyTransactionReceived($user, $transaction)` - Notify package received
- `notifyTransactionCancelled($user, $transaction, $cancelledBy)` - Notify transaction cancelled
- `notifyNewReview($user, $review)` - Notify new review received

### 5. ChatService

**File**: `app/Services/ChatService.php`

**Purpose**: Handle automated chat messages

**Methods**:
- `sendShippingReceipt($transaction)` - Send receipt message to chat (hanya jika tracking_number ada, return Message atau null)

**Private Methods**:
- `generateReceiptMessage($transaction)` - Generate formatted receipt message dengan detail lengkap
- `getTrackingLink($courier, $trackingNumber)` - Get tracking URL berdasarkan courier

**Usage**: Otomatis dipanggil saat seller konfirmasi shipping via `TransactionController@updateShipping`. Jika `$transaction->tracking_number` ada, maka receipt message otomatis dikirim ke chat dengan detail lengkap transaksi (produk, harga, metode, shipping info, tracking link, dll).

### 6. Real-time Chat (Laravel Reverb & Redis)

**Purpose**: Real-time messaging dengan WebSocket

**Components**:
- **Laravel Reverb**: WebSocket server untuk broadcasting
- **Laravel Echo**: Client-side library untuk real-time updates
- **Redis**: Message broadcasting & pub/sub

**Setup**:
1. Pastikan Redis server berjalan
2. Jalankan `php artisan reverb:start`
3. Set `BROADCAST_DRIVER=reverb` atau `redis` di `.env`
4. Set `VITE_ENABLE_REVERB=true` di `.env`
5. Set `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, `VITE_REVERB_PORT`

**How it works**:
- Message dikirim → `MessageSent` event di-broadcast ke private channel `conversation.{id}`
- Redis pub/sub mengirim event ke Reverb server
- Reverb server mengirim ke connected clients via WebSocket
- Laravel Echo di client menerima event dan update UI real-time
- Fallback: Jika broadcasting tidak tersedia, chat tetap berfungsi dengan polling (setInterval 5 detik)

---

## Security & Best Practices

### Authentication Security

- ✅ **Password Hashing**: bcrypt (Laravel default)
- ✅ **Session Security**: Database session, regenerate after login
- ✅ **CSRF Protection**: Laravel CSRF tokens
- ✅ **Email Verification**: Required sebelum login
- ✅ **OTP Security**: 6 digit random, 15 menit expiry, one-time use
- ✅ **Google OAuth**: Secure state parameter validation

### Authorization

- ✅ **Middleware**: `auth` middleware untuk protected routes
- ✅ **Authorization Checks**: Manual checks di controllers (`abort(403)`)
- ✅ **Resource Ownership**: Check user ownership sebelum edit/delete
- ✅ **Validation**: Form Requests untuk validation rules

### Input Validation

- ✅ **Form Requests**: Dedicated request classes untuk validation
- ✅ **Database Constraints**: Unique constraints, foreign keys
- ✅ **SQL Injection Prevention**: Eloquent ORM (prepared statements)
- ✅ **XSS Prevention**: Blade auto-escaping

### Data Protection

- ✅ **Soft Deletes**: users, products, transactions
- ✅ **UUID Primary Keys**: Prevent enumeration attacks
- ✅ **Sensitive Data**: Password tidak disimpan plain text
- ✅ **File Upload**: Validation & Cloudinary storage (no local storage)

### API Security

- ✅ **Rate Limiting**: RajaOngkir API caching
- ✅ **Error Handling**: Tidak expose sensitive info di error messages
- ✅ **Logging**: Error logging untuk debugging

---

## Catatan Penting

### ⚠️ Limitations & Constraints

1. **Redis Required**
   - Redis diperlukan untuk broadcasting & real-time chat
   - Reverb menggunakan Redis untuk message broadcasting
   - Chat real-time dengan WebSocket connection
   - Pastikan Redis server berjalan sebelum menggunakan fitur chat

2. **Real-time Features**
   - Chat real-time dengan Laravel Reverb & Laravel Echo
   - WebSocket connection untuk instant messaging
   - Broadcasting menggunakan Redis
   - Notifikasi real-time untuk chat messages

3. **No Admin Panel**
   - C2C only, tidak ada admin
   - Tidak ada content moderation
   - User manage sendiri produk mereka

4. **No Payment Gateway**
   - COD only
   - Pembayaran langsung antara buyer-seller
   - Platform hanya tracking, tidak handle payment

5. **Localhost Only**
   - Dikembangkan untuk localhost (Laragon)
   - Tidak ada deployment
   - Tidak ada production environment

6. **RajaOngkir Daily Limit**
   - Free tier: 250 requests/day
   - Caching implemented untuk mengurangi calls
   - Rate limit handling & error messages

7. **Database Constraints**
   - Province & City sebagai text (bukan FK)
   - Tidak ada FK ke wilayah external API

### ✅ Best Practices Implemented

1. **Database Transactions**: Untuk operasi kritis (create transaction, update stock)
2. **Error Handling**: Try-catch blocks dengan proper error messages
3. **Logging**: Error logging untuk debugging
4. **Caching**: RajaOngkir API caching
5. **Validation**: Form Requests untuk semua user input
6. **Authorization**: Check ownership & permissions di semua actions
7. **Soft Deletes**: Untuk data penting (users, products, transactions)

### 🔧 Development Tips

1. **Clear Cache**: Jika ada masalah, clear semua cache
2. **Check Logs**: Lihat `storage/logs/laravel.log` untuk error details
3. **Database**: Gunakan HeidiSQL untuk manage database
4. **Asset Building**: Gunakan `npm run dev` untuk development dengan hot reload
5. **Migration**: Selalu backup database sebelum migration

### 📝 Code Structure

- **Controllers**: Handle HTTP requests & responses
- **Models**: Database interactions & relationships
- **Services**: Business logic & external API calls
- **Requests**: Form validation rules
- **Views**: Blade templates dengan Tailwind CSS
- **Routes**: `web.php` untuk semua routes

### 🎨 Frontend Architecture

- **Blade**: Server-side templating
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework (reactive components)
- **Vite**: Fast build tool & dev server

---

## Kesimpulan

Projek **Reloved Marketplace** adalah platform C2C marketplace yang lengkap dengan fitur-fitur utama:
- User management dengan OAuth
- Product management dengan dynamic attributes
- Chat & negotiation system
- Transaction tracking (COD)
- Review & rating system
- Wishlist & notifications

Platform ini dirancang untuk localhost development dengan Laragon, menggunakan PostgreSQL, Redis, Cloudinary, dan RajaOngkir API. Fitur real-time chat menggunakan Laravel Reverb dengan Redis untuk broadcasting, memungkinkan instant messaging tanpa perlu refresh halaman.

---

**Dokumentasi ini dibuat untuk membantu developer memahami struktur dan alur kerja projek Reloved Marketplace.**

**Last Updated**: 2025-01-XX  
**Version**: 1.0  
**Status**: Development (Localhost)

