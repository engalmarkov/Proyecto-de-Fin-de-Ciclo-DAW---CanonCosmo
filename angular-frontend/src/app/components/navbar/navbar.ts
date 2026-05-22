import { Component, HostListener, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { CartService } from '../../services/cart'; 
import { AuthService } from '../../services/auth'; 

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css' 
})
export class NavbarComponent { 
  scrolled = false;
  cartService = inject(CartService);
  authService = inject(AuthService);
  private router = inject(Router);

  @HostListener('window:scroll', [])
  onWindowScroll() {
    this.scrolled = window.scrollY > 10;
  }

  logout() {
    this.authService.logout();
  }

  // MÉTODO DE BÚSQUEDA
  realizarBusqueda(termino: string) {
    if (termino.trim()) {
      // Si hay texto, navegamos al catálogo con la query "buscar"
      this.router.navigate(['/productos'], { queryParams: { buscar: termino.trim() } });
    } else {
      // Si busca en blanco, mostramos el catálogo completo
      this.router.navigate(['/productos']);
    }
  }
}