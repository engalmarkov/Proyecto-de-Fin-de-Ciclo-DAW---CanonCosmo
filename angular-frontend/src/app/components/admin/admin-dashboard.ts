import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <div class="p-8 max-w-7xl mx-auto">
      <!-- Cabecera del Centro de Mando -->
      <div class="mb-12 border-b border-slate-800/80 pb-6">
        <h1 class="text-4xl font-black font-orbitron text-white uppercase tracking-widest mb-2">
          PANEL DE <span class="text-friki-neon">ADMINISTRADOR</span>
        </h1>
        <p class="text-slate-400 font-mono text-sm">Bienvenido al panel de administración. ¿Qué vamos a gestionar hoy?</p>
      </div>

      <!-- Cuadrícula de Accesos Rápidos -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- Tarjeta: Inventario (Listado) -->
        <a routerLink="/admin/productos" class="group bg-slate-900/60 border border-slate-800 rounded-2xl p-8 hover:bg-slate-800/80 hover:border-friki-neon transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(0,255,255,0.15)] block relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-friki-neon/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150"></div>
          <i class="fa-solid fa-box-open text-5xl text-slate-600 group-hover:text-friki-neon transition-colors mb-6 drop-shadow-lg"></i>
          <h2 class="text-2xl font-bold text-white uppercase tracking-wide mb-2 group-hover:text-friki-neon transition-colors">Inventario</h2>
          <p class="text-slate-400 text-sm">Ver, editar y eliminar el inventario existente en la base de datos.</p>
        </a>

        <!-- Tarjeta: Forjar Objeto (Crear) -->
        <a routerLink="/admin/productos/nuevo" class="group bg-slate-900/60 border border-slate-800 rounded-2xl p-8 hover:bg-slate-800/80 hover:border-friki-neon transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(0,255,255,0.15)] block relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-friki-neon/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150"></div>
          <i class="fa-solid fa-hammer text-5xl text-slate-600 group-hover:text-friki-neon transition-colors mb-6 drop-shadow-lg"></i>
          <h2 class="text-2xl font-bold text-white uppercase tracking-wide mb-2 group-hover:text-friki-neon transition-colors">Crear Producto</h2>
          <p class="text-slate-400 text-sm">Añadir nuevos objetos y productos directamente al catálogo principal.</p>
        </a>

        <!-- Tarjeta: Categorías (Próximamente / Opcional) -->
        <a routerLink="/admin/calendario" class="group bg-slate-900/60 border border-slate-800 rounded-2xl p-8 hover:bg-slate-800/80 hover:border-purple-500 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(168,85,247,0.15)] block relative overflow-hidden">
          <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150"></div>
          <i class="fa-solid fa-tags text-5xl text-slate-600 group-hover:text-purple-500 transition-colors mb-6 drop-shadow-lg"></i>
          <h2 class="text-2xl font-bold text-white uppercase tracking-wide mb-2 group-hover:text-purple-500 transition-colors">Torneos</h2>
          <p class="text-slate-400 text-sm">Gestiona los torneos y eventos programados.</p>
        </a>

      </div>
    </div>
  `
})
export class AdminDashboardComponent {}