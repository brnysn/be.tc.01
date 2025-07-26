# Laravel Test Case: "B2B Sipariş Yönetimi API"

This project is a Simple Order Management API built with Laravel. It provides endpoints for managing products, orders, and user authentication.

## Prerequisites

- Docker and Docker Compose
- Git

## Installation

1. Clone the repository:
```bash
git clone https://github.com/brnysn/be.tc.01.git be
cd be
```

2. Start the Docker containers:
```bash
docker compose up --build
```

The application will automatically:
- Set up the environment file
- Generate application key
- Run database migrations
- Seed the database with test data

The application should now be running at `http://localhost:8080`

## Test Credentials

You can use these pre-configured accounts to test the application:

### Admin User
```json
{
    "email": "admin@b2btechus.com",
    "password": "password"
}
```

### Customer User
```json
{
    "email": "customer@b2btechus.com",
    "password": "password"
}
```

## 📘 API Documentation

A complete Postman collection with all API endpoints and examples is available here:
## [🔗 B2B Order Management API - Postman Workspace](https://www.postman.com/restless-shadow-328763/workspace/b2b-sipari-ynetimi-api)

### Main Routes

#### Public Routes
- `GET /api/products` - List all products (cached)
- `GET /api/products/{product}` - Get product details

#### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout (requires authentication)
- `POST /api/logout-all` - Logout from all devices (requires authentication)
> **Note:**  
> Since it is a test, an email verification system is not established.


#### User Profile
- `GET /api/profile` - Get user profile
- `PUT /api/profile` - Update user profile

#### Products (Admin Only)
- `POST /api/products` - Create a new product
- `PUT /api/products/{product}` - Update product
- `DELETE /api/products/{product}` - Soft delete product
- `GET /api/products/trashed/list` - List soft-deleted products
- `POST /api/products/{id}/restore` - Restore soft-deleted product
- `DELETE /api/products/{id}/force-delete` - Permanently delete product

#### Orders (Authentication Required)
- `GET /api/orders` - List orders (customers see their own, admins see all)
- `POST /api/orders` - Create a new order (customers only)
- `GET /api/orders/{order}` - Get order details (admin can see all order's details, customer can see only self order details.)

## User Roles

The system has two types of users:
- **Admin**: Can manage products (including soft deletes) and view all orders
- **Customer**: Can view products and manage their own orders

## Development

The project uses Docker with the following services:
- **app**: PHP Laravel application
- **nginx**: Web server
- **mysql**: Database server

## License

This project is open-sourced software licensed under the MIT license.
