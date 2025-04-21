// src/app/user-page/user-page.component.ts
import { Component, OnInit }     from '@angular/core';
import { HttpClient }            from '@angular/common/http';
import { Router }                from '@angular/router';
import { CommonModule }          from '@angular/common';
import { RouterLink }            from '@angular/router';

@Component({
  selector: 'app-user-page',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './userpage.component.html',
  styleUrls: ['./userpage.component.css']
})
export class UserPageComponent implements OnInit {
  name = '';
  messages: { content:string; created_at:string }[] = [];

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit() {
    // Fetch user info (to get name)
    this.http.get<any>(
      'http://localhost:8000/api/get_user.php',
      { withCredentials: true }
    ).subscribe({
      next: res => {
        if (res.status === 'success') {
          this.name = res.user.name;
        } else {
          this.router.navigate(['/login']);
        }
      }
    });

    // Fetch this user’s messages
    this.http.get<any>(
      'http://localhost:8000/api/get_messages.php',
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