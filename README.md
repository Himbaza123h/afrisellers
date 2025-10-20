# AfriSellers B2B Marketplace

A comprehensive B2B marketplace platform for African suppliers and buyers.

## Project Timeline
- **Start Date**: October 13, 2025
- **End Date**: January 12, 2026
- **Duration**: 13 weeks (3 months)

## Sprint Overview
1. **Sprint 1** (5 weeks): Core B2B Marketplace + Admin Hierarchy
2. **Sprint 2** (4 weeks): Loadboard System + Departmental Integration
3. **Sprint 3** (4 weeks): Agent/Affiliate System + Advanced Features

## Tech Stack
- **Backend**: Laravel 11, PHP 8.3
- **Frontend**: Blade, Tailwind CSS v4, Alpine.js
- **Database**: MySQL, Redis
- **Payment**: Flutterwave/Stripe
- **Cloud**: AWS
- **CDN**: Cloudflare

## Setup Instructions
1. Clone repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env`
5. Generate key: `php artisan key:generate`
6. Run migrations: `php artisan migrate`
7. Seed database: `php artisan db:seed`

## Development Workflow
- `main` - Production
- `staging` - Client review
- `development` - Integration
- `sprint-X-*` - Sprint branches
- `sprint-X/feature-name` - Feature branches

## Client Feedback Checkpoints
Total: 22 checkpoints throughout the project
Review period: 48 hours per checkpoint

## Documentation
- API Documentation: `/docs/api`
- User Guides: `/docs/user-guides`
- Admin Guides: `/docs/admin-guides`

