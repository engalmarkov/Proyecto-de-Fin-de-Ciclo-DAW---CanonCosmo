import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface Categoria {
  id: number;
  nombre: string;
}

export interface Producto {
  id: number;
  nombre: string;
  categoria: Categoria;
  descripcion: string;
  precio: string | number;
  precioOferta?: string | number | null;
  stock: number;
  imagen_url?: string;
}

export interface ApiResponse {
  productos: Producto[];
  total: number;
}

@Injectable({
  providedIn: 'root'
})
export class ProductService {
  // Base URL que ya incluye '/productos'
  private apiUrl = 'http://localhost:8000/api/productos';
  private adminUrl = 'http://localhost:8000/api/admin/productos';

  constructor(private http: HttpClient) { }

  getProducts(categoria?: string, buscar?: string): Observable<ApiResponse> {
    let params = new HttpParams();
    if (categoria && categoria !== 'Todos') {
      params = params.set('categoria', categoria);
    }
    if (buscar) {
      params = params.set('buscar', buscar);
    }
    return this.http.get<ApiResponse>(this.apiUrl, { params });
  }

  getProduct(id: number): Observable<Producto> {
    return this.http.get<Producto>(`${this.apiUrl}/${id}`);
  }

createProduct(data: any) {
    return this.http.post(this.adminUrl, data); // Usa adminUrl
  }

  updateProduct(id: number, data: any) {
    return this.http.put(`${this.adminUrl}/${id}`, data); // Usa adminUrl
  }

  deleteProduct(id: number) {
    return this.http.delete(`${this.adminUrl}/${id}`); // Usa adminUrl
  }
}