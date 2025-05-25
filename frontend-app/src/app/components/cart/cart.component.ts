import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
  cartItems: any[] = [];
  itemCount: number = 0;
  totalPrice: number = 0;

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadCart();
    this.loadSummary();
  }

  loadCart(): void {
    this.http.get<any[]>('http://localhost:8000/api/cart.php', { withCredentials: true })
      .subscribe(res => this.cartItems = res);
  }

  loadSummary(): void {
    this.http.get<any>('http://localhost:8000/api/cart_summary.php', { withCredentials: true })
      .subscribe(summary => {
        this.itemCount = summary.itemCount;
        this.totalPrice = summary.totalPrice;
      });
  }
}
