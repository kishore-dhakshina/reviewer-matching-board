import { Component, OnInit, ChangeDetectorRef } from "@angular/core";
import {
  ReviewerService,
  Article,
  Reviewer,
  Assignment,
} from "./services/reviewer";

@Component({
  selector: "app-root",
  standalone: true,
  imports: [],
  templateUrl: "./app.html",
  styleUrl: "./app.css",
})
export class App implements OnInit {
  articles: Article[] = [];
  reviewers: Reviewer[] = [];
  assignments: Assignment[] = [];
  // Store the selected article
  selectedArticle: Article | null = null;

  assignmentMessage = "";
  assignmentSuccess = false;

  loading = false;
  errorMessage = "";

  constructor(
    private reviewerService: ReviewerService,
    private cdr: ChangeDetectorRef,
  ) {}

  ngOnInit(): void {
    this.loadArticles();
    this.loadReviewers();
    this.loadAssignments();
  }

  // Select an article
  selectArticle(article: Article): void {
    this.selectedArticle = article;

    // Clear the previous assignment message
    this.assignmentMessage = "";
    this.assignmentSuccess = false;
  }

  // Check whether reviewer has conflict of interest
  hasConflict(reviewer: Reviewer): boolean {
    if (!this.selectedArticle) {
      return false;
    }

    return (
      this.selectedArticle.author.institution.trim().toLowerCase() ===
      reviewer.institution.trim().toLowerCase()
    );
  }

  isAlreadyAssigned(reviewer: Reviewer): boolean {
    if (!this.selectedArticle) {
      return false;
    }

    return this.assignments.some((assignment) => {
      return (
        assignment.article_id === this.selectedArticle.id &&
        assignment.reviewer_id === reviewer.id
      );
    });
  }
  assignReviewer(reviewerId: string): void {
    if (!this.selectedArticle) {
      return;
    }

    this.assignmentMessage = "";
    this.assignmentSuccess = false;

    this.reviewerService
      .assignReviewer(this.selectedArticle.id, reviewerId)
      .subscribe({
        next: (response) => {
          // console.log("Assignment response:", response);

          this.assignmentMessage =
            response.message || "Reviewer assigned successfully.";

          this.assignmentSuccess = true;

          if (response.data) {
            this.assignments.push(response.data);
          }

          this.cdr.markForCheck();
        },

        error: (error) => {
          // console.log("Assignment error:", error);

          this.assignmentMessage =
            error.error?.message || "Unable to assign reviewer.";

          this.assignmentSuccess = false;

          this.cdr.markForCheck();
        },
      });
  }
  loadArticles(): void {
    this.loading = true;

    this.reviewerService.getArticles().subscribe({
      next: (response) => {
        if (response.success) {
          this.articles = response.data;
        } else {
          this.errorMessage = response.message || "Unable to load articles.";
        }

        this.loading = false;

        // Notify Angular to refresh the view
        this.cdr.markForCheck();
      },
      error: (error) => {
        this.errorMessage = "Unable to load articles.";
        this.loading = false;

        this.cdr.markForCheck();

        console.error(error);
      },
    });
  }

  // Get reviewers from backend
  loadReviewers(): void {
    this.reviewerService.getReviewers().subscribe({
      next: (response) => {
        if (response.success) {
          this.reviewers = response.data;
        }

        // Notify Angular to refresh the view
        this.cdr.markForCheck();
      },
      error: (error) => {
        this.errorMessage = "Unable to load reviewers.";

        this.cdr.markForCheck();

        console.error(error);
      },
    });
  }

  // Get Assignments from backend
  loadAssignments(): void {
    this.reviewerService.getAssignments().subscribe({
      next: (response) => {
        if (response.success) {
          this.assignments = response.data;
        }

        // console.log("Existing assignments:", this.assignments);

        this.cdr.markForCheck();
      },

      error: (error) => {
        console.error("Unable to load assignments:", error);

        this.cdr.markForCheck();
      },
    });
  }
}
