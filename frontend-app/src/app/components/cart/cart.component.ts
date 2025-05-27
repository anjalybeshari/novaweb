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
    this.http
      .post<any>('http://localhost:8000/api/checkout.php', {}, { withCredentials: true })
      .subscribe({
        next: res => {
          alert(`Porosia u bë me sukses! ID: ${res.order_id}`);
          this.loadCart();
          this.loadSummary();
        },
        error: err => {
          alert('Diçka shkoi keq gjatë përpunimit të porosisë.');
          console.error(err);
        }
      });
  }

  /** helper to build a valid image URL, encoding spaces etc */
  getImageUrl(filename: string): string {
    return `/assets/img/${encodeURIComponent(filename)}`;
  }

  /** optional error handler */
  onImgError(event: Event) {
    const img = event.target as HTMLImageElement;
    console.warn('Image load failed:', img.src);
    img.src = 'assets/img/placeholder.png';
  }
}
