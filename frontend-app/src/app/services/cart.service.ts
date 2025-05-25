// src/app/cart/cart.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface CartItem {
  product_id: number;
  product_title: string;
  product_image1: string;
  product_price: number;
  quantity: number;
}

@Injectable({ providedIn: 'root' })
export class CartService {
  private apiUrl = 'http://localhost:8000/api/cart.php'; 

  constructor(private http: HttpClient) {}

  addToCart(productId: number, quantity = 1): Observable<any> {
  return this.http.post(
    this.apiUrl,
    { product_id: productId, quantity },
    { withCredentials: true }
  );
}

  getCart(): Observable<CartItem[]> {
    return this.http.get<CartItem[]>(this.apiUrl);
  }

  updateCart(qty: Record<number, number>): Observable<any> {
    return this.http.put(this.apiUrl + '?action=update', { qty });
  }

  removeItems(ids: number[]): Observable<any> {
    return this.http.request('delete', this.apiUrl + '?action=remove', { body: { remove: ids } });
  }
}