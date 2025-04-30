import { Component, OnInit } from '@angular/core';
import { ApiService }        from '../../services/api.service';
import { CommonModule }      from '@angular/common';
import { NavbarComponent }   from '../navbar/navbar.component';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './products.component.html'
})
export class ProductsComponent implements OnInit {
  products: any[] = [];

  constructor(private api: ApiService) {}

  ngOnInit() {
    this.api.getProducts().subscribe(data => this.products = data);
  }

  // addToCart(p: any) {
  //   this.api.addToCart(p.product_id, 1).subscribe();
  // }
}
