// products.component.ts
import { Component, Input, OnInit } from '@angular/core';
import { CommonModule }      from '@angular/common';
import { RouterModule }      from '@angular/router';
import { NavbarComponent }   from '../navbar/navbar.component';
import { ApiService }        from '../../services/api.service';
import { ActivatedRoute }    from '@angular/router';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [ CommonModule, RouterModule ],
  templateUrl: './products.component.html',
  styleUrls:   ['./products.component.css']    // ← point to your new CSS
})
export class ProductsComponent implements OnInit {
  @Input() products: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private api: ApiService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getProducts(id || undefined).subscribe({
      next: data => this.products = data,
      error: err => console.error('Failed to load product', err)
    });
  }
}
