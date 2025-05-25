import { Component, OnInit } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { ProductsComponent } from '../products/products.component';
import { ApiService } from '../../services/api.service';
import { CommonModule }      from '@angular/common';

@Component({
  selector: 'app-display-all',
  imports: [NavbarComponent,CommonModule,ProductsComponent],
  templateUrl: './display-all.component.html',
  styleUrl: './display-all.component.css'
})
export class DisplayAllComponent implements OnInit{
   products: any[] = [];
  categories: any[] = [];
  brands: any[] = [];

  constructor(private api: ApiService) {}

  ngOnInit(): void {
    this.api.getProducts().subscribe(p => this.products = p);
    this.api.getCategories().subscribe(c => this.categories = c);
    this.api.getBrands().subscribe(b => this.brands = b);
  }
}
