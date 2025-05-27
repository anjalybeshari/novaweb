// src/app/cart/cart.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, Observable, tap } from 'rxjs';

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
 private _count$ = new BehaviorSubject<number>(0);
  public readonly count$: Observable<number> = this._count$.asObservable();
  constructor(private http: HttpClient) {}

addToCart(productId: number): Observable<any> {
  return this.http.post(
    this.apiUrl,
    { product_id: productId },       // ← snake_case!
    { withCredentials: true }
  ).pipe(
    tap(() => this._count$.next(this._count$.value + 1))
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