import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../navbar/navbar.component';
import { CartService } from '../../services/cart.service';
import { ApiService } from '../../services/api.service'; // importo shërbimin
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, NavbarComponent,RouterModule],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  userName: string = '';
  favorites: any[] = [];  

  constructor(private cartService: CartService, private api: ApiService) {}

  ngOnInit(): void {
    const storedUser = localStorage.getItem('user');
  if (storedUser) {
    const user = JSON.parse(storedUser);
    this.userName = user.name || user.user_name || ''; // Kontrollo saktësinë e fushës
  }

    // Thirr API-n për të marrë 6 produkte nga DB
    this.api.getProducts(undefined, undefined, 6).subscribe({
      next: data => {
        this.favorites = data;
      },
      error: err => {
        console.error('Gabim gjatë marrjes së produkteve:', err);
      }
    });
  }
trackByProductId(index: number, item: any): number {
  return item.product_id;
}

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
