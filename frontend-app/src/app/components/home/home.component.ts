// src/app/components/home/home.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../navbar/navbar.component';
import { CartService } from '../../services/cart.service';  // ✅ Importo shërbimin e cart

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  userName: string = '';

  constructor(private cartService: CartService) {}  // ✅ Injekto shërbimin

  ngOnInit(): void {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      const user = JSON.parse(storedUser);
      this.userName = user.name;
    }
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

  favorites = [
  {
    product_id: 1,
    image: 'te_pref1.png',
    title: 'Princess Necklace',
    subtitle: 'Varese',
    price: 19990
  },
  {
    product_id: 2,
    image: 'te_pref3.png',
    title: 'Tennis Bracelet',
    subtitle: 'Byzylyk',
    price: 10990
  },
  {
    product_id: 3,
    image: 'te_pref5.png',
    title: 'Wedding Ring',
    subtitle: 'Unaze',
    price: 10000
  },
  {
    product_id: 4,
    image: 'te_pref2.png',
    title: 'TearDrop Necklace',
    subtitle: 'Varese',
    price: 12990
  },
  {
    product_id: 5,
    image: 'te_pref4.png',
    title: 'SnowFlake Earrings',
    subtitle: 'Vathe',
    price: 8000
  },
  {
    product_id: 6,
    image: 'te_pref6.png',
    title: 'TearDrop Ring',
    subtitle: 'Unaze',
    price: 11500
  }
];

}
