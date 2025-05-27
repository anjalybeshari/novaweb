import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-checkout',
  standalone: true,               // <-- kjo e bën standalone
  imports: [CommonModule],        // <-- duhet për *ngIf dhe direktiva tjera
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.css']
})
export class CheckoutComponent {

  loading: boolean = false;
  errorMessage: string | null = null;

  constructor(private http: HttpClient) {}

  checkout() {
    this.loading = true;
    this.errorMessage = null;

    this.http.post<any>('http://localhost:8000/api/checkout.php', {}, { withCredentials: true })
      .subscribe({
        next: (res) => {
          this.loading = false;
          if (res.approve_link) {
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
}
