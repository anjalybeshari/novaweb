// src/app/products/products.component.ts
import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.css']
})
export class ProductsComponent {
  @Input() products: any[] = [];

  constructor(private cartService: CartService) {}

  addToCart(productId: number): void {
    this.cartService.addToCart(productId).subscribe({
      next: res => {
        console.log('Shtuar në cart:', res);
      },
      error: err => {
        console.error('Gabim gjatë shtimit:', err);
      }
    });
  }
}
