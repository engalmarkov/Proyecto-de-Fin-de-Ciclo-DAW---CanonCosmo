import { Injectable, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Producto } from './product';

export interface CartItem {
  product: Producto;
  quantity: number;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private http = inject(HttpClient);

  private items = signal<CartItem[]>(this.loadCart());

  cartItems = this.items.asReadonly();

  constructor() {}

  private loadCart(): CartItem[] {
    const saved = localStorage.getItem('frikistore_cart');
    return saved ? JSON.parse(saved) : [];
  }

  private saveCart(items: CartItem[]): void {
    localStorage.setItem('frikistore_cart', JSON.stringify(items));
    this.items.set(items);
  }

  addToCart(product: Producto, quantity: number = 1): void {
    const currentItems = [...this.items()];
    const index = currentItems.findIndex(i => i.product.id === product.id);

    if (index > -1) {
      currentItems[index].quantity += quantity;
    } else {
      currentItems.push({ product, quantity });
    }
    this.saveCart(currentItems);
  }

  removeFromCart(productId: number): void {
    const filtered = this.items().filter(i => i.product.id !== productId);
    this.saveCart(filtered);
  }

  clearCart(): void {
    this.saveCart([]);
  }

  getTotal(): number {
    return this.items().reduce((acc, item) => acc + (Number(item.product.precio) * item.quantity), 0);
  }

  getCount(): number {
    return this.items().length;
  }

  finalizarPedido(): Observable<any> {
    const payload = {
      items: this.items().map(item => ({
        producto_id: item.product.id,
        cantidad: item.quantity
      }))
    };

    return this.http.post('http://localhost:8000/api/pedidos', payload);
  }
}