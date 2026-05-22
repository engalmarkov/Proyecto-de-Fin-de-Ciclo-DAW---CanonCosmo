import { Injectable, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap, switchMap, of } from 'rxjs';
import { Router } from '@angular/router';

export interface User {
  id?: number;
  email: string;
  nombre: string;
  apellidos: string;
  roles?: string[];
}

export interface AuthResponse {
  token: string;
  user?: User; 
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api';
  private http = inject(HttpClient);
  private router = inject(Router);

  currentUser = signal<User | null>(this.getUserFromStorage());
  token = signal<string | null>(localStorage.getItem('frikistore_token'));

  constructor() {}

  private getUserFromStorage(): User | null {
    const user = localStorage.getItem('frikistore_user');
    if (!user || user === 'undefined') return null;
    try {
      return JSON.parse(user);
    } catch {
      return null;
    }
  }

  getUserProfile(freshToken: string): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/perfil`, {
      headers: { 
        'Authorization': `Bearer ${freshToken}`,
        'Accept': 'application/json' 
      }
    }).pipe(
      tap(response => {
        if (response && response.user) {
          localStorage.setItem('frikistore_user', JSON.stringify(response.user));
          this.currentUser.set(response.user);
        }
      })
    );
  }

  login(credentials: any): Observable<any> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials).pipe(
      switchMap(response => {
        // 1. Guardamos el token en memoria
        localStorage.setItem('frikistore_token', response.token);
        this.token.set(response.token);

        // 2. Si el login ya trae los datos directamente, los guardamos
        if (response.user && response.user.nombre) {
          localStorage.setItem('frikistore_user', JSON.stringify(response.user));
          this.currentUser.set(response.user);
          return of(response);
        } 
        
        // 3. Si no, llamamos a la función de perfil corregida
        return this.getUserProfile(response.token);
      })
    );
  }

  register(userData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/registro`, userData);
  }

  logout(): void {
    localStorage.removeItem('frikistore_token');
    localStorage.removeItem('frikistore_user');
    this.token.set(null);
    this.currentUser.set(null);
    this.router.navigate(['/login']);
  }

  isLoggedIn(): boolean {
    return !!this.token();
  }

  getAuthHeaders() {
    return {
      'Authorization': `Bearer ${this.token()}`,
      'Accept': 'application/json'
    };
  }
}