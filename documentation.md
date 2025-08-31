# Project Documentation

## Overview

This project is a simple blog or social media platform where users can create, edit, and delete posts, and other users can comment on and like those posts. Users can register and log in to the application.

## Key Components

-   **Controllers:** [`app/Http/Controllers/AuthController.php`](app/Http/Controllers/AuthController.php), [`app/Http/Controllers/PostController.php`](app/Http/Controllers/PostController.php)
-   **Models:** [`app/Models/User.php`](app/Models/User.php), [`app/Models/Post.php`](app/Models/Post.php), [`app/Models/Comment.php`](app/Models/Comment.php), [`app/Models/Likes.php`](app/Models/Likes.php)
-   **Routes:** Defined in [`routes/api.php`](routes/api.php)
-   **Database:** Tables for `users`, `posts`, `comments`, `likes`, etc.

## API Endpoints

-   **/register:** `POST` - Registers a new user. Parameters: `name`, `email`, `password`.
-   **/login:** `POST` - Logs in an existing user. Parameters: `email`, `password`.
-   **/logout:** `POST` - Logs out the authenticated user. Requires authentication.
-   **/all/posts:** `GET` - Retrieves all posts.
-   **/single/post/{post_id}:** `GET` - Retrieves a single post by ID.
-   **/add/post:** `POST` - Adds a new post. Parameters: `title`, `content`. Requires authentication.
-   **/edit/post:** `PUT` - Edits an existing post. Parameters: `title`, `content`. Requires authentication.
-   **/edit/post/{post_id}:** `PUT` - Edits an existing post by ID. Parameters: `title`, `content`. Requires authentication.
-   **/delete/post/{post_id}:** `DELETE` - Deletes a post by ID. Requires authentication.

## Database Schema

```mermaid
erDiagram
    users {
        INT id PK
        VARCHAR name
        VARCHAR email UNIQUE
        TIMESTAMP email_verified_at
        VARCHAR password
        VARCHAR rememberToken
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    posts {
        INT id PK
        VARCHAR title
        TEXT content
        INT user_id FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    comments {
        INT id PK
        TEXT comment
        INT user_id FK
        INT post_id FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    likes {
        INT id PK
        INT user_id FK
        INT post_id FK
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    personal_access_tokens {
        INT id PK
        VARCHAR tokenable_type
        BIGINT UNSIGNED tokenable_id
        TEXT name
        VARCHAR(64) token UNIQUE
        TEXT abilities
        TIMESTAMP last_used_at
        TIMESTAMP expires_at
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    posts ||--|| users : author
    comments ||--|| users : author
    comments ||--|| posts : post
    likes ||--|| users : user
    likes ||--|| posts : post
```

## Authentication

The authentication process is handled by the [`app/Http/Controllers/AuthController.php`](app/Http/Controllers/AuthController.php). Users can register with their name, email, and password. After registration, they can log in with their email and password. Authenticated users can then access protected routes, such as creating, editing, and deleting posts. The `auth:sanctum` middleware is used to protect these routes. There are no explicit user roles defined in the code, so all authenticated users have the same permissions.
