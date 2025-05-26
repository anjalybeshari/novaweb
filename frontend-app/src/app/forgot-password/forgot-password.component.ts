import { Component }    from '@angular/core';
import { HttpClient }   from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule }  from '@angular/forms';
import { RouterLink }   from '@angular/router';

interface ForgotPasswordResponse {
  status: 'success' | 'error';
  message: string;
}

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './forgot-password.component.html',
  styleUrl: './forgot-password.component.css'
})
export class ForgotPasswordComponent {
  email = '';
  errorMessage = '';
  successMessage = '';

  constructor(private http: HttpClient) {}

  submit() {
    this.errorMessage = '';
    this.successMessage = '';

    this.http
      .post<ForgotPasswordResponse>(
        'http://localhost:8000/api/forgot_password.php',
        { email: this.email },
        { withCredentials: true }
      )
      .subscribe({
        next: (res) => {
          // Explicitly handle each case:
          if (res.status === 'success') {
            this.successMessage = res.message;
          } else {
            this.errorMessage = res.message;
          }
        },
        error: () => {
          this.errorMessage = 'Server error';
        }
      });
  }
}