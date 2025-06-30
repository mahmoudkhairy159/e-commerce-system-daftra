# 🛒 E-commerce System - Daftra Task

[![Laravel](https://img.shields.io/badge/Laravel-10.0-red.svg?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg?style=flat-square&logo=php)](https://php.net)
[![React](https://img.shields.io/badge/React-18.2-blue.svg?style=flat-square&logo=react)](https://reactjs.org)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

## 🎯 Project Overview

A comprehensive **full-stack e-commerce platform** built with Laravel 10 and React 18, featuring a modular architecture, multi-language support, advanced order management, and a modern admin dashboard. This system provides a complete solution for online stores with robust features for both customers and administrators.

## ✨ Key Features

### 🛍️ **Customer Features**
- **Product Catalog**: Browse products with advanced filtering and search
- **Multi-language Support**: Arabic and English language support
- **Product Reviews & Ratings**: Customer feedback system
- **Related Products & Accessories**: Smart product recommendations
- **Shopping Cart**: Persistent cart with quantity management
- **User Authentication**: Registration, login, social authentication
- **User Profiles**: Address management, order history, preferences
- **Order Tracking**: Real-time order status updates
- **Responsive Design**: Modern React-based SPA with Material-UI

### 👨‍💼 **Admin Features**
- **Product Management**: Create, edit, approve products with multi-language support
- **Category Management**: Hierarchical category system with translations
- **Order Management**: Complete order lifecycle with status transitions
- **User Management**: Customer accounts and role-based access control
- **Inventory Management**: Stock tracking and management
- **Shipping Management**: Shipping methods and tracking
- **Analytics Dashboard**: Sales reporting and insights
- **Content Management**: Multi-language content editing

### 🔧 **Technical Features**
- **Modular Architecture**: Laravel Modules for clean separation
- **Repository Pattern**: Clean data access layer
- **Event-Driven**: Order events, notifications, and background jobs
- **Caching System**: Redis caching for products and categories
- **API-First Design**: RESTful APIs with comprehensive documentation
- **Security**: Sanctum authentication, rate limiting, input validation
- **File Management**: Image uploads with AWS S3 support
- **Database**: Advanced relationships and soft deletes

## 🏗️ **Architecture & Design Patterns**

### **Modular Architecture**
The application uses **Laravel Modules** for clean separation of concerns:

```
Modules/
├── Admin/          # Admin management & roles
├── Cart/           # Shopping cart functionality  
├── Category/       # Product categorization
├── Order/          # Order management & tracking
├── Product/        # Product catalog & reviews
├── Shipping/       # Shipping methods & tracking
└── User/           # User authentication & profiles
```

**Benefits:**
- **Maintainability**: Each module is self-contained with its own models, controllers, and routes
- **Scalability**: Easy to add new modules without affecting existing code
- **Team Collaboration**: Multiple developers can work on different modules simultaneously
- **Testing**: Isolated testing per module with dedicated test suites

### **Repository Pattern Implementation**
Using **Prettus L5-Repository** for clean data access:

```php
// Clean controller logic
public function index(ProductRepository $repository)
{
    return $repository->with(['categories', 'reviews'])
                     ->scopeActive()
                     ->paginate();
}
```

**Benefits:**
- **Separation of Concerns**: Controllers focus on HTTP logic, repositories handle data
- **Testability**: Easy mocking of data layer for unit tests
- **Flexibility**: Swap implementations without affecting business logic
- **Caching**: Built-in repository caching support

### **Event-Driven Architecture**
```php
// Order placement triggers multiple events
event(new OrderPlaced($order));

// Listeners handle notifications, inventory updates, etc.
SendOrderPlacedEmailJob::dispatch($order);
SendAdminOrderNotification::dispatch($order);
```

## 🚀 **Quick Start**

### **Prerequisites**
- PHP 8.2 or higher
- Composer 2.x
- MySQL 5.7+ or PostgreSQL
- Node.js 18+ (for React frontend)
- Redis (recommended for caching)

### **Installation**

#### **1. Clone the Repository**
```bash
git clone https://github.com/yourusername/ecommerce-system-daftra.git
cd ecommerce-system-daftra
```

#### **2. Install Dependencies**
```bash
# Backend dependencies
composer install

# Frontend dependencies  
npm install
```

#### **3. Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### **4. Configure Database**
Edit your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_daftra_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### **5. Configure Additional Services**
```env
# Redis (for caching)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password

# AWS S3 (for file storage)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

#### **6. Database Setup**
```bash
# Run migrations and seeders
php artisan optimize
php artisan module:migrate-fresh --seed
PHP artisan schedule:run
or 
php artisan queue:work
```

#### **7. Build Frontend**
```bash
# Development build
npm run dev

# Production build
npm run build
```

#### **8. Start the Application**
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start the frontend (if using dev mode)
npm run dev
```

### **Access Points**

- **Application**: http://localhost:8000
- **Admin Dashboard**: http://localhost:8000/admin
- **API Documentation**: http://localhost:8000/api/documentation

## 📚 **API Documentation**

### **API Endpoints Overview**

#### **Authentication**
```bash
POST /api/user/v1/auth/register           # User registration
POST /api/user/v1/auth/login              # User login
POST /api/user/v1/auth/logout             # User logout
POST /api/user/v1/auth/refresh-token      # Token refresh
POST /api/user/v1/auth/forgot-password    # Password reset request
POST /api/user/v1/auth/reset-password     # Password reset
```

#### **Products**
```bash
GET    /api/user/v1/products              # List products (paginated)
GET    /api/user/v1/products/{id}         # Get product details
GET    /api/user/v1/products/slugs/{slug} # Get product by slug
GET    /api/user/v1/products/featured     # Featured products
GET    /api/user/v1/products/new-arrivals # New arrivals
GET    /api/user/v1/products/best-sellers # Best selling products
GET    /api/user/v1/products/category/{id} # Products by category
```

#### **Cart Management**
```bash
GET    /api/user/v1/cart                  # Get user cart
POST   /api/user/v1/cart/add              # Add product to cart
PUT    /api/user/v1/cart/update           # Update cart item
DELETE /api/user/v1/cart/remove           # Remove from cart
```

#### **Order Management**
```bash
GET    /api/user/v1/orders                # User order history
POST   /api/user/v1/orders                # Place new order
GET    /api/user/v1/orders/{id}           # Get order details
PUT    /api/user/v1/orders/{id}/cancel    # Cancel order
```

#### **Categories**
```bash
GET    /api/user/v1/categories            # List categories
GET    /api/user/v1/categories/{id}       # Category details
GET    /api/user/v1/categories/{id}/products # Products in category
```

#### **Admin Endpoints**
```bash
GET    /api/admin/v1/products             # Admin product management
POST   /api/admin/v1/products             # Create product
PUT    /api/admin/v1/products/{id}        # Update product
DELETE /api/admin/v1/products/{id}        # Delete product
GET    /api/admin/v1/orders               # All orders management
PUT    /api/admin/v1/orders/{id}/status   # Update order status
```

### **Authentication Example**

```bash
# Register new user
curl -X POST http://localhost:8000/api/user/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Ahmed",
    "last_name": "Hassan",
    "email": "ahmed@example.com",
    "password": "SecurePass123",
    "password_confirmation": "SecurePass123"
  }'

# Login and get token
curl -X POST http://localhost:8000/api/user/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@example.com",
    "password": "SecurePass123"
  }'

# Use token for authenticated requests
curl -X GET http://localhost:8000/api/user/v1/products \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## 📦 **Key Dependencies**

### **Backend (Laravel)**
- **Laravel 10**: Latest Laravel framework with modern features
- **Sanctum**: API token authentication
- **Laravel Modules**: Modular application structure
- **Prettus Repository**: Repository pattern implementation
- **Eloquent Translatable**: Multi-language model support
- **Eloquent Filter**: Advanced query filtering
- **Laravel Socialite**: Social media authentication
- **Spatie Enum**: Type-safe enumerations

### **Frontend (React)**
- **React 18**: Modern React with concurrent features
- **Material-UI**: Google's Material Design components
- **React Router**: Client-side routing
- **Axios**: HTTP client for API calls
- **Notistack**: Toast notifications

### **Database & Caching**
- **MySQL/PostgreSQL**: Primary database
- **Laravel Queue**: Background job processing

## 🔧 **Configuration**

### **Module Configuration**
Each module has its own configuration files:
```
Modules/{ModuleName}/Config/
├── config.php         # Module-specific settings
└── acl.php           # Access control lists
```

### **Environment Variables**
Key environment variables to configure:
```env
# Application
APP_NAME="E-commerce System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_DATABASE=ecommerce_db

# Redis
REDIS_HOST=127.0.0.1

# Mail
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# File Storage
FILESYSTEM_DISK=s3
AWS_BUCKET=your-bucket

# Localization
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

## 🚀 **Deployment**

### **Production Deployment**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build frontend assets
npm run build

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```
# LOGIN By default user
email: mahmoudkhairy159@gmail.com
password: 12345678
```



**Built with ❤️ using Laravel 10, React 18, and modern development practices**
