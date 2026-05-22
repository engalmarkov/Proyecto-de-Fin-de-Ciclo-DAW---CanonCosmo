import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-admin-calendario',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  template: `
    <div class="max-w-5xl mx-auto px-4 py-12">
      <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-black font-orbitron text-white uppercase tracking-widest">
          Panel Admin: <span class="text-friki-neon">Calendario</span>
        </h1>
        <a routerLink="/admin" class="text-slate-400 hover:text-white transition-colors text-sm underline">
          Volver al Panel
        </a>
      </div>

      <div class="bg-friki-card border border-slate-800 rounded-3xl p-8 space-y-6">
        <p class="text-slate-400 text-sm mb-4">
          Modifica los juegos asignados a cada día de la semana. Si marcas la casilla <span class="text-red-500 font-bold">Festivo</span>, el texto se cambiará automáticamente para avisar a los clientes.
        </p>

        @if (loading) {
          <div class="flex justify-center py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-friki-neon"></div>
          </div>
        }

        @if (!loading) {
          <div class="space-y-4">
            @for (item of dias; track item.id) {
              <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-slate-900/40 p-4 rounded-2xl border border-slate-800 hover:border-slate-700 transition-all">
                
                <div class="md:col-span-2">
                  <span class="font-black font-orbitron text-white text-lg uppercase tracking-wider">
                    {{ item.dia }}
                  </span>
                </div>

                <div class="md:col-span-6">
                  <input type="text" [(ngModel)]="item.juegos" 
                         [disabled]="item.esFestivo"
                         class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white font-medium focus:outline-none focus:border-friki-neon transition-colors disabled:opacity-30 disabled:cursor-not-allowed text-sm"
                         placeholder="Ej: Torneo de Digimon TCG">
                </div>

                <div class="md:col-span-2 flex items-center justify-start md:justify-center">
                  <label class="relative flex items-center cursor-pointer">
                    <input type="checkbox" [(ngModel)]="item.esFestivo" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600 peer-checked:after:bg-white"></div>
                    <span class="ms-3 text-xs font-black uppercase text-slate-400 peer-checked:text-red-500">Festivo</span>
                  </label>
                </div>

                <div class="md:col-span-2 text-right">
                  <button (click)="guardarDia(item)"
                          class="w-full md:w-auto bg-friki-neon text-friki-dark font-black px-5 py-2.5 rounded-xl hover:bg-white transition-all text-xs uppercase tracking-wider shadow-md shadow-friki-neon/10">
                    Guardar
                  </button>
                </div>

              </div>
            }
          </div>
        }
      </div>
    </div>
  `
})
export class AdminCalendarioComponent implements OnInit {
  private http = inject(HttpClient);
  private authService = inject(AuthService); // Usamos tu servicio para el Token JWT
  
  dias: any[] = [];
  loading = true;

  ngOnInit() {
    this.cargarCalendario();
  }

  // Descarga los días actuales para rellenar los inputs
  cargarCalendario() {
    this.http.get<any[]>('http://localhost:8000/api/calendario').subscribe({
      next: (data) => {
        this.dias = data;
        this.loading = false;
      },
      error: (err) => {
        console.error('Error al cargar el panel de calendario:', err);
        this.loading = false;
      }
    });
  }

  // Envía los cambios de un día concreto a Symfony usando PATCH
  guardarDia(item: any) {
    // Si el administrador marca festivo, autocompletamos el texto de aviso
    const textoJuegos = item.esFestivo ? '¡DÍA FESTIVO / CERRADO!' : item.juegos;

    const payload = {
      juegos: textoJuegos,
      esFestivo: item.esFestivo
    };

    // Es obligatorio mandar el token JWT del admin para que Symfony acepte el cambio
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${this.authService.token()}` // Ajusta 'token()' según cómo se llame en tu AuthService
    });

    this.http.patch(`http://localhost:8000/api/calendario/${item.id}`, payload, { headers }).subscribe({
      next: () => {
        alert(`¡El ${item.dia} se ha actualizado correctamente en la base de datos!`);
        if (item.esFestivo) {
          item.juegos = '¡DÍA FESTIVO / CERRADO!';
        }
      },
      error: (err) => {
        alert('Error al guardar los cambios en el servidor.');
        console.error(err);
      }
    });
  }
}