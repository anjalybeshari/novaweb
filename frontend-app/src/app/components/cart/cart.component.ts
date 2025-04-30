// src/app/components/cart/cart.component.ts
import { Component, OnInit }    from '@angular/core';
import { CommonModule }          from '@angular/common';
import { ApiService }            from '../../services/api.service';
import { NavbarComponent }       from '../navbar/navbar.component';  // ← import it

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [
    CommonModule,
    NavbarComponent               // ← add it here
  ],
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
  items: any[] = [];
  total = 0;

  constructor(private api: ApiService) {}

  ngOnInit() {
    this.loadCart();
  }

  loadCart() {
    this.api.getCart().subscribe(res => {
      this.items = res.items;
      this.total = res.total;
    });
  }

  remove(pid: number) {
    this.api.removeFromCart(pid).subscribe(() => this.loadCart());
  }
}
