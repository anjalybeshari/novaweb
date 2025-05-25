import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
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

  constructor(private api: ApiService) {}

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
