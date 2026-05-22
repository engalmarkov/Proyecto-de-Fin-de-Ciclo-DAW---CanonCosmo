import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth'; 

export const adminGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);
  
  const token = authService.token(); 

  if (token) {
    try {
      const payloadBase64 = token.split('.')[1];
      const payload = JSON.parse(atob(payloadBase64));

      if (payload.roles && payload.roles.includes('ROLE_ADMIN')) {
        return true; 
      }
    } catch (e) {
      console.error('El token tiene un formato raro o está corrupto.');
    }
  }

  // Si no hay token o no tiene la placa de admin, redireccionamos a la página principal
  router.navigate(['/']);
  return false; 
};