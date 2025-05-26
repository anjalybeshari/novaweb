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

  showCategories = false;
  categories: any[] = [];

  constructor(private api: ApiService, private router: Router) {
    this.loadCategories();
  }

  loadCategories() {
    this.api.getCategories().subscribe({
      next: data => this.categories = data,
      error: err => console.error('Failed to load categories', err)
    });
  }

  toggleCategories() {
    this.showCategories = !this.showCategories;
  }

  filterByCategory(categoryId: number) {
    // Mbyll dropdown-in
    this.showCategories = false;

    // Kalon tek faqja e produkteve me filtrin e kategorisë
    this.router.navigate(['/display-all'], { queryParams: { category: categoryId } });
  }

  toggleSearch() {
    this.showSearch = true;
    this.query = '';

    setTimeout(() => {
      const inputElement = document.getElementById('navbar-search-input');
      inputElement?.focus();
    }, 0);
  }

  submit() {
    this.api.search(this.query).subscribe(data => {
      this.results = data;
      console.log("Search results:", data);
    });
  }
}
