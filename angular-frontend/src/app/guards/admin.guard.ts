import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth'; 

export const adminGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);
  
  // En vez de mirar el usuario, cogemos directamente tu token
  const token = authService.token(); 

  if (token) {
    try {
      // Magia negra: Abrimos la "barriga" del token JWT (el payload)
      const payloadBase64 = token.split('.')[1];
      const payload = JSON.parse(atob(payloadBase64));

      // Comprobamos si la placa de ADMIN está dentro del token
      if (payload.roles && payload.roles.includes('ROLE_ADMIN')) {
        return true; // ¡Acceso concedido al panel VIP!
      }
    } catch (e) {
      console.error('El token tiene un formato raro o está corrupto.');
    }
  }

  // Si no hay token o no tiene la placa de admin, patada a la portada
  router.navigate(['/']);
  return false; 
};