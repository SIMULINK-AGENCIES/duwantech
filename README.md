# 🛍️ Laravel E-commerce System for the Kenyan Market

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
- Action Buttons (visible only to logged-in users):
  - Call Shop
  - Buy via M-PESA

---

### 🔹 3. Product Page

- Product image gallery
- Dynamically rendered attributes from JSON
- Details like:
  - Price, Stock, Brand, Condition, Warranty
  - Battery health, Negotiable price, etc.
- Seller contact & M-PESA checkout option
- Share product on social media (with optimized previews)
- SEO-friendly structured data, meta tags, and URL slugs

---

### 🔹 4. Authentication System

- Register/Login with phone or email
- Email or SMS verification required
- Verified users can:
  - Buy products
  - See seller contact info
- Security:
  - Google reCAPTCHA / hCaptcha
  - Invisible honeypot fields
  - Rate limiting

---

### 🔹 5. Online Purchase & M-PESA Integration

- Secure STK push checkout via M-PESA API
- Payment confirmation & order logging
- Admin notified on successful payment
- Trackable order history

---

### 🔹 6. Admin Dashboard

Manage the entire site easily with features like:

- Add/Edit/Delete:
  - Categories, Subcategories
  - Products (with images + JSON attribute editor)
- View orders & transactions
- Configure:
  - Company name, slogan, logo
  - SEO defaults
  - M-PESA API credentials
  - Tawk.to chat integration
  - Social preview templates
  - Home page layout & banners

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
