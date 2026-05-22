import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ProductService } from '../../services/product';

@Component({
    selector: 'app-admin-productos',
    standalone: true,
    imports: [CommonModule, RouterLink],
    template: `
    <div class="p-8 max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-black font-orbitron text-white uppercase tracking-widest">
          GESTIÓN DE <span class="text-friki-neon">INVENTARIO</span>
        </h1>
        
        <a routerLink="/admin/productos/nuevo" class="inline-block bg-friki-neon text-slate-900 font-bold py-3 px-6 rounded-xl hover:bg-white transition-all transform active:scale-95 shadow-[0_0_15px_rgba(0,255,255,0.4)]">
        <i class="fa-solid fa-plus mr-2"></i> Añadir Objeto
        </a>
      </div>

      <div *ngIf="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-friki-neon"></div>
      </div>

      <div *ngIf="!loading" class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-xs font-black text-slate-500 uppercase tracking-wider">
                <th class="px-6 py-4">ID</th>
                <th class="px-6 py-4">Nombre del Objeto</th>
                <th class="px-6 py-4">Precio</th>
                <th class="px-6 py-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50 text-slate-300">
              <tr *ngFor="let prod of productos" class="hover:bg-slate-800/50 transition-colors">
                <td class="px-6 py-4 font-mono text-friki-neon font-bold">#{{ prod.id }}</td>
                <td class="px-6 py-4 font-bold text-white">{{ prod.nombre }}</td>
                <td class="px-6 py-4 text-green-400 font-mono">{{ prod.precio }} €</td>
                <td class="px-6 py-4 text-right space-x-3">
                <a [routerLink]="['/admin/productos/editar', prod.id]" class="text-slate-400 hover:text-friki-neon transition-colors p-2 inline-block">
                <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <button (click)="eliminarProducto(prod.id)" class="text-slate-400 hover:text-red-500 transition-colors p-2 transform active:scale-90">
                <i class="fa-solid fa-trash"></i>
                </button>
                </td>
              </tr>
              
              <tr *ngIf="productos.length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-slate-500 font-bold">
                  La base de datos está vacía. ¡Añade algo de loot!
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  `
})
export class AdminProductosComponent implements OnInit {
    private productService = inject(ProductService);

    productos: any[] = [];
    loading = true;

    ngOnInit() {
        this.cargarProductos();
    }

    cargarProductos() {
        this.productService.getProducts().subscribe({
            next: (data: any) => {
                this.productos = data.productos;

                this.loading = false;
            },
            error: (err) => {
                console.error('Error al cargar el inventario:', err);
                this.loading = false;
            }
        });
    }

    eliminarProducto(id: number) {
        // Pedimos confirmación antes de lanzar el misil
        const confirmado = confirm('¿Estás seguro de que quieres desintegrar este objeto del inventario? Esta acción no se puede deshacer.');

        if (confirmado) {
            this.productService.deleteProduct(id).subscribe({
                next: () => {
                    // Magia visual: filtramos la tabla para quitar el producto borrado al instante
                    // sin necesidad de recargar la página entera
                    this.productos = this.productos.filter(p => p.id !== id);
                },
                error: (err) => {
                    console.error('Error al intentar destruir el objeto:', err);
                    alert('Hubo un fallo en la Matrix y no se pudo borrar.');
                }
            });
        }
    }
}