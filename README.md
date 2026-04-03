# Online Store Project

This project is a simple full-stack online store made with a PHP backend and a Vue frontend.

The idea of the application is:
- users can browse products by category
- users can register, login, add items to cart, and place an order
- admin can manage products, users, settings, and view orders

The code is kept basic on purpose. The goal was to build the project using simple methods, clear components, and a responsive layout without making the logic too complicated.

## Main Features

- Home page with category cards
- Products page with category filter and pagination
- Product details page
- Register and login with JWT authentication
- Cart with simple checkout
- Orders saved in the database
- Admin dashboard for:
  - products CRUD
  - users CRUD
  - settings
  - orders list
- Responsive frontend using Tailwind CSS

## Tech Stack

### Frontend
- Vue 3
- Vite
- Tailwind CSS

### Backend
- PHP
- FastRoute
- MariaDB / MySQL
- Docker

## Project Structure

```text
online_store_VueProject/
├── backend/
│   ├── app/
│   │   ├── public/              # Entry point and uploaded files
│   │   └── src/
│   │       ├── Controllers/     # Handle incoming requests
│   │       ├── Services/        # Business logic
│   │       ├── Repositories/    # Database queries
│   │       ├── Models/          # Data objects
│   │       ├── Framework/       # Base controller, IoC container, and database helper
│   │       └── Utils/           # JWT helper
│   └── docker-compose.yml
├── frontend/
│   ├── public/                  # Static files like category images and logo
│   └── src/
│       ├── components/
│       │   ├── admin/           # Admin tables
│       │   ├── cart/            # Cart related parts
│       │   ├── molecules/       # Reusable small UI blocks
│       │   ├── organisms/       # Bigger reusable UI blocks
│       │   └── pages/           # App pages
│       ├── utils/
│       │   └── api.js           # Shared API helper
│       ├── App.vue              # Main app layout and simple routing
│       └── config.js
└── README.md
```



### Controllers
Controllers handle the request and response.

Examples:
- `ProductController`
- `AuthController`
- `OrderController`
- `SettingsController`

I used controllers because they are the first layer that receives the route call. They validate the request, call the needed service, and return JSON.

### Services
Services hold the main application logic.

Examples:
- `ProductService`
- `AuthService`
- `OrderService`

I used services to keep business logic out of controllers. For example:
- login logic belongs in `AuthService`
- order creation logic belongs in `OrderService`

### Repositories
Repositories talk directly to the database.

Examples:
- `ProductRepository`
- `UserRepository`
- `OrderRepository`

I used repositories so SQL stays in one place. This keeps controllers and services easier to understand.

### Interfaces

Each service and repository has an interface (`IProductService`, `IProductRepository`, etc.).
This is used for dependency injection — the IoC container in `index.php` binds each interface to its concrete class, so controllers only depend on the interface, not the implementation.

### IoC Container

A simple IoC (Inversion of Control) container lives in `Framework/Container.php`.
It is set up in `index.php` and handles injecting repositories into services and services into controllers automatically through the constructor.

### Why this structure helps

- each file has one clear job
- controllers don't create their own dependencies
- easier to test by swapping in a different implementation
- easier to debug and extend

## Responsive Design

The website is responsive and works on:
- mobile
- tablet
- laptop
- desktop

Tailwind CSS is used for layout, spacing, grids, and responsive utilities.

## Security

- All user-supplied text is sanitized with `htmlspecialchars()` before being stored to prevent script injection (XSS)
- All database queries use PDO parameterized statements to prevent SQL injection
- Passwords are hashed with bcrypt (`password_hash`) and never stored as plain text
- All protected routes validate the JWT token and check the user role before responding
- See [GDPR.md](GDPR.md) for details on personal data handling and user rights

## Authentication

The project uses JWT authentication.

After login:
- the backend returns a token
- the frontend stores the token in local storage
- protected admin requests send the token in the `Authorization` header

Roles:
- `admin`
- `customer`

Only admin can:
- create, update, or delete products
- manage users
- update settings
- view all orders

### Default Admin Account

Use this admin account to access the dashboard:

- Email: `admin@example.com`
- Password: `admin123!`

## API Endpoints

### Auth
- `POST /register`
- `POST /login`

### Products
- `GET /products`
- `GET /products/{id}`
- `POST /products`
- `PUT /products/{id}`
- `DELETE /products/{id}`

`GET /products` supports:
- category filter
- pagination

Example:

```text
/products?category=1&page=1&per_page=9
```

### Categories
- `GET /categories`

### Settings
- `GET /settings`
- `PUT /settings`

### Users
- `GET /users`
- `POST /users`
- `DELETE /users/{id}`

### Orders
- `GET /orders`
- `GET /my-orders`
- `POST /orders`

### Upload
- `POST /upload`

## Database Notes

Main tables used in the project:
- `users`
- `categories`
- `products`
- `orders`
- `order_items`
- `settings`

Orders are saved in:
- `orders` for the main order
- `order_items` for each ordered product

When an order is placed:
- the order is saved
- order items are saved
- product stock is updated

## Running the Project

## Backend

```bash
cd backend
docker-compose up -d
```

Backend runs on:

```text
http://localhost
```

phpMyAdmin runs on:

```text
http://localhost:8080
```

## Frontend

```bash
cd frontend
npm install
npm run dev
```

Frontend runs on:

```text
http://localhost:5173
```

## Simple Development Notes

- Category images are placed in `frontend/public/`
- Static logo is also placed in `frontend/public/`
- Uploaded product images are stored in `backend/app/public/uploads/`
- Admin dashboard is available after admin login

## AI Usage

AI was used in a limited way to:
- help fill some sample data, like adding products
- help me understand where files should go
- help me understand structure and organization

The main code, basic logic, simple methods, page design, and component structure were kept straightforward and written in a beginner friendly way.

## Final Note

This project focuses on:
- simple code
- reusable components
- clear backend structure
- REST API practice
- responsive design

The goal was not to make the project overly advanced, but to make it understandable, working, and easy to explain.

