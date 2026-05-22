import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth';
import { OrderService } from '../../services/order';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './profile.html',
  styleUrls: ['./profile.css']
})
export class ProfileComponent implements OnInit {
  authService = inject(AuthService);
  orderService = inject(OrderService);
  
  orders: any[] = [];
  loading = true;

  ngOnInit() {
    this.loadOrders();
  }

  loadOrders() {
    this.orderService.getUserOrders().subscribe({
      next: (data) => {
        this.orders = data;
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  logout() {
    this.authService.logout();
  }

  // Método para comprobar si el usuario es admin, que usaremos para mostrar/ocultar el enlace al panel de admin en el perfil
  isAdmin(): boolean {
    const token = this.authService.token();
    if (!token) return false;
    
    try {
      const payloadBase64 = token.split('.')[1];
      const payload = JSON.parse(atob(payloadBase64));
      return payload.roles && payload.roles.includes('ROLE_ADMIN');
    } catch (e) {
      return false;
    }
  }
}