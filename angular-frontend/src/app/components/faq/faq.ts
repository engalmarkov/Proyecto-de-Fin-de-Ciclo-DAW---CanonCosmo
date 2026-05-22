import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-faq',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="p-8 max-w-3xl mx-auto">
      <h1 class="text-4xl font-black font-orbitron text-white uppercase tracking-widest mb-10 text-center">
        ¿Tienes dudas? Tenemos <span class="text-friki-neon">respuestas</span>
      </h1>

      <div class="space-y-4">
        @for (item of faqItems; track item.id) {
          <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
            <button 
              (click)="toggle(item.id)"
              class="w-full flex justify-between items-center p-6 text-left hover:bg-slate-800/50 transition-all">
              <span class="text-white font-bold font-orbitron">{{ item.pregunta }}</span>
              <i class="fa-solid fa-chevron-down text-friki-neon transition-transform" [class.rotate-180]="item.abierto"></i>
            </button>
            
            @if (item.abierto) {
              <div class="px-6 pb-6 text-slate-400 text-sm leading-relaxed animate-in fade-in slide-in-from-top-2">
                {{ item.respuesta }}
              </div>
            }
          </div>
        }
      </div>
    </div>
  `
})
export class FaqComponent {
  faqItems = [
    { id: 1, pregunta: '¿Cuánto tardará en llegar mi pedido?', respuesta: 'Los envíos estándar tardan entre 48 y 72 horas laborables en llegar a la península.', abierto: false },
    { id: 2, pregunta: '¿Es seguro pagar con tarjeta?', respuesta: 'Absolutamente. Utilizamos encriptación SSL de nivel bancario y nunca almacenamos tus datos financieros.', abierto: false },
    { id: 3, pregunta: '¿Puedo cambiar mi dirección de envío?', respuesta: 'Si el pedido aún no ha salido de nuestro almacén, escríbenos a soporte lo antes posible y lo cambiaremos encantados, o bien llámanos a 922 123 456.', abierto: false },
    { id: 4, pregunta: '¿Tenéis tienda física?', respuesta: 'Si quieres visitarnos, ¡estamos en Tenerife!, nuestro local está en Calle General Serrano, 79.', abierto: false }
  ];

  toggle(id: number) {
    this.faqItems = this.faqItems.map(item => ({
      ...item,
      abierto: item.id === id ? !item.abierto : false
    }));
  }
}