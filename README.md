# 🛍️ Laravel E-commerce System for the duwantech.co.ke's Kenyan Market

This project is a **custom Laravel Blade-based E-commerce website** tailored for selling multiple product categories in Kenya. It is optimized for flexibility, search engine ranking, mobile responsiveness, and ease of configuration via a powerful backend dashboard.

---

## 🚀 Features

### 🔹 1. Landing Page

- Central search bar: `What are you looking for?`
- Sidebar with:
  - All categories with product counts
  - Nested subcategories (e.g., Electronics → Laptops & Computers)
- Trending/popular items section
- Social media share options for categories (Facebook, Instagram, TikTok, X)

---

### 🔹 2. Category & Subcategory Pages

- Display:
  - All items in the selected category or subcategory
  - **No pagination** (load all or use infinite scroll)
- Sorting Options:
  - Recommended First
  - Newest First
  - Lowest Price
  - Highest Price
- Sidebar Filters:
  - Based on available dynamic attributes (via product JSON)
  - Expandable (e.g., Internal Storage, Brand, Price, etc.)
- Each product card shows:
  - Thumbnail
  - Title
  - Price
  - Condition badge
  - "Call Shop" and "Buy via M-PESA" buttons (shown only to authenticated users)

---

### 🔹 3. Product Page

- Layout:
  - Product images gallery (carousel)
  - Detailed info section (dynamically generated from JSON attributes)
- Includes:
  - Stock status, condition, negotiable status, price, discount (if any)
  - Seller contact info
  - “Buy via M-PESA” CTA (integrated via Daraja or equivalent)
  - Share on social media buttons
- SEO:
  - OpenGraph & Twitter Card meta tags for preview generation
  - URL Slug based on product name
  - Optimized title, description, and keyword meta tags
- Enables filter generation and dynamic display

---

### 🔹 4. Authentication System

- User registration & login (via email or phone number)
- Account verification (via email or OTP SMS)
- Only verified users can:
  - See phone numbers
  - Buy products online via M-PESA
  - Access portal
- Anti-bot measures:
  - Google reCAPTCHA v3 or hCaptcha on registration/login
  - Rate limiting on auth endpoints
  - Invisible honeypot traps for bots
  - Email/phone domain blocklist

---

### 🔹 5. Online Purchase & M-PESA Integration

-On product page, users can:
  - Click Buy Button
  - Proceed to Buy via M-PESA
  - In the checkout page, user can continue shopping
- M-PESA Integration:
  - STK Push via configurable API key
  - Auto-handle payment callbacks
  - Mark product as paid and notify shop
  - Orders are stored and listed in the admin panel

---

### 🔹 6. Admin Dashboard

- Manage the entire site easily with features like:
    - Secure, role-based(spatie) backend.

    - Add/Edit/Delete Categories and Subcategories

    - Upload products:
    - Enter name, price, image, and a flexible JSON object of attributes
    - View and manage orders
    - Monitor M-PESA transactions and sales
    - Configure:
    - Company logo
    - Name, slogan, description
    - SEO defaults
    - Live chat (Tawk.to API key)
    - M-PESA API credentials
    - Full CMS-like controls for banner content and homepage text.
    - Toggle features on/off from backend without code changes

---

### 🔹 7. JSON-Based Product Attributes

Each product stores a flexible set of attributes in a JSON field. This makes the system extensible without changing the database structure.

Example:

```json
{
  "condition": "Refurbished",
  "price": 8000,
  "color": "Gray",
  "storage": "16 GB",
  "battery_health": "Above 90%",
  "negotiable": true,
  "location": "Nairobi Central"
}
```
- This structure allows:
    - Flexible filter generation per category
    - Easily expandable product data
    - Enables filter generation and dynamic display


### 🔹 8. Mobile Optimization
- Responsive UI with Tailwind CSS
- Sticky search and filter menu for mobile
- Mobile-friendly checkout

### 🔹 9. 📦 Technical Stack
- Laravel 11
- Blade templating engine
- Tailwind CSS
- MySQL
- Laravel Sanctum (for mobile API security)
- Integration-ready with:
    - M-PESA (Daraja)
    - Tawk.to for live chat
    - Social media sharing SDKs

### 🔹 10. 🔐 Security Features
- HTTPS everywhere (SSL certificate setup)
- reCAPTCHA / hCaptcha
- Email or phone verification
- Rate limiting & IP throttling
- Laravel validation & authorization
- Admin-only backend access
- Server-side validation on all inputs
- Session timeout & CSRF protection

