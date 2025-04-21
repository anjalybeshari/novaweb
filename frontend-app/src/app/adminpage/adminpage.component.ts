// src/app/admin-page/admin-page.component.ts
import { Component, OnInit }     from '@angular/core';
import { HttpClient }            from '@angular/common/http';
import { Router }                from '@angular/router';
import { CommonModule }          from '@angular/common';
import { RouterLink }            from '@angular/router';

@Component({
  selector: 'app-admin-page',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './adminpage.component.html',
  styleUrls: ['./adminpage.component.css']
})
export class AdminPageComponent implements OnInit {
  name = '';
  messages: { user_name:string; content:string; created_at:string }[] = [];

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit() {
    // verify admin
    this.http.get<any>(
      'http://localhost:8000/api/get_user.php',
      { withCredentials: true }
    ).subscribe({
      next: res => {
        if (res.status==='success' && res.user.role==='admin') {
          this.name = res.user.name;
        } else {
          this.router.navigate(['/login']);
        }
      }
    });

    // fetch all messages
    this.http.get<any>(
      'http://localhost:8000/api/get_all_messages.php',
      { withCredentials: true }
    ).subscribe(res => {
      if (res.status === 'success') {
        this.messages = res.messages;
      }
    });
  }

  logout() {
    this.http.get<any>(
      'http://localhost:8000/api/logout.php',
      { withCredentials: true }
    ).subscribe(() => this.router.navigate(['/login']));
  }
}