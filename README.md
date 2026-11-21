# Reviewer Matching Board

A reviewer matching application built using Angular and CodeIgniter 3.

The application allows users to:
- View available articles.
- View reviewers for a selected article.
- Identify conflicts of interest based on institutional affiliation.
- Assign reviewers to articles.
- Prevent duplicate assignments.
- View previously saved assignments.

## Tech Stack

**Frontend**
- Angular (Standalone Components)
- TypeScript
- HTML and CSS
- RxJS

**Backend**
- PHP
- CodeIgniter 3
- JSON-based mock data and assignment storage

## Project Structure

```text
reviewer-board/
├── backend/
│   ├── application/
│   ├── data/
│   ├── storage/
│   ├── system/
│   ├── index.php
│   └── .htaccess
├── frontend/
│   ├── src/
│   ├── package.json
│   └── angular.json
├── README.md
└── .gitignore
```

## Prerequisites

- PHP and Apache (XAMPP recommended)
- Node.js and npm
- Angular CLI

## Backend Setup

1. Clone or copy the project into your XAMPP `htdocs` directory.

2. Make sure the backend is located at:

   `C:\xampp\htdocs\reviewer-board\backend`

3. Start Apache from the XAMPP Control Panel.

4. Ensure Apache's `mod_rewrite` module is enabled.

5. Make sure the mock data file exists:

   `backend/data/mock_openalex_data.json`

6. The application automatically creates `backend/storage/assignments.json` when assignments are first accessed. Ensure the storage directory is writable.

### Backend API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/reviewer/articles` | Fetch articles |
| GET | `/api/reviewer/reviewers` | Fetch reviewers |
| GET | `/api/reviewer/assignments` | Fetch saved assignments |
| POST | `/api/reviewer/assign` | Assign a reviewer |

Backend base URL:

`http://localhost/reviewer-board/backend`

## Frontend Setup

1. Open a terminal in the frontend directory:

   `cd frontend`

2. Install dependencies:

   `npm install`

3. Start the Angular application:

   `ng serve`

4. Open the application:

   `http://localhost:4200`

## Assignment Rules

- A reviewer cannot be assigned to an article if the reviewer and article author belong to the same institution.
- A reviewer cannot be assigned more than once to the same article.
- Successful assignments are saved in a JSON file and remain available after refreshing the application.

## Notes

- The application uses mock JSON data.
- The backend API URL is configured in the Angular reviewer service.
- This project was developed as a reviewer matching assignment.