import { Component, Input, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-products',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './products.component.html',
  styleUrls: ['./products.component.css']
})
export class ProductsComponent implements OnInit {
   @Input() products: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private api: ApiService,
    private cartService: CartService
  ) {}

ngOnInit(): void {
  this.api.getProducts().subscribe({
    next: data => {
      console.log('Produkte nga backend:', data);
      this.products = data;
    },
    error: err => {
      console.error('Gabim në marrjen e produkteve:', err);
    }
  });
}


  addToCart(productId: number): void {
    this.cartService.addToCart(productId).subscribe({
      next: res => {
        console.log('Shtuar në cart:', res);
        alert('Produkti u shtua në cart!');
      },
      error: err => {
        console.error('Gabim gjatë shtimit:', err);
        alert('Dështoi shtimi në cart.');
      }
    });
  }
}
