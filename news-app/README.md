# README.md

# News App

## Description
This project is a web application that implements a news platform using the MVC (Model-View-Controller) architecture. It allows users to create, edit, and view articles, as well as manage comments and user authentication.

## Features
- User authentication (login and registration)
- Article management (create, edit, view, and list articles)
- Comment management (add and view comments)
- Responsive design with CSS

## Project Structure
```
news-app
├── config
│   └── database.php          # Database connection settings
├── controllers
│   ├── ArticleController.php  # Handles article-related requests
│   ├── AuthController.php     # Manages user authentication
│   ├── CommentController.php   # Handles comment-related requests
│   └── UserController.php      # Manages user-related actions
├── models
│   ├── Article.php            # Interacts with the articles table
│   ├── Comment.php            # Interacts with the comments table
│   ├── Reaction.php           # Interacts with the reactions table
│   └── User.php               # Interacts with the users table
├── views
│   ├── articles
│   │   ├── create.php         # Form for creating a new article
│   │   ├── edit.php           # Form for editing an existing article
│   │   ├── index.php          # Displays a list of articles
│   │   └── show.php           # Displays a single article's details
│   ├── auth
│   │   ├── login.php          # Form for user login
│   │   └── register.php       # Form for user registration
│   └── layouts
│       └── main.php           # Main layout including header and footer
├── public
│   ├── css
│   │   └── style.css          # CSS styles for the application
│   ├── js
│   │   └── main.js            # JavaScript code for the application
│   └── index.php              # Entry point for the application
├── .htaccess                  # Configuration for URL rewriting
└── README.md                  # Documentation for the project
```

## Installation
1. Clone the repository.
2. Set up the database using the provided SQL dump.
3. Configure the database connection in `config/database.php`.
4. Run the application on a local server.

## Usage
- Access the application through the public/index.php file.
- Use the provided forms for user registration and login.
- Manage articles and comments through the respective views.

## License
This project is licensed under the MIT License.