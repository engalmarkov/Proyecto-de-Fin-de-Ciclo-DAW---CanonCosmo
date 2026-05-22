import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { CartService } from '../../services/cart';
import { OrderService } from '../../services/order';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './cart.html',
  styleUrls: ['./cart.css']
})
export class CartComponent {
  public cartService = inject(CartService);
  public orderService = inject(OrderService);
  public authService = inject(AuthService);
  private router = inject(Router);

  loading = false;

  parseNumber(value: string | number): number {
    return Number(value);
  }

  removeItem(id: number) {
    this.cartService.removeFromCart(id);
  }

  checkout() {
    if (!this.authService.isLoggedIn()) {
      this.router.navigate(['/login']);
      return;
    }

    this.loading = true;

    this.cartService.finalizarPedido().subscribe({
      next: (res) => {
        this.cartService.clearCart();
        this.loading = false;
        this.router.navigate(['/pedido-exito'], { state: { order: res } });
      },
      error: (err) => {
        this.loading = false;
        const mensajeReal = err.error?.error || 'Error al procesar el pedido.';
        alert(mensajeReal);
        console.error('Error del backend:', err);
      }
    });
  }
}