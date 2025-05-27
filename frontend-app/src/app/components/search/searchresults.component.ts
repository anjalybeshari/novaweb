// src/app/search-results/search-results.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../services/api.service';
import { ProductsComponent } from '../products/products.component';
import { NavbarComponent } from '../navbar/navbar.component';

@Component({
  selector: 'app-search-results',
  standalone: true,
  imports: [CommonModule, RouterModule,ProductsComponent,NavbarComponent],
  templateUrl: './searchresults.component.html',
  styleUrls: ['./searchresults.component.css']
})
export class SearchResultsComponent implements OnInit {
  term = '';
  results: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private api: ApiService
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      this.term = params['q'] || '';
      if (this.term) {
        this.api.search(this.term).subscribe({
          next: data => this.results = data,
          error: err => console.error(err)
        });
      }
    });
  }
}
