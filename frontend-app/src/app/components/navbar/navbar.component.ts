import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent {
  showSearch = false;
  query = '';
  results: any[] = [];

    constructor(private api: ApiService, private router: Router) {}

  toggleSearch() {
    this.showSearch = true;
    this.query = '';
    setTimeout(() => {
      const inputElement = document.getElementById('navbar-search-input');
      inputElement?.focus();
    }, 0);
  }

  submit() {
    if (!this.query.trim()) return;
    this.router.navigate(['/search'], { queryParams: { q: this.query } });
  }
}

