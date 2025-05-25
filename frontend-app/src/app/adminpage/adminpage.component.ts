// src/app/admin-page/adminpage.component.ts
import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-page',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './adminpage.component.html',
  styleUrls: ['./adminpage.component.css']
})
export class AdminPageComponent implements OnInit {
  name = '';
  selectedSection: string = 'users';

  messages: { user_name: string; content: string; created_at: string }[] = [];
  users: any[] = [];
  products: any[] = [];

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit() {
    // Verifiko që përdoruesi është admin
    this.http.get<any>(
      'http://localhost:8000/api/getuser.php',
      { withCredentials: true }
    ).subscribe({
      next: res => {
        if (res.status === 'success' && res.user.role === 'admin') {
          this.name = res.user.name;
        } else {
          this.router.navigate(['/login']);
        }
      },
      error: () => {
        this.router.navigate(['/login']);
      }
    });

    // Merr mesazhet
    this.http.get<any>(
      'http://localhost:8000/api/getallmessages.php',
      { withCredentials: true }
    ).subscribe(res => {
      if (res.status === 'success') {
        this.messages = res.messages;
      }
    });

    // Merr produktet nga databaza
    this.http.get<any>(
      'http://localhost:8000/api/get_products.php',
      { withCredentials: true }
    ).subscribe(res => {
      if (res.status === 'success') {
        this.products = res.products;
      }
    });

    // Merr përdoruesit nga databaza
this.http.get<any>(
  'http://localhost:8000/api/get_all_users.php',
  { withCredentials: true }
).subscribe(res => {
  if (res.status === 'success') {
    this.users = res.users;
  }
});

  }

  selectSection(section: string) {
    this.selectedSection = section;
  }

  logout() {
    this.http.get<any>(
      'http://localhost:8000/api/logout.php',
      { withCredentials: true }
    ).subscribe(() => this.router.navigate(['/login']));
  }
}
