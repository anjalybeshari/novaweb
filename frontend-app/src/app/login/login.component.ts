import { Component } from '@angular/core';
import { HttpClient} from '@angular/common/http';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common'; 

@Component({
  selector: 'app-login',
  standalone: true,
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  imports: [CommonModule, FormsModule] 
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';
  successMessage: string = '';

  constructor(private http: HttpClient, private router: Router) {}

  login() {
    const data = {
      email: this.email,
      password: this.password
    };

    this.http.post<any>('http://localhost:8000/api/login.php', data)
      .subscribe({
        next: (res) => {
          if (res.status === 'success') {
            this.successMessage = res.message;
            this.errorMessage = '';
            console.log('User:', res.user);
            this.router.navigate(['/login']);
          } else {
            this.errorMessage = res.message;
            this.successMessage = '';
          }
        },
        error: (err) => {
          this.errorMessage = 'Server error occurred.';
          console.error(err);
        }
      });
  }
}
