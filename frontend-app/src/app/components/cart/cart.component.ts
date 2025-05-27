// cart.component.ts
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
  itemCount = 0;
  totalPrice = 0;

  loading: boolean = false;              // <--- Shto këtë
  errorMessage: string | null = null;    // <--- Dhe këtë

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadCart();
    this.loadSummary();
  }

  loadCart(): void {
    this.http
      .get<any[]>('http://localhost:8000/api/cart.php', { withCredentials: true })
      .subscribe(res => (this.cartItems = res));
  }

  loadSummary(): void {
    this.http
      .get<any>('http://localhost:8000/api/cart_summary.php', { withCredentials: true })
      .subscribe(summary => {
        this.itemCount = summary.itemCount;
        this.totalPrice = summary.totalPrice;
      });
  }

  checkout() {
  this.loading = true;
  this.errorMessage = null;

  this.http.post<any>('http://localhost:8000/api/checkout.php', {}, { withCredentials: true })
    .subscribe({
      next: (res) => {
        this.loading = false;
        if (res.approve_link) {
          // Ridrejto te PayPal për pagesë
          window.location.href = res.approve_link;
        } else {
          this.errorMessage = 'Nuk u mor linku i pagesës PayPal.';
        }
      },
      error: (err) => {
        this.loading = false;
        this.errorMessage = err.error?.error || 'Gabim gjatë checkout.';
      }
    });
}

  getImageUrl(filename: string): string {
    return `/assets/img/${encodeURIComponent(filename)}`;
  } 



}
