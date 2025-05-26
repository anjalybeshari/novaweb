import { Component, OnInit }  from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient }         from '@angular/common/http';
import { CommonModule }       from '@angular/common';
import { FormsModule }        from '@angular/forms';
import { RouterLink }         from '@angular/router';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './reset-password.component.html',
   styleUrl: './reset-password.component.css',

})
export class ResetPasswordComponent implements OnInit {
  token = '';
  password = '';
  password_confirmation = '';
  successMessage = '';
  errorMessage = '';

  constructor(
    private route: ActivatedRoute,
    private http:   HttpClient,
    private router: Router
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.token = params['token'] || '';
    });
  }

  submit() {
    this.successMessage = this.errorMessage = '';
    if (this.password !== this.password_confirmation) {
      this.errorMessage = 'Passwords do not match';
      return;
    }

    this.http.post<any>(
      'http://localhost:8000/api/reset_password.php',
      {
        token: this.token,
        password: this.password,
        password_confirmation: this.password_confirmation
      },
      { withCredentials: true }
    ).subscribe({
      next: res => {
        if (res.status === 'success') {
          this.successMessage = res.message;
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message;
        }
      },
      error: () => this.errorMessage = 'Server error'
    });
  }
}