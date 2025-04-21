// src/app/login/login.component.ts
import { Component }        from '@angular/core';
import { HttpClient }       from '@angular/common/http';
import { Router }           from '@angular/router';
import { CommonModule }     from '@angular/common';
import { FormsModule }      from '@angular/forms';
import { RouterLink }       from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';
  successMessage: string = '';

  constructor(private http: HttpClient, private router: Router) {}

  login() {
    this.errorMessage = '';
    this.http.post<any>(
      'http://localhost:8000/api/login.php', 
      { email: this.email, password: this.password },
      { withCredentials: true }
    ).subscribe({
      next: res => {
        if (res.status === 'success') {
          // route based on role
          if (res.user.role === 'admin') {
            this.router.navigate(['/admin']);
          } else {
            this.router.navigate(['/user']);
          }
        } else {
          this.errorMessage = res.message;
        }
      },
      error: () => this.errorMessage = 'Server error'
    });
  }
}