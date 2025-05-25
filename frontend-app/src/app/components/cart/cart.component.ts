// src/app/cart/cart.component.ts
import { Component, OnInit } from '@angular/core';
import { CartService, CartItem } from '../../services/cart.service';


@Component({
  selector: 'app-cart',
  templateUrl: './cart.component.html',
  styleUrls: ['./cart.component.css']
})
export class CartComponent implements OnInit {
  cart: CartItem[] = [];
  quantities: { [id: number]: number } = {};
  toRemove: Set<number> = new Set();

  constructor(private cartService: CartService) {}

  ngOnInit() {
    this.loadCart();
  }

  loadCart() {
    this.cartService.getCart().subscribe(items => {
      this.cart = items;
      this.quantities = {};
      items.forEach(i => this.quantities[i.product_id] = i.quantity);
    });
  }

  updateCart() {
    this.cartService.updateCart(this.quantities).subscribe(() => this.loadCart());
  }

  removeSelected() {
    this.cartService.removeItems(Array.from(this.toRemove)).subscribe(() => {
      this.toRemove.clear();
      this.loadCart();
    });
  }

  toggleRemove(id: number, checked: boolean) {
    checked ? this.toRemove.add(id) : this.toRemove.delete(id);
  }

  getSubtotal(item: CartItem) {
    return item.product_price * this.quantities[item.product_id];
  }

  getTotal() {
    return this.cart.reduce((sum, i) => sum + i.product_price * this.quantities[i.product_id], 0);
  }
}