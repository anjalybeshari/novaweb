import { Component, Optional } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { CartService } from '../../services/cart.service';

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
 cartCount = 0;
  userName: string = '';

  constructor(
    private api: ApiService,
    private router: Router,
     private cartService: CartService,
    @Optional() private route: ActivatedRoute
  ) {
    this.loadCategories();
    this.loadUserName();
      this.cartService.count$.subscribe(c => this.cartCount = c);
    if (this.route) {
      this.route.queryParams.subscribe(params => {
        this.showCategories = params['showCategories'] === 'true';
        if (this.showCategories) this.showSearch = false;
      });
    }
  }

  loadUserName() {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      const user = JSON.parse(storedUser);
      this.userName = user.name || user.user_name || '';
    }
  }

  loadCategories() {
    this.api.getCategories().subscribe({
      next: data => this.categories = data,
      error: err => console.error('Failed to load categories', err)
    });
  }

  toggleCategories() {
    this.router.navigate(['/display-all'], { queryParams: { showCategories: true } });
  }

  filterByCategory(categoryId: number) {
    this.showCategories = false;
    this.router.navigate(['/display-all'], { queryParams: { category: categoryId } });
  }

  toggleSearch() {
    this.showSearch = true;
    this.query = '';
    this.results = [];
    setTimeout(() => {
      document.getElementById('navbar-search-input')?.focus();
    }, 0);
  }

  submit() {
  const term = this.query.trim();
  if (!term) return;
  this.showSearch = false;
  this.router.navigate(['/search'], { queryParams: { q: term } });
}


  navigateToProduct(id: number) {
    this.showSearch = false;
    this.results = [];
    this.query = '';
    this.router.navigate(['/products', id]);
  }

  logout() {
    localStorage.removeItem('user');
    this.userName = '';
    this.router.navigate(['/login']);
  }
}
