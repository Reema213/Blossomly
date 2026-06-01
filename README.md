# Blossomly 🌸 — Online Flower Shop

A full-stack e-commerce web application for browsing and purchasing flowers and plants online, built with PHP and MySQL.

---

## 🌺 Features

- **Home Page** — Dynamic landing page with product highlights, banner, and navigation
- **Product Details** — Detailed product view with pop-up help feature
- **Shopping Cart & Checkout** — Add, remove, update quantities, and complete purchases
- **Product Management** — Admin interface to manage the flower inventory
- **Contact Us** — Customer contact form
- **User Authentication** — Login system with JavaScript validation

---

## 🛠️ Tech Stack

**Frontend**
- HTML5, CSS3, JavaScript
- Responsive Design

**Backend**
- PHP (PDO)
- MySQL

**Database**
- MySQL with ER diagram
- PDO for secure database connection

**Server**
- XAMPP (Apache + MySQL)

---

## 📁 Project Structure

```
Blossomly/
├── Interfaces/
│   ├── Home_Page/
│   │   ├── Home.php
│   │   └── style.css
│   ├── Product_Details/
│   │   ├── product_detail.php
│   │   └── style.css
│   ├── Manage_Product/
│   │   ├── manage_product.php
│   │   └── style.css
│   ├── Checkout/
│   │   └── checkout.php
│   ├── Contact_Us/
│   │   ├── contact.php
│   │   └── style.css
│   └── Login/
│       ├── login.php
│       └── start.php
├── includes/
│   ├── header.php
│   └── footer.html
├── images/              # Product and banner images
├── Blossomly Databases/
│   ├── blossomlyDB.sql  # Database schema
│   └── ERD.png          # Entity Relationship Diagram
├── db_connection.php
└── .gitignore
```

---

## ⚙️ Getting Started

### Prerequisites
- XAMPP (or any Apache + PHP + MySQL server)

### Installation

```bash
# Clone the repository
git clone https://github.com/Reema213/Blossomly.git
```

1. Move the project folder to `xampp/htdocs/`
2. Open **phpMyAdmin** and create a database called `blossomly`
3. Import `Blossomly Databases/blossomlyDB.sql`
4. Open your browser and go to `http://localhost/Blossomly/Interfaces/Login/start.php`

---

## 👩‍💻 Team

| Name | Role | Responsibilities |
|------|------|-----------------|
| Layan AlHarthi | Full Stack Developer (Home Page) | Designed HTML/CSS for the Home Page, PHP dynamic content, linked Home/About/Contact pages, built the shared header and footer |
| Juri Sulayhim | Web Developer (Product Details) | Designed the Product Details page with CSS, HTML, PHP, and JavaScript pop-up help feature |
| Sharifah Alyousef | Web Developer | Designed product management and contact pages, responsive design, JavaScript for product management, frontend-backend integration |
| Reema Shaya Aljaber | Backend Developer | Created database tables and ER diagram, PHP/PDO database connection, login page with JS validation, code debugging and final assembly |
| Haneen Alsaflan | Web Developer (Checkout) | Designed checkout page, implemented full shopping cart functionality (add, remove, update, calculate totals, complete purchase) |

---

## 📚 Course

Developed as a semester project at **Imam Abdulrahman Bin Faisal University (IAU)**, 2026.

---

## 📄 License

This project is for educational purposes.
