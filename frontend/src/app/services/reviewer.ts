
import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";

// Article structure
export interface Article {
  id: string;
  title: string;
  author: {
    name: string;
    institution: string;
  };
}

// Reviewer structure
export interface Reviewer {
  id: string;
  name: string;
  institution: string;
  keywords: string[];
}

// Assignment structure
export interface Assignment {
  article_id: string;
  reviewer_id: string;
  assigned_at: string;
}

// API response structure
export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data: T;
}

@Injectable({
  providedIn: "root",
})
export class ReviewerService {
  private apiUrl = "http://localhost/reviewer-board/backend/api/reviewer";

  constructor(private http: HttpClient) {}

  // Get all articles
  getArticles(): Observable<ApiResponse<Article[]>> {
    return this.http.get<ApiResponse<Article[]>>(
      `${this.apiUrl}/articles`
    );
  }

  // Get all reviewers
  getReviewers(): Observable<ApiResponse<Reviewer[]>> {
    return this.http.get<ApiResponse<Reviewer[]>>(
      `${this.apiUrl}/reviewers`
    );
  }

  // Get all assignments
  getAssignments(): Observable<ApiResponse<Assignment[]>> {
    return this.http.get<ApiResponse<Assignment[]>>(
      `${this.apiUrl}/assignments`
    );
  }

  // Assign reviewer to article
  assignReviewer(
    articleId: string,
    reviewerId: string,
  ): Observable<ApiResponse<Assignment>> {
    const data = {
      article_id: articleId,
      reviewer_id: reviewerId,
    };

    return this.http.post<ApiResponse<Assignment>>(
      `${this.apiUrl}/assign`,
      data
    );
  }
}
