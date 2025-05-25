import { Component, Input, OnInit } from '@angular/core';
import { NavbarComponent } from "../navbar/navbar.component";
import { ProductsComponent } from '../products/products.component';
import { ApiService } from '../../services/api.service';
import { CommonModule }      from '@angular/common';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-display-all',
  imports: [NavbarComponent,CommonModule,ProductsComponent],
  templateUrl: './display-all.component.html',
  styleUrl: './display-all.component.css'
})
export class DisplayAllComponent implements OnInit{
  @Input() products: any[] = [];    
  categories: any[] = [];
  brands: any[] = [];

  constructor(private api: ApiService,
     private route: ActivatedRoute,
  ) {}

  ngOnInit(): void {
    this.api.getProducts().subscribe(p => this.products = p);
    this.api.getCategories().subscribe(c => this.categories = c);
    this.api.getBrands().subscribe(b => this.brands = b);
     const id = Number(this.route.snapshot.paramMap.get('id'));
    if (id) {
      this.api.getProducts(id).subscribe(data => this.products = data);
    }
  }
}