### 🔹 11. 🔎 SEO Optimization
- Slug-based URLs for products & categories
- Structured data markup (JSON-LD) for products
- Optimized metadata (title, description, keywords)
- OpenGraph / Twitter meta for social sharing
- Clean, crawlable HTML structure
- Sitemap generation for SEO indexing

### 🔹 12. 🔁 Extensibility
- Easily add new payment providers (e.g. PayPal, Card)
- Modular admin settings to integrate other APIs
- Can be extended into multi-vendor marketplace later
- Webhooks and event-based architecture

### 🔹 13. 📊 Analytics & Logs
- Order and user activity logs
- Sales reports
- MPESA transaction logs
- Optional Google Analytics integration

### 🔹 14. 🔎 Search & Filtering
- Global search input (products, categories, subcategories)
- Sidebar filters include:
  - Expandable attributes (e.g., Internal Storage [+])
  - Filter types: Checkbox, radio, range slider
- Filters auto-update based on selected category

## ⚙️ Backend Configurable Settings
| Feature                     | Configurable From Admin? |
| --------------------------- | ------------------------ |
| Site Logo                   | ✅                        |
| Contact Info                | ✅                        |
| Categories & Subcategories  | ✅                        |
| Product Attributes          | ✅ (via JSON)             |
| M-PESA API Keys             | ✅                        |
| Live Chat API Key (Tawk.to) | ✅                        |
| SEO Defaults                | ✅                        |
| Social Share Previews       | ✅                        |
| Featured Items / Banners    | ✅                        |

## 🧠 Best Practices
- Use Laravel FormRequests for validation
- Use Service classes for M-PESA and Tawk.to logic
- Use Policies & Gates for access control
- Cache category counts and filters for speed
- Queue background jobs (e.g., sending emails, M-PESA verification)

## ✅ Performance Optimizations

- **Eager Loading**
  - Avoid N+1 queries when fetching relationships.
  - Example:
    ```php
    Product::with('category', 'images')->get();
    ```

- **Database Indexing**
  - Add indexes to all foreign keys:
    - `category_id`, `user_id`, `product_id`, `parent_id`
  - Also index frequently queried fields:
    - `slug`, `status`, `is_featured`, `price`
  - For JSON columns (if using PostgreSQL or MySQL 8+):
    - Consider virtual columns and indexing common keys.

- **Caching**
  - Cache results of expensive queries:
    ```php
    Cache::remember('categories_list', now()->addHours(24), fn () => Category::all());
    ```
  - Cache settings, filters, homepage data, and sitemap if dynamic.

