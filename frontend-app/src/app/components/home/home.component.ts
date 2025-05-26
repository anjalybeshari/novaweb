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
  favorites: any[] = [];  // do t'i mbajmë këtu produktet e marra nga DB

  constructor(private cartService: CartService, private api: ApiService) {}

  ngOnInit(): void {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      const user = JSON.parse(storedUser);
      this.userName = user.name;
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
