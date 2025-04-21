import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink }   from '@angular/router';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  name = '';
  email = '';
  password = '';
  errorMessage = '';
  successMessage = '';

  constructor(private http: HttpClient, private router: Router) {}

  register() {
    this.errorMessage = this.successMessage = '';

    this.http.post<any>(
      'http://localhost:8000/api/register.php',
      { name: this.name, email: this.email, password: this.password }
    ).subscribe({
      next: res => {
        if (res.status === 'success') {
          this.successMessage = 'Registration successful! Redirecting to login…';
          // after a short delay, go to login
          setTimeout(() => this.router.navigate(['/login']), 1500);
        } else {
          this.errorMessage = res.message;
        }
      },
      error: () => {
        this.errorMessage = 'Server error during registration.';
      }
    });
  }
}