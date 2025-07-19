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

## ✅ Deployment
- Docker-ready or use shared hosting
- Env config for MPESA, Tawk.to, and database
- Use Laravel Scheduler for cleanup & monitoring
- Regular backups and server monitoring

## ✅ Summary
- This E-commerce system is designed to deliver:
- High performance
- Maximum configurability
- Easy extensibility
- SEO & social media readiness
- Secure, authenticated purchasing