- **Image Optimization**
  - Use [`spatie/laravel-medialibrary`](https://github.com/spatie/laravel-medialibrary) to:
    - Auto-generate responsive image sizes (thumbnails, previews).
    - Compress images on upload to improve page load speeds.
    - Store optimized images via filesystem, S3, or CDN.

- **Pagination & Lazy Loading**
  - Use pagination for large queries.
  - Apply lazy loading in Blade views for image-heavy pages.

---

## 🧵 Queue Management (Laravel Horizon)

- Horizon handles:
  - M-PESA payment confirmations
  - Image processing
  - Sending emails & SMS
  - Scheduled tasks (e.g., sitemap generation)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

## 🛡️ System Monitoring and Logging

### 🔍 Application Performance Monitoring

- **Laravel Telescope**
  - Monitor HTTP requests, jobs, queues, exceptions, database queries, emails, scheduled tasks, and more.
  - Ideal for development and staging environments.
  - Install with:
    ```bash
    composer require laravel/telescope
    php artisan telescope:install
    php artisan migrate
    ```
  - Access via `/telescope`.

- **Production Monitoring**
  - Use advanced tools like:
    - [Blackfire.io](https://blackfire.io/)
    - [New Relic](https://newrelic.com/)
    - [Laravel Vapor Metrics (if using Vapor)](https://vapor.laravel.com/docs/1.0/metrics.html)

- **Database Query Optimization**
  - Use Telescope or Laravel Debugbar to monitor slow queries.
  - Avoid N+1 queries with eager loading (`with()`).

---

### 📈 User Behavior and Usage Analytics

- **Anonymous Analytics**
  - Track user behavior with:
    - [Plausible](https://plausible.io/)
    - [Umami](https://umami.is/)
  - These tools are privacy-friendly and don’t require user consent.

- **Session Monitoring**
  - Integrate with **Tawk.to** live chat to:
    - Track active visitors
    - Monitor sessions and chat history
    - View user navigation in real-time

---

### 🪵 Logging

- **Laravel Default Logging**
  - Uses Monolog under the hood.
  - Logs are stored in `storage/logs/laravel.log` by default.
  - Recommended: switch to `daily` log channel in `config/logging.php`.

- **Log Viewing**
  - Use [`arcanedev/log-viewer`](https://github.com/ARCANEDEV/LogViewer) to view logs in-browser:
    ```bash
    composer require arcanedev/log-viewer
    ```

- **Error Alerts**
  - Set up error reporting with third-party tools:
    - [Sentry](https://sentry.io/)
    - [Flare](https://flareapp.io/)
    - [Bugsnag](https://www.bugsnag.com/)

- **Slack & Email Alerts**
  - Configure channels in `config/logging.php` to push logs to Slack or send critical error emails.
  - Example Slack log channel:
    ```php
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Logs',
        'emoji' => ':boom:',
        'level' => 'error',
    ],
    ```

---

### 🛠 System Health Checks (Optional)

- Consider using [`spatie/laravel-health`](https://github.com/spatie/laravel-health) to monitor:
  - Database connection
  - Cache performance
  - Disk space usage
  - Queue workers
  - External API health

---

### 🔁 Cron Job Monitoring

- Use a tool like [Laravel Scheduler Monitor](https://github.com/spatie/laravel-schedule-monitor) to:
  - Ensure scheduled tasks run as expected
  - Get notified if a cron job fails or skips


## 🎨 UI/UX Design Requirements

The website interface should be clean, modern, and mobile-friendly, with the following design principles and features:

### 🔹 General UI Style
- **Minimalistic and elegant** design with spacious layout
- **Card-based layout** for product listings
- Use **rounded corners**, **soft shadows**, and **subtle hover effects**
- Font sizes: large for headings, readable for body (e.g., `text-xl`, `text-base`)
- Stick to **2-3 primary brand colors** (configurable from backend)
- Use **TailwindCSS** utility classes for consistent styling

### 🔹 Navigation & Layout
- Sticky top navbar with:
  - Logo
  - Search bar
  - Category dropdown
  - Auth/Login buttons
- Left sidebar for categories and filters
- Main content area for:
  - Trending products (on homepage)
  - Product listings (category pages)
- **Responsive grid** for product cards (2–4 columns depending on screen size)

### 🔹 Product Cards
- Image at top, title and price below
- Condition badge (e.g., “Refurbished”, “New”)
- Quick action buttons (e.g., “Call Shop”, “Buy via M-PESA”)
- Use tags or icons for verified sellers or featured items

### 🔹 Product Page Design
- Split layout: left image gallery, right-side details and price
- Sticky “Buy via M-PESA” button
- Contact info in a clean info box
- Icons for attributes (e.g., storage, color, condition)
- Social share buttons with branding colors and icons

### 🔹 Filters and Sidebar
- Accordion-style filter groups (expand/collapse)
- Use checkboxes, sliders, or dropdowns where appropriate
- Filters update the product list in real-time (JS-ready)

### 🔹 Admin Panel UX
- Left sidebar with links (Dashboard, Products, Orders, Settings)
- Clean forms with validation
- Color pickers for brand color configuration
- Live preview of branding changes (if possible)
- JSON field editor (for flexible product attributes)

### 🔹 Color Scheme Configuration
From the admin dashboard, shop owners can:
- Choose a primary and secondary color
- Choose a text color scheme (light/dark)
- Upload logo and favicon
- Set homepage banners and slogans

Colors should be applied to:
- Buttons
- Active filters
- Headings
- Navigation bar

### 🔹 Mobile Experience
- Sticky bottom bar for important actions on mobile (e.g., “Call”, “Buy”)
- Filters collapse into a drawer or accordion
- Scrollable horizontal product sliders where needed
- Responsive nav bar with hamburger menu

### ✨ Design Style Summary for Generation
- Use Tailwind CSS
- Card-based layout
- Clean, modern, responsive
- Rounded corners, soft shadows
- Brand color configurable from backend
- Sticky buttons, beautiful typography

## 📁 Project Structure (Laravel + Blade + Tailwind)
- `/app/Http/Controllers` — All business logic
- `/resources/views` — Blade templates (UI)
- `/resources/css` — Tailwind config and styles
- `/routes/web.php` — Web routes
- `/routes/api.php` — API routes (for mobile or external services)
- `/public/` — Public assets, favicon, logos
- `/config/` — Config files including M-PESA, Tawk.to, and UI settings
