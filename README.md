# Reloved Marketplace

Platform C2C (Consumer-to-Consumer) marketplace untuk jual beli barang preloved.

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Pastikan Redis server berjalan

# Run servers (3 terminal)
# Terminal 1: Reverb (real-time chat)
php artisan reverb:start

# Terminal 2: Laravel
php artisan serve

# Terminal 3: Vite (optional, untuk development)
npm run dev
```

Access: `http://localhost:8000`

## Tech Stack

- **Backend**: Laravel 10.x
- **Database**: PostgreSQL 15+ (via Laragon)
- **Storage**: Cloudinary (images)
- **Shipping**: RajaOngkir API
- **Frontend**: Blade + Tailwind CSS + Alpine.js

## Key Features

- ✅ User authentication (Email OTP, Google OAuth)
- ✅ Product management dengan dynamic attributes
- ✅ Chat & negotiation system
- ✅ Transaction tracking (COD)
- ✅ Review & rating system
- ✅ Wishlist & notifications
- ✅ Shipping cost calculation

## Important Notes

- ⚠️ **Localhost Only**: Dikembangkan untuk Laragon (Windows)
- ✅ **Redis Required**: Chat real-time menggunakan Laravel Reverb & Redis
- ⚠️ **No Payment Gateway**: COD only
- ⚠️ **No Admin Panel**: C2C only

## Documentation

📚 **Untuk dokumentasi lengkap, lihat [DOCUMENTATION.md](./DOCUMENTATION.md)**

Dokumentasi lengkap mencakup:
- Struktur projek detail
- Database schema
- Fitur lengkap dengan alur
- API endpoints
- Services & integrations
- Security & best practices

## License

This project is a college assignment for localhost development only.
