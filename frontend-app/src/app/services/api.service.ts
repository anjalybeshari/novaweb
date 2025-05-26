// frontend-app/src/app/services/api.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private base = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

getProducts(category?: number, brand?: number, limit?: number): Observable<any[]> {
  let params = new HttpParams();
  if (category) params = params.set('category', category.toString());
  if (brand)    params = params.set('brand', brand.toString());
  if (limit)    params = params.set('limit', limit.toString());

  return this.http.get<any[]>(`${this.base}/products.php`, { params, withCredentials: true });
}


  getCategories(): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/categories.php`, { withCredentials: true });
  }

  getBrands(): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/brands.php`, { withCredentials: true });
  }

  getCart(): Observable<{items:any[], total:number}> {
    return this.http.get<any>(`${this.base}/cart.php`, { withCredentials: true });
  }

  addToCart(pid: number, qty = 1): Observable<any> {
    return this.http.post(`${this.base}/cart.php`, { product_id: pid, quantity: qty }, { withCredentials: true });
  }

  removeFromCart(pid: number): Observable<any> {
    return this.http.delete(`${this.base}/cart.php`, { params: { product_id: pid }, withCredentials: true });
  }

  search(q: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/search.php`, { params: { q }, withCredentials: true });
  }
}
