import { Component, OnInit, inject } from '@angular/core'; // 1. Añadimos OnInit e inject
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http'; // 2. Importamos HttpClient

@Component({
  selector: 'app-calendario-juegos',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="max-w-4xl mx-auto py-12 px-4">
      <h1 class="text-3xl font-black font-orbitron text-white mb-8 uppercase text-center">
        Días de <span class="text-friki-neon">Torneos</span>
      </h1>
      <div class="grid gap-4">
        @for (dia of calendario; track dia.dia) {
          <div class="bg-friki-card border border-slate-800 p-6 rounded-2xl flex justify-between items-center hover:border-friki-neon/50 transition-all">
            <span class="text-xl font-bold text-white font-orbitron uppercase">{{ dia.dia }}</span>
            <span class="text-friki-neon font-black">{{ dia.juegos }}</span>
          </div>
        }
      </div>
    </div>
  `
})
// 3. Implementamos OnInit
export class CalendarioJuegosComponent implements OnInit {
  private http = inject(HttpClient); // Inyectamos el servicio HTTP
  
  calendario: any[] = []; // Lo inicializamos vacío

  ngOnInit() {
    // Aquí es donde lo pones:
    this.http.get('http://localhost:8000/api/calendario').subscribe({
      next: (data: any) => {
        this.calendario = data;
      },
      error: (err) => {
        console.error('Error al cargar el calendario:', err);
      }
    });
  }
}