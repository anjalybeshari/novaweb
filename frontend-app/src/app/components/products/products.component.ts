// products.component.ts
import { Component, Input, OnInit } from '@angular/core';
import { CommonModule }      from '@angular/common';
import { RouterModule }      from '@angular/router';
import { ApiService }        from '../../services/api.service';
import { ActivatedRoute }    from '@angular/router';
import { CartService } from '../../services/cart.service';

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
    private api: ApiService,
    private cartService: CartService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.api.getProducts(id || undefined).subscribe({
      next: data => this.products = data,
      error: err => console.error('Failed to load product', err)
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