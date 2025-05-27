import { Component, OnInit } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { ProductsComponent } from '../products/products.component';
import { ApiService } from '../../services/api.service';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-display-all',
  standalone: true,
  imports: [NavbarComponent, CommonModule, ProductsComponent,RouterModule],
  templateUrl: './display-all.component.html',
  styleUrls: ['./display-all.component.css']
})
export class DisplayAllComponent implements OnInit {
  products: any[] = [];
  categories: any[] = [];
  brands: any[] = [];

  constructor(private api: ApiService, private route: ActivatedRoute) {}

  ngOnInit(): void {

    this.api.getCategories().subscribe(c => this.categories = c);
    this.api.getBrands().subscribe(b => this.brands = b);

    this.route.queryParams.subscribe(params => {
      const category = params['category'] ? Number(params['category']) : undefined;
      const brand = params['brand'] ? Number(params['brand']) : undefined;

      this.api.getProducts(category, brand).subscribe(products => {
        this.products = products;
      });
    });
  }
}